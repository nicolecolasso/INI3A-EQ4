<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Compra;
use App\Models\ProdutoReserva;
use App\Models\Produto;

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
        
        if (!empty($dados['senha'])) {
            $dados['password'] = bcrypt($dados['senha']); 
        }
        
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

        if (!empty($dados['senha'])) {
            $dados['password'] = bcrypt($dados['senha']); 
        } else {
            unset($dados['password']); 
        }
        
        unset($dados['senha']); 

        User::find($id)->update($dados);

        return redirect()->route('admin.usuarios');
    }
   
    public function excluir($id)
    {
        // 1. Localiza as compras ativas deste utilizador que prendem stock unificado
        $comprasAtivas = Compra::where('fk_compra_id_usuario', $id)
            ->whereIn('status', ['Carrinho', 'Reservado'])
            ->get();

        foreach ($comprasAtivas as $compra) {
            // Busca as linhas intermediárias da tabela pivot
            $itens = ProdutoReserva::where('fk_id_compra', $compra->id_compra)->get();
            
            foreach ($itens as $item) {
                // Devolve a peça única para a vitrine pública
                Produto::where('id_produto', $item->fk_id_produto)
                    ->update(['status' => 'Disponível']);
                
                // Atualiza o estado na pivot
                $item->update(['status' => 'Cancelado']);
            }

            // Atualiza o status da compra mãe
            $compra->update(['status' => 'Cancelada']);
        }

        // 2. Executa a exclusão lógica do utilizador com segurança
        User::find($id)->update([
            'excluido' => true,
            'data_exclusao' => now()
        ]);

        return redirect()->route('admin.usuarios')->with('sucesso', 'Utilizador excluído e stock pendente devolvido à vitrine com sucesso!');
    }
}