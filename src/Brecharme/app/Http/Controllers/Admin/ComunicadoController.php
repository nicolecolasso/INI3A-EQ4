<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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

    public function salvar(Request $req)
    {
        $dados = $req->all();
        
        $dados['data_envio'] = now();       

        Comunicado::create($dados);
        
        return redirect()->route('admin.comunicados');
    }

    public function reenviarComunicado($id)
    {
        $linha = Comunicado::find($id);
        return view('admin.comunicados.reenviarComunicado', compact('linha'));
    }
}
