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
            'mensagem' => 'required|string',
        ]);
        Comunicado::create([
            'assunto'              => $request->input('assunto'),
            'mensagem'             => $request->input('mensagem'),
            'data_envio'           => now(),
            'status'               => Comunicado::STATUS_PENDENTE,
            'fk_comunicado_id_usuario' => Auth::id() 
        ]);
        return redirect()->route('admin.gerenciar')->with('successo', 'Comunicado salvo e logo será enviado!');
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
        $destinatarios = User::where('receber_avisos', true)->get();

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
