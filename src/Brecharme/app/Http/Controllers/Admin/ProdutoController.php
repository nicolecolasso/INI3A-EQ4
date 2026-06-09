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
        return view('admin.produtos.novoProduto');
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
        return view('admin.produtos.editarProduto', compact('linha'));
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
        $produto = Produto::where('id_produto', $id)->where('excluido', false)->firstOrFail();

        // Descobre IDs de compras possuem este produto associado em carrinhos/reservas
        $idsComprasAfetadas = \App\Models\ProdutoReserva::where('fk_id_produto', $id)
            ->whereIn('status', ['Carrinho', 'Reservado'])
            ->pluck('fk_id_compra')
            ->unique();

        if ($idsComprasAfetadas->isNotEmpty()) {
            // Modifica o status na tabela pivô (intermediária) para 'Cancelado'
            $produto->compras()->updateExistingPivotIds($idsComprasAfetadas, ['status' => 'Cancelado']);

            // Verifica se as compras afetadas estão sem nenhum item ativo
            foreach ($idsComprasAfetadas as $idCompra) {
                $itensAtivos = \App\Models\ProdutoReserva::where('fk_id_compra', $idCompra)
                    ->whereIn('status', ['Carrinho', 'Reservado'])
                    ->count();

                // Se não sobrou nenhum item ativo, a compra geral também é cancelada
                if ($itensAtivos === 0) {
                    Compra::where('id_compra', $idCompra)->update(['status' => 'Cancelada']);
                }
            }
        }

        $produto->update([
            'excluido' => true,
            'data_exclusao' => now()   
        ]);

        return redirect()->route('admin.produtos')->with('sucesso', 'Produto excluído com sucesso. Vínculos atualizados para Cancelado.');
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