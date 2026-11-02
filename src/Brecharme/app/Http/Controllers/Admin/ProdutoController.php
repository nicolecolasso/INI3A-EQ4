<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Produto;
use App\Models\Compra;
use App\Models\Categoria;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use App\Models\ProdutoReserva;

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
            'caminho_img'    => 'required|image',
        ]);

        $dados = $this->ajusteDados($req);

        $categoria = Categoria::firstOrCreate([
            'nome' => Str::title(trim($req->input('categoria_nome')))
        ]);

        $dados['status'] = 'Disponível';

        $dados['fk_id_categoria'] = $categoria->id_categoria;

        Produto::create($dados);

        return redirect()->route('admin.produtos')->with('sucesso', 'Produto cadastrado no catálogo com sucesso!');    
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

        $req->validate([
            'nome'           => 'required|string',
            'valor'          => 'required|numeric',
            'categoria_nome' => 'required|string|max:255',
            'descricao'      => 'required|string',
            'caminho_img'    => 'nullable|image',
            'status'         => 'required|in:Disponível,Carrinho,Reservado,Vendido'
        ]);

        $dados = $this->ajusteDados($req);
        
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

        DB::transaction(function () use ($produto, $id) {
            $idsCompras = ProdutoReserva::where('fk_id_produto', $id)
                ->whereIn('status', ['Carrinho', 'Reservado'])
                ->pluck('fk_id_compra')
                ->unique();

            // Cancela o vínculo na pivot
            ProdutoReserva::where('fk_id_produto', $id)
                ->whereIn('status', ['Carrinho', 'Reservado'])
                ->update(['status' => 'Cancelado']);

            // Cancela compras abertas que ficaram sem nenhum item ativo
            foreach ($idsCompras as $idCompra) {
                $temAtivo = ProdutoReserva::where('fk_id_compra', $idCompra)
                    ->where('status', '!=', 'Cancelado')
                    ->exists();

                if (!$temAtivo) {
                    Compra::where('id_compra', $idCompra)
                        ->whereIn('status', ['Carrinho', 'Reservado'])
                        ->update(['status' => 'Cancelada']);
                }
            }

            $produto->update([
                'excluido'      => true,
                'data_exclusao' => now(),
                'status'        => $produto->status === 'Vendido' ? 'Vendido' : 'Disponível',
            ]);
        });

        return redirect()->route('admin.produtos')->with('sucesso', 'Produto excluído. Vínculos em carrinho/reserva foram cancelados.');
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