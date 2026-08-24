<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EnviarWhatsAppJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $telefone;
    protected string $mensagem;

    /**
     * Passa os dados necessários ao criar a Job.
     */
    public function __construct(string $telefone, string $mensagem)
    {
        $this->telefone = $telefone;
        $this->mensagem = $mensagem;
    }

    /**
     * Executa a tarefa de envio em segundo plano.
     */
    public function handle(): void
    {
        $apiUrl = config('services.whatsapp.url', 'http://127.0.0.1:56828/send-message');

        echo "\n---> Tentando enviar para: " . $apiUrl . "\n";

        try {
            $response = Http::withoutVerifying()->post($apiUrl, [
                'phone' => $this->telefone,
                'message' => $this->mensagem,
            ]);

            echo "---> Resposta do Node (Status " . $response->status() . "): " . $response->body() . "\n";

        } catch (\Exception $e) {
            echo "---> ERRO DE CONEXÃO PHP -> NODE: " . $e->getMessage() . "\n";
        }
    }
}