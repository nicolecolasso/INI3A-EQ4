<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;
use App\Models\Produto;
use App\Models\Doacao;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class VitrineController extends Controller
{
    public function vitrine(Request $request)
    {
        $query = Produto::where('status', 'Disponível')
                        ->where('excluido', false);

        if ($request->filled('categoria')) {
            $query->where('fk_id_categoria', $request->categoria);
        }

        if ($request->filled('preco_min')) {
            $query->where('valor', '>=', $request->preco_min);
        }

        if ($request->filled('preco_max')) {
            $query->where('valor', '<=', $request->preco_max);
        }

        $produtos = $query->paginate(12);
        $categorias = Categoria::orderBy('nome')->get();

        return view('produtos.vitrine', compact('produtos', 'categorias'));
    }

    public function buscar(Request $request)
    {
        $query = Produto::where('status', 'Disponível')
                        ->where('excluido', false);

        if ($request->filled('q')) {
            $searchTerm = $request->q;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('nome', 'ilike', "%{$searchTerm}%")
                  ->orWhere('descricao', 'ilike', "%{$searchTerm}%")
                  ->orWhereHas('categoria', function ($categoriaQuery) use ($searchTerm) {
                      $categoriaQuery->where('nome', 'ilike', "%{$searchTerm}%" );
                  });
            });
        }

        if ($request->filled('categoria')) {
            $query->where('fk_id_categoria', $request->categoria);
        }

        if ($request->filled('preco_min')) {
            $query->where('valor', '>=', $request->preco_min);
        }

        if ($request->filled('preco_max')) {
            $query->where('valor', '<=', $request->preco_max);
        }

        $produtos = $query->paginate(12)->appends($request->query());
        $categorias = Categoria::orderBy('nome')->get();

        return view('produtos.vitrine', compact('produtos', 'categorias'));
    }

    public function detalheProduto($id)
    {
        $produto = Produto::where('id_produto', $id)
                        ->where('excluido', false)
                        ->where('status', 'Disponível')
                        ->firstOrFail(); 

        return view('produtos.produto', compact('produto'));
    }

    public function novaDoacao()
    {
        $categorias = Categoria::orderBy('nome', 'asc')->get();
        return view('produtos.novaDoacao', compact('categorias'));
    }

    private function ajusteDados(Request $req)
    {
        $dados = $req->all();

        if ($req->hasFile('caminho_img')) {
            $imagem = $req->file('caminho_img');
            
            // Evita o risco de uma imagem sobrescrever outra
            $nomeImagem = $imagem->hashName(); 

            $targetPath = public_path('img/doacoes');

            // Garante que a pasta existe usando a ferramenta do Laravel
            File::ensureDirectoryExists($targetPath);

            $imagem->move($targetPath, $nomeImagem);

            $dados['caminho_img'] = "img/doacoes/" . $nomeImagem;
        }

        return $dados;
    }

    public function salvarDoacao(Request $request)
    {
        $request->validate([
            'nome'           => 'required|string',
            'categoria_nome' => 'required|string|max:255',
            'descricao'      => 'nullable|string',
            'caminho_img'    => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'localizacao'    => $request->input('necessita_retirada') == '1' ? 'required|string' : 'nullable|string',
        ]);

        $dados = $this->ajusteDados($request);

        $dados['fk_doacao_id_usuario'] = Auth::id();
        $categoria = Categoria::firstOrCreate([
                'nome' => Str::title(trim($request->input('categoria_nome')))
            ]);
        $dados['fk_id_categoria'] = $categoria->id_categoria;
        $dados['data_doacao'] = now();
        $dados['status']      = 'Em Análise'; 

        Doacao::create($dados);

        return redirect()->route('perfil.minhasDoacoes')->with('sucesso', 'Sua proposta de doação foi enviada para análise!');
    }
}