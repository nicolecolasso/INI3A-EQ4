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
        // IP ou URL da sua API de WhatsApp que está rodando no Node/PM2
        $apiUrl = config('services.whatsapp.url', 'http://127.0.0.1:3000/send-message');

        try {
            $response = Http::post($apiUrl, [
                'phone' => $this->telefone,
                'message' => $this->mensagem,
            ]);

            if ($response->successful()) {
                Log::info("WhatsApp enviado com sucesso para: {$this->telefone}");
            } else {
                Log::error("Falha ao enviar WhatsApp para {$this->telefone}: " . $response->body());
            }
        } catch (\Exception $e) {
            Log::error("Erro na requisição para API de WhatsApp: " . $e->getMessage());
        }
    }
}