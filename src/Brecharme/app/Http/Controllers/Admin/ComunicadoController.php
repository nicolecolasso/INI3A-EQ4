<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comunicado;
use App\Models\User;
use App\Jobs\EnviarWhatsAppJob;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;

class ComunicadoController extends Controller
{

   public function novoComunicado()
    {
        return view('admin.comunicados.novoComunicado');
    }

    public function salvar(Request $request)
    {
        $request->validate([
            'assunto'  => 'required|string|max:255',
            'mensagem' => 'required|string|max:1000',
        ]);

        // Grava o comunicado no histórico (log de quem disparou)
        Comunicado::create([
            'assunto'                  => $request->assunto,
            'mensagem'                 => $request->mensagem,
            'data_envio'               => now(),
            'status'                   => Comunicado::STATUS_PENDENTE,
            'fk_comunicado_id_usuario' => Auth::id(),
        ]);

        // Busca os destinatários elegíveis
        $destinatarios = User::where('receber_avisos', true)
            ->where('excluido', false)
            ->whereNotNull('telefone')
            ->get();

        $mensagemCompleta = "*{$request->assunto}*\n\n{$request->mensagem}";

        $delayEmSegundos = 0;
        foreach ($destinatarios as $contato) {
            EnviarWhatsAppJob::dispatch($contato->telefone, $mensagemCompleta)
                ->delay(now()->addSeconds($delayEmSegundos));
            $delayEmSegundos += 8;
        }

        return redirect()->route('admin.gerenciar')->with('sucesso', 'Comunicado salvo e está sendo enviado!');
    }

    public function reenviarComunicado()
    {
        $comunicadosAntigos = Comunicado::orderBy('data_envio', 'desc')->get();
        
        return view('admin.comunicados.reenviarComunicado', compact('comunicadosAntigos'));
    }


    public function dispararComunicado(Request $request)
    {
        $request->validate([
            'mensagem' => 'required|string|max:1000'
        ]);

        // Busca os contatos que aceitaram receber comunicados
        $destinatarios = User::where('receber_avisos', true)
            ->where('excluido', false)
            ->whereNotNull('telefone')
            ->get();

        $delayEmSegundos = 0;

        foreach ($destinatarios as $contato) {
            // Envia cada mensagem para a fila com um intervalo de segurança
            EnviarWhatsAppJob::dispatch($contato->telefone, $request->mensagem)
                ->delay(now()->addSeconds($delayEmSegundos));

            // Adiciona 8 segundos de pausa entre cada envio para evitar ban do WhatsApp!
            $delayEmSegundos += 8;
        }

        return back()->with('sucesso', 'Comunicado em processamento! As mensagens serão disparadas aos poucos.');
    }
}
