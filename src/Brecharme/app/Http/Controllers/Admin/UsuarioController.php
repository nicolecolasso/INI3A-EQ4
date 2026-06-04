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
        
        // Se a senha foi preenchida, criptografa e joga na coluna correta do banco (password)
        if (!empty($dados['senha'])) {
            $dados['password'] = bcrypt($dados['senha']); 
        }
        
        // Remove o índice 'senha' para não confundir o Eloquent
        unset($dados['senha']);

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

        // 🎯 O SEGREDO AQUI: 
        // Se a senha veio preenchida no formulário de edição, atualizamos
        if (!empty($dados['senha'])) {
            $dados['password'] = bcrypt($dados['senha']); 
        } else {
            // Se veio vazia, removemos TANTO 'senha' quanto 'password' do array.
            // Assim o Laravel ignora completamente esse campo e mantém a senha antiga intacta!
            unset($dados['password']); 
        }
        
        unset($dados['senha']); 

        // Executa o update apenas com os campos modificados (como telefone, nome, etc)
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

