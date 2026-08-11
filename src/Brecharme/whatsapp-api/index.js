const { Client, LocalAuth } = require('whatsapp-web.js');
const qrcode = require('qrcode-terminal');
const express = require('express');

const app = express();
app.use(express.json());

// Instancia o cliente do WhatsApp mantendo a sessão salva na pasta .wwebjs_auth
const client = new Client({
    authStrategy: new LocalAuth(),
    puppeteer: {
        args: ['--no-sandbox', '--disable-setuid-sandbox'] // Necessário para rodar em servidores Linux
    }
});

// Exibe o QR Code no terminal quando a API for iniciada
client.on('qr', (qr) => {
    console.log('=== ESCANEIE O QR CODE ABAIXO NO SEU WHATSAPP ===');
    qrcode.generate(qr, { small: true });
});

// Avisa quando o WhatsApp estiver conectado e pronto
client.on('ready', () => {
    console.log('✅ Cliente do WhatsApp conectado e pronto para enviar mensagens!');
});

// Rota HTTP POST que o Laravel vai chamar para disparar mensagens
app.post('/send-message', async (req, res) => {
    const { phone, message } = req.body;

    if (!phone || !message) {
        return res.status(400).json({ status: 'error', message: 'Telefone e mensagem são obrigatórios.' });
    }

    try {
        // Formata o número para o padrão internacional do WhatsApp (ex: 5514999999999@c.us)
        const formattedPhone = phone.replace(/\D/g, ''); // Remove caracteres não numéricos
        const chatId = `${formattedPhone.startsWith('55') ? formattedPhone : '55' + formattedPhone}@c.us`;

        await client.sendMessage(chatId, message);
        console.log(`✉️ Mensagem enviada com sucesso para: ${formattedPhone}`);
        
        return res.json({ status: 'success', message: 'Mensagem enviada com sucesso!' });
    } catch (error) {
        console.error('❌ Erro ao enviar mensagem:', error);
        return res.status(500).json({ status: 'error', error: error.message });
    }
});

// Inicializa o cliente do WhatsApp para gerar o QR Code
client.initialize().catch((error) => {
    console.error('❌ Falha ao inicializar o cliente do WhatsApp:', error);
});

// Inicia o servidor HTTP na porta 3000
const PORT = 3000;
app.listen(PORT, () => {
    console.log(`🚀 Servidor da API do WhatsApp rodando na porta ${PORT}`);
});