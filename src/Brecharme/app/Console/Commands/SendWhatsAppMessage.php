<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Laravel\Dusk\Browser;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;

class SendWhatsAppMessage extends Command
{
    // O comando poderá ser testado no terminal como: php artisan whatsapp:send 5511999999999 "Mensagem aqui"
    protected $signature = 'whatsapp:send {number} {message}';
    protected $description = 'Envia comunicados automatizados via WhatsApp Web';

    public function handle()
    {
        $number = $this->argument('number');
        $message = $this->argument('message');

        // Configuração para reaproveitar perfil do Chrome (mude para a pasta real do seu PC)
        $options = (new ChromeOptions)->addArguments([
            '--user-data-dir=C:\Users\SEU_USUARIO\AppData\Local\Google\Chrome\User Data', 
            '--profile-directory=Default',
            '--no-sandbox',
            '--disable-dev-shm-usage'
        ]);

        $capabilities = DesiredCapabilities::chrome()->setCapability(ChromeOptions::CAPABILITY, $options);
        
        // Conecta ao ChromeDriver rodando na sua máquina na porta padrão
        $driver = RemoteWebDriver::create('http://localhost:9515', $capabilities);
        $browser = new Browser($driver);

        // 🔄 CORREÇÃO DA URL: API oficial de redirecionamento para o WhatsApp Web
        $url = "https://web.whatsapp.com/send?phone=" . $number . "&text=" . urlencode($message);

        try {
            $browser->visit($url)
                ->waitFor('.copyable-area', 30) // Espera até 30 segundos a área de digitação carregar
                ->keys('body', '{enter}');     // Simula o clique na tecla ENTER

            // Dá 2 segundos para a mensagem subir no servidor do WhatsApp antes de fechar o Chrome
            sleep(2); 

            $this->info("Mensagem enviada com sucesso para {$number}!");
        } catch (\Exception $e) {
            $this->error("Erro ao enviar: " . $e->getMessage());
        } finally {
            $driver->quit(); // Fecha o navegador automatizado com segurança
        }
    }
}