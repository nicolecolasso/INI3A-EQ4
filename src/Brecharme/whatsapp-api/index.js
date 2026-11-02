const { Client, LocalAuth } = require('whatsapp-web.js');
const qrcode = require('qrcode-terminal');
const express = require('express');

const PORT = 56828;
const INIT_TIMEOUT_MS = 3 * 60 * 1000;   // tempo máximo para ficar "ready" (sem QR na tela)
const WATCHDOG_INTERVAL_MS = 60 * 1000;  // checagem periódica da conexão
const MAX_FAILURES = 3;                  // falhas seguidas antes de reiniciar

const app = express();
app.use(express.json());

let isReady = false;
let qrShown = false;
let watchdogFailures = 0;
let sendFailures = 0;
let shuttingDown = false;

const client = new Client({
    authStrategy: new LocalAuth(),
    puppeteer: {
        headless: true,
        args: [
            '--no-sandbox',
            '--disable-setuid-sandbox',
            '--disable-dev-shm-usage',
            '--disable-gpu'
        ]
    }
});

// Fecha o Chrome direito e sai com erro; o pm2 sobe o processo de novo.
async function shutdown(reason) {
    if (shuttingDown) return;
    shuttingDown = true;
    isReady = false;
    console.error(`Encerrando (${reason}) para o pm2 reiniciar...`);
    try {
        await Promise.race([
            client.destroy(),
            new Promise((resolve) => setTimeout(resolve, 10000))
        ]);
    } catch (e) {
        // ignora: vamos sair de qualquer jeito
    }
    process.exit(1);
}

client.on('qr', (qr) => {
    qrShown = true;
    console.log('=== ESCANEIE O QR CODE ABAIXO NO SEU WHATSAPP ===');
    qrcode.generate(qr, { small: true });
});

client.on('authenticated', () => {
    qrShown = false;
    console.log('WhatsApp autenticado.');
});

client.on('auth_failure', (msg) => {
    shutdown(`falha de autenticação: ${msg}`);
});

client.on('ready', () => {
    isReady = true;
    qrShown = false;
    watchdogFailures = 0;
    sendFailures = 0;
    console.log('Cliente do WhatsApp conectado e pronto para enviar mensagens!');
});

client.on('disconnected', (reason) => {
    shutdown(`desconectado: ${reason}`);
});

// Se não ficar pronto a tempo (e não estiver esperando leitura de QR), reinicia.
setTimeout(() => {
    if (!isReady && !qrShown) {
        shutdown('timeout de inicialização');
    }
}, INIT_TIMEOUT_MS);

// Vigia a conexão: se o WhatsApp parar de responder, reinicia sozinho.
setInterval(async () => {
    if (!isReady || shuttingDown) return;
    try {
        const state = await Promise.race([
            client.getState(),
            new Promise((_, reject) => setTimeout(() => reject(new Error('getState timeout')), 30000))
        ]);
        if (state === 'CONNECTED') {
            watchdogFailures = 0;
        } else {
            watchdogFailures++;
            console.warn(`Estado inesperado do WhatsApp: ${state} (${watchdogFailures}/${MAX_FAILURES})`);
        }
    } catch (error) {
        watchdogFailures++;
        console.warn(`Falha ao checar estado (${watchdogFailures}/${MAX_FAILURES}): ${error.message}`);
    }
    if (watchdogFailures >= MAX_FAILURES) {
        shutdown('watchdog: WhatsApp sem resposta');
    }
}, WATCHDOG_INTERVAL_MS);

// Erros típicos de Chrome/página quebrada
const FATAL_PATTERNS = [
    'Execution context was destroyed',
    'Target closed',
    'Session closed',
    'Protocol error',
    'sendIq called before startComms'
];
const isFatalError = (message = '') => FATAL_PATTERNS.some((p) => message.includes(p));

process.on('unhandledRejection', (reason) => {
    const message = (reason && reason.message) || String(reason);
    console.error('Promessa rejeitada sem tratamento:', message);
    if (isFatalError(message)) shutdown(`erro fatal: ${message}`);
});

// Rota para checar se a API está pronta (útil para testes e monitoramento)
app.get('/health', (req, res) => {
    res.status(isReady ? 200 : 503).json({ status: isReady ? 'ready' : 'not_ready' });
});

// Rota HTTP POST que o Laravel chama para disparar mensagens
app.post('/send-message', async (req, res) => {
    const { phone, message } = req.body;

    if (!phone || !message) {
        return res.status(400).json({ status: 'error', message: 'Telefone e mensagem são obrigatórios.' });
    }

    if (!isReady) {
        return res.status(503).json({ status: 'error', message: 'WhatsApp ainda não está pronto. Tente novamente em instantes.' });
    }

    try {
        const digits = phone.replace(/\D/g, '');

        let formattedPhone;
        if (digits.length === 12 || digits.length === 13) {
            formattedPhone = digits; // já tem 55 + DDD + número
        } else if (digits.length === 10 || digits.length === 11) {
            formattedPhone = '55' + digits; // DDD + número, falta o 55
        } else {
            console.warn(`Número com formato inesperado, pulando envio: ${phone}`);
            return res.status(400).json({ status: 'error', message: 'Número de telefone em formato inválido.' });
        }

        const numberDetails = await client.getNumberId(formattedPhone);
        if (!numberDetails) {
            return res.status(400).json({ status: 'error', message: 'Este número não está registrado no WhatsApp.' });
        }

        await client.sendMessage(numberDetails._serialized, message);
        sendFailures = 0;
        console.log(`Mensagem enviada com sucesso para: ${formattedPhone}`);

        return res.json({ status: 'success', message: 'Mensagem enviada com sucesso!' });
    } catch (error) {
        console.error('Erro ao enviar mensagem:', error);
        if (isFatalError(error.message)) {
            sendFailures++;
            if (sendFailures >= MAX_FAILURES) {
                shutdown('falhas seguidas ao enviar mensagens');
            }
        }
        return res.status(500).json({ status: 'error', error: error.message });
    }
});

client.initialize().catch((error) => {
    console.error('Falha ao inicializar o cliente do WhatsApp:', error);
    shutdown('falha ao inicializar');
});

app.listen(PORT, '0.0.0.0', () => {
    console.log(`Servidor da API do WhatsApp rodando na porta ${PORT}`);
});