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

   public function novoComunicado()
    {
        return view('admin.comunicados.novoComunicado');
    }

    public function salvar(Request $request)
    {
        Comunicado::create([
            'assunto'              => $request->input('assunto'),
            'mensagem'             => $request->input('mensagem'),
            'data_envio'           => now(),
            'status'               => Comunicado::STATUS_PENDENTE,
            'fk_comunicado_id_usuario' => Auth::id() // Quem enviou (o admin logado)
        ]);
        return redirect()->route('admin.gerenciar')->with('successo', 'Comunicado salvo e logo será enviado!');
    }

    public function reenviarComunicado()
    {
        // Carrega todos os comunicados já enviados organizados pelo mais recente
        $comunicadosAntigos = Comunicado::orderBy('data_envio', 'desc')->get();
        
        return view('admin.comunicados.reenviarComunicado', compact('comunicadosAntigos'));
    }
}
