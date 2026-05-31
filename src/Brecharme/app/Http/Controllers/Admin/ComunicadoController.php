<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comunicado;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;

class ComunicadoController extends Controller
{
    public function comunicados()
    {
        return view('admin.comunicados.comunicados');
    }

   public function novoComunicado()
    {
        return view('admin.comunicados.novoComunicado');
    }

    public function salvar(Request $request)
    {
        // 1. Salva o registro do comunicado na tabela do banco para o histórico do TCC
        $comunicado = Comunicado::create([
            'assunto'              => $request->input('assunto'),
            'mensagem'             => $request->input('mensagem'),
            'data_envio'           => now(),
            'fk_comunicado_id_usuario' => Auth::id() // Quem enviou (o admin logado)
        ]);

        // 2. Busca todos os clientes cadastrados que NÃO são administradores e NÃO estão excluídos
        $clientes = User::where('admin', false)
                        ->where('excluido', false)
                        ->get();

        $textoMensagem = "*" . $comunicado->assunto . "*\n\n" . $comunicado->mensagem;

        // 3. Dispara a automação para cada cliente
        foreach ($clientes as $cliente) {
            // Limpa o número removendo espaços, parênteses ou traços (Ex: deixa apenas 5511999999999)
            $telefoneLimpo = preg_replace('/[^0-9]/', '', $cliente->telefone);

            // Se o telefone gravado não tiver o DDI do Brasil (55), adiciona automaticamente
            if (strlen($telefoneLimpo) <= 11) {
                $telefoneLimpo = "55" . $telefoneLimpo;
            }

            // Invoca o comando robótico  em tempo de execução
            Artisan::call('whatsapp:send', [
                'number'  => $telefoneLimpo,
                'message' => $textoMensagem
            ]);
        }

        // 4. Redireciona de volta para a listagem com feedback visual de sucesso
        return redirect()->route('admin.comunicados')->with('sucesso', 'Comunicado gravado e enviado para os WhatsApps dos clientes!');
    }

    public function reenviarComunicado($id)
    {
        $linha = Comunicado::find($id);
        return view('admin.comunicados.reenviarComunicado', compact('linha'));
    }
}
