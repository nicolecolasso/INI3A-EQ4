<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Produto;
use App\Models\Doacao;
use App\Models\ProdutoReserva;
use App\Models\Compra;
use Illuminate\Support\Facades\File;

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
            $nomeImagem = $imagem->hashName(); 
            $targetPath = public_path('img/produtos');

            File::ensureDirectoryExists($targetPath);
            $imagem->move($targetPath, $nomeImagem);

            $dados['caminho_img'] = "img/produtos/" . $nomeImagem;
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
        $linha = Produto::where('id_produto', $id)->where('excluido', false)->firstOrFail();
        $doacoes = Doacao::all(); 
        return view('admin.produtos.editarProduto', compact('linha', 'doacoes'));
    }

    public function atualizar(Request $req, $id)
    {
        $produto = Produto::where('id_produto', $id)->where('excluido', false)->firstOrFail();
        $dados = $this->ajusteDados($req);
        
        if (!$req->hasFile('caminho_img')) {
            $dados['caminho_img'] = $produto->caminho_img;
        }

        $produto->update($dados);

        return redirect()->route('admin.produtos')->with('sucesso', 'Produto updated successfully!');
    }

    public function excluir($id)
    {
        // 1. Localiza se o produto de peça única estava em algum carrinho/reserva aberta de usuários
        $vinculosAtivos = ProdutoReserva::where('fk_id_produto', $id)->get();

        foreach ($vinculosAtivos as $vinculo) {
            $idCompra = $vinculo->fk_id_compra;
            $vinculo->delete(); // Remove o item da transação pivot imediatamente

            // Se a compra associada ficar vazia, ela é cancelada
            $restantes = ProdutoReserva::where('fk_id_compra', $idCompra)->count();
            if ($restantes === 0) {
                Compra::where('id_compra', $idCompra)->update(['status' => 'Cancelada']);
            }
        }

        // 2. Aplica a exclusão lógica respeitando perfeitamente o seu Enum de produtos
        Produto::where('id_produto', $id)->update([
            'excluido' => true,
            'status'   => 'Reservado' // Alinhado com seu Enum ['Disponível', 'Carrinho', 'Vendido', 'Reservado']
        ]);

        return redirect()->route('admin.produtos')->with('sucesso', 'Produto removido com sucesso e carrinhos atualizados.');
    }

    public function ativar($id)
    {
        Produto::where('id_produto', $id)->update([
            'excluido' => false,
            'status'   => 'Disponível'
        ]);

        return redirect()->route('admin.produtos')->with('sucesso', 'Produto reativado com sucesso!');
    }
}