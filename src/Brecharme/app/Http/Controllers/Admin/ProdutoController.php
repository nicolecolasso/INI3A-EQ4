<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Produto;
use App\Models\Doacao;
use App\Models\ProdutoReserva;
use App\Models\Compra;

class ProdutoController extends Controller
{
    public function produtos()
    {
        $linhas = Produto::orderBy('id_produto', 'desc')->get();
        return view('admin.produtos.produtos', compact('linhas'));
    }

    public function novoProduto()
    {
        $doacoes = Doacao::all(); 
        return view('admin.produtos.novoProduto', compact('doacoes'));
    }

    private function ajusteDados(Request $req)
    {
        $dados = $req->all();

        if ($req->hasFile('caminho_img')) {
            $imagem = $req->file('caminho_img');
            $num = rand(1111, 9999);
            $dir = "img/produtos/";
            
            $ex = $imagem->getClientOriginalExtension(); 
            $nomeImagem = "imagem_" . $num . "." . $ex;
            
            $imagem->move(public_path($dir), $nomeImagem);
            $dados['caminho_img'] = $dir . $nomeImagem;
        }

        return $dados;
    }

    public function salvar(Request $req)
    {
        $dados = $this->ajusteDados($req);
        Produto::create($dados);
        return redirect()->route('admin.produtos');
    }

    public function editarProduto($id)
    {
        $linha = Produto::find($id);
        $doacoes = Doacao::all(); 
        return view('admin.produtos.editarProduto', compact('linha', 'doacoes'));
    }

    public function atualizar(Request $req, $id)
    {
        $produto = Produto::where('id_produto', $id)->firstOrFail();
        $dados = $this->ajusteDados($req);
        
        if (!$req->hasFile('caminho_img')) {
            $dados['caminho_img'] = $produto->caminho_img;
        }

        $produto->update($dados);

        return redirect()->route('admin.produtos')->with('sucesso', 'Produto atualizado com sucesso!');
    }

    public function excluir($id)
    {
        // 1. Localiza se o produto de peça única estava em algum carrinho ou reserva aberta
        $vinculosAtivos = ProdutoReserva::where('fk_id_produto', $id)->get();

        foreach ($vinculosAtivos as $vinculo) {
            $idCompra = $vinculo->fk_id_compra;
            $vinculo->delete(); // Elimina o item específico da transação

            // Se a compra associada ficar vazia após remover o item excluído, ela é cancelada
            $restantes = ProdutoReserva::where('fk_id_compra', $idCompra)->count();
            if ($restantes === 0) {
                Compra::where('id_compra', $idCompra)->update(['status' => 'Cancelada']);
            }
        }

        // 2. Aplica a exclusão lógica no produto
        Produto::find($id)->update([
            'excluido' => true,
            'status'   => 'Reservado' // Muda o status para não aparecer em queries de vitrine comum
        ]);

        return redirect()->route('admin.produtos')->with('sucesso', 'Produto removido e transações associadas saneadas.');
    }
}