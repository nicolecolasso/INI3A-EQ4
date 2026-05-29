<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Usuario;

class UsuarioController extends Controller
{
    public function usuarios()
    {
        return view('admin.usuarios.usuarios');
    }

    public function novoUsuario()
    {
        return view('admin.usuarios.novoUsuario');
    }

    public function salvar(Request $req)
    {
        $dados = $req->all();
        
        if (isset($dados['senha'])) {
            $dados['senha'] = bcrypt($dados['senha']); 
        }

        Usuario::create($dados);
        return redirect()->route('admin.usuarios');
    }


    public function editarUsuario($id)
    {
        $linha = Usuario::find($id);
        return view('admin.usuarios.editarUsuario', compact('linha'));
    }


    public function atualizar(Request $req, $id)
    {
        $dados = $req->all();
        Usuario::find($id)->update($dados);

        return redirect()->route('admin.usuarios');
    }

   
    public function excluir($id)
    {
        Usuario::find($id)->update(['excluido' => true]);
        Usuario::find($id)->update(['data_exclusao' => now()]);
        return redirect()->route('admin.usuarios');
    }
}

