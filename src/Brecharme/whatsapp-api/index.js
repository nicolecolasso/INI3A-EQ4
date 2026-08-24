const { Client, LocalAuth } = require('whatsapp-web.js');
const qrcode = require('qrcode-terminal');
const express = require('express');

const app = express();
app.use(express.json());

// Instancia o cliente do WhatsApp mantendo a sessão salva na pasta .wwebjs_auth
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

// Exibe o QR Code no terminal quando a API for iniciada
client.on('qr', (qr) => {
    console.log('=== ESCANEIE O QR CODE ABAIXO NO SEU WHATSAPP ===');
    qrcode.generate(qr, { small: true });
});

// Avisa quando o WhatsApp estiver conectado e pronto
client.on('ready', () => {
    console.log('Cliente do WhatsApp conectado e pronto para enviar mensagens!');
});

// Rota HTTP POST que o Laravel vai chamar para disparar mensagens
app.post('/send-message', async (req, res) => {
    const { phone, message } = req.body;

    if (!phone || !message) {
        return res.status(400).json({ status: 'error', message: 'Telefone e mensagem são obrigatórios.' });
    }

    try {
        // Formata o número para o padrão internacional do WhatsApp
        const digits = phone.replace(/\D/g, ''); // Remove tudo que não for número

        let formattedPhone;
        if (digits.length === 12 || digits.length === 13) {
            // Já tem código do país (55 + DDD + número)
            formattedPhone = digits;
        } else if (digits.length === 10 || digits.length === 11) {
            // Só tem DDD + número, falta o código do país
            formattedPhone = '55' + digits;
        } else {
            console.warn(`Número com formato inesperado, pulando envio: ${phone}`);
            return res.status(400).json({ status: 'error', message: 'Número de telefone em formato inválido.' });
        }

        // Verifica se o número realmente existe no WhatsApp antes de enviar
        const numberDetails = await client.getNumberId(formattedPhone);
        if (!numberDetails) {
            return res.status(400).json({ status: 'error', message: 'Este número não está registrado no WhatsApp.' });
        }

        await client.sendMessage(numberDetails._serialized, message);
        console.log(`Mensagem enviada com sucesso para: ${formattedPhone}`);

        return res.json({ status: 'success', message: 'Mensagem enviada com sucesso!' });
    } catch (error) {
        console.error('Erro ao enviar mensagem:', error);
        return res.status(500).json({ status: 'error', error: error.message });
    }
});

// Inicializa o cliente do WhatsApp para gerar o QR Code
client.initialize().catch((error) => {
    console.error('Falha ao inicializar o cliente do WhatsApp:', error);
});

const PORT = 56828;
app.listen(PORT, '0.0.0.0', () => {
    console.log(`Servidor da API do WhatsApp rodando na porta ${PORT}`);
});
