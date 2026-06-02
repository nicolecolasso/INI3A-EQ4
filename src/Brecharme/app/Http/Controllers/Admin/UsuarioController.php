<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UsuarioController extends Controller
{
    public function usuarios()
    {
        $linhas = User::orderBy('id', 'desc')->get();

        return view('admin.usuarios.usuarios', compact('linhas'));
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

        User::create($dados);
        return redirect()->route('admin.usuarios');
    }


    public function editarUsuario($id)
    {
        $linha = User::find($id);
        return view('admin.usuarios.editarUsuario', compact('linha'));
    }


    public function atualizar(Request $req, $id)
    {
        $dados = $req->all();

        if (!empty($dados['senha'])) {
            $dados['senha'] = bcrypt($dados['senha']); 
        } else {
            unset($dados['senha']); 
        }

        User::find($id)->update($dados);

        return redirect()->route('admin.usuarios');
    }

   
    public function excluir($id)
    {
        User::find($id)->update(['excluido' => true]);
        User::find($id)->update(['data_exclusao' => now()]);
        return redirect()->route('admin.usuarios');
    }
}

