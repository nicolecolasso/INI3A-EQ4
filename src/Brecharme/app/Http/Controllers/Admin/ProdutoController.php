<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Produto;
use App\Models\Compra;
use App\Models\Categoria;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class ProdutoController extends Controller
{
    public function produtos()
    {
        $linhas = Produto::orderBy('id_produto', 'desc')->get();
        $categorias = Categoria::orderBy('nome', 'asc')->get();
        return view('admin.produtos.produtos', compact('linhas', 'categorias'));
    }

    public function buscar(Request $request)
    {
        $query = Produto::query();

        if ($request->filled('nome')) {
            $query->where('nome', 'ilike', '%' . $request->input('nome') . '%')->orderBy('nome', 'asc');
        }

        if ($request->filled('categoria')) {
            $query->where('fk_id_categoria', $request->input('categoria'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('excluido')) {
            if ($request->input('excluido') === 'ativo') {
                $query->where('excluido', false);
            } elseif ($request->input('excluido') === 'inativo') {
                $query->where('excluido', true);
            }
        }

        $linhas = $query->orderBy('id_produto', 'desc')->get();
        
        $categorias = Categoria::orderBy('nome', 'asc')->get();

        return view('admin.produtos.produtos', compact('linhas', 'categorias'));
    }

    public function novoProduto()
    {
        $categorias = Categoria::orderBy('nome', 'asc')->get();
        return view('admin.produtos.novoProduto', compact('categorias'));
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
        $req->validate([
            'nome'           => 'required|string',
            'valor'          => 'required|numeric',
            'categoria_nome' => 'required|string|max:255',
            'descricao'      => 'required|string',
            'caminho_img'    => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $dados = $this->ajusteDados($req);

        $categoria = Categoria::firstOrCreate([
            'nome' => Str::title(trim($req->input('categoria_nome')))
        ]);

        $dados['status'] = 'Disponível';

        $dados['fk_id_categoria'] = $categoria->id_categoria;

        Produto::create($dados);

        return redirect()->route('admin.produtos');
    }

    public function editarProduto($id)
    {
        $linha = Produto::where('id_produto', $id)->where('excluido', false)->firstOrFail();
        $categorias = Categoria::orderBy('nome', 'asc')->get();

        return view('admin.produtos.editarProduto', compact('linha', 'categorias'));
    }

    public function atualizar(Request $req, $id)
    {
        $produto = Produto::where('id_produto', $id)->where('excluido', false)->firstOrFail();
        $dados = $this->ajusteDados($req);

        $req->validate([
            'nome'           => 'required|string',
            'valor'          => 'required|numeric',
            'categoria_nome' => 'required|string|max:255',
            'descricao'      => 'required|string',
            'caminho_img'    => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status'         => 'required|in:Disponível,Carrinho,Reservado,Vendido'
        ]);
        
        if (!$req->hasFile('caminho_img')) {
            $dados['caminho_img'] = $produto->caminho_img;
        }

        $categoria = Categoria::firstOrCreate([
            'nome' => Str::title(trim($req->input('categoria_nome')))
        ]);

        $dados['fk_id_categoria'] = $categoria->id_categoria;

        $produto->update($dados);

        return redirect()->route('admin.produtos')->with('sucesso', 'Produto atualizado com sucesso!');
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