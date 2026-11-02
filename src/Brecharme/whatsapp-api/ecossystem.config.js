module.exports = {
    apps: [
        {
            name: 'whatsapp-api',
            script: 'index.js',
            instances: 1,
            exec_mode: 'fork',
            autorestart: true,
            // espera crescente entre reinícios (evita loop frenético se algo estiver quebrado)
            exp_backoff_restart_delay: 5000,
            // dá tempo do index.js fechar o Chrome antes de o pm2 matar o processo
            kill_timeout: 15000,
            time: true
            // Opcional: reinício diário às 4h para limpar vazamento de memória do Chrome.
            // Descomente se notar a API ficando lenta com o tempo.
            // cron_restart: '0 4 * * *'
        }
    ]
};