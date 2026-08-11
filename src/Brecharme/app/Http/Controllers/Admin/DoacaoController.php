<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Doacao;
use App\Models\Produto;
use App\Models\Categoria;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File; 
use Illuminate\Support\Facades\Auth;

class DoacaoController extends Controller
{
    public function doacoes()
    {
        $categorias = Categoria::orderBy('nome', 'asc')->get();
        $linhas = Doacao::with('usuario')
            ->orderByRaw("
                CASE status
                    WHEN 'Em Análise' THEN 1
                    WHEN 'Aprovada' THEN 2
                    WHEN 'Integrada ao Estoque' THEN 3
                    WHEN 'Recusada' THEN 4
                    WHEN 'Cancelada' THEN 5
                    ELSE 6
                END ASC
            ")
            ->orderBy('data_doacao', 'desc')
            ->get();

        return view('admin.doacoes.doacoes', compact('linhas', 'categorias'));
    }

    public function buscar(Request $request)
    {
        $query = Doacao::with(['usuario', 'categoria']);

        if ($request->filled('termo')) {
            $termo = '%' . $request->input('termo') . '%';
            
            $query->where(function ($q) use ($termo) {
                $q->where('nome', 'ilike', $termo) // Busca pelo nome do item
                ->orWhereHas('usuario', function ($subQuery) use ($termo) {
                    $subQuery->where('name', 'ilike', $termo); // Busca pelo nome do doador
                });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('retirada')) {
            if ($request->input('retirada') === 'sim') {
                $query->whereNotNull('localizacao')->where('localizacao', '!=', '');
            } elseif ($request->input('retirada') === 'nao') {
                $query->where(function ($q) {
                    $q->whereNull('localizacao')->orWhere('localizacao', '');
                });
            }
        }

        $linhas = $query->orderByRaw("
                CASE status
                    WHEN 'Em Análise' THEN 1
                    WHEN 'Aprovada' THEN 2
                    WHEN 'Integrada ao Estoque' THEN 3
                    WHEN 'Recusada' THEN 4
                    WHEN 'Cancelada' THEN 5
                    ELSE 6
                END ASC
            ")
            ->orderBy('data_doacao', 'desc')
            ->get();

        $categorias = Categoria::orderBy('nome', 'asc')->get();

        return view('admin.doacoes.doacoes', compact('linhas', 'categorias'));
    }

    public function novaDoacao()
    {
        $categorias = Categoria::orderBy('nome', 'asc')->get();
        return view('admin.doacoes.novaDoacao', compact('categorias'));
    }

    private function ajusteDados(Request $req)
    {
        $dados = $req->all();

        if ($req->hasFile('caminho_img')) {
            $imagem = $req->file('caminho_img');
            $nomeImagem = $imagem->hashName(); 
            $targetPath = public_path('img/doacoes');

            File::ensureDirectoryExists($targetPath);
            $imagem->move($targetPath, $nomeImagem);

            $dados['caminho_img'] = "img/doacoes/" . $nomeImagem;
        }

        return $dados;
    }

    public function salvar(Request $req)
    {        
        $req->validate([
            'nome'           => 'required|string',
            'categoria_nome' => 'required|string|max:255',
            'descricao'      => 'required|string',
            'caminho_img'    => 'required|image',
            'localizacao'    => $req->input('necessita_retirada') == '1' ? 'required|string' : 'nullable|string',
        ]);

        $dados = $this->ajusteDados($req);

        // Gerencia e cria/reutiliza a categoria dinâmica
        $categoria = Categoria::firstOrCreate([
            'nome' => Str::title(trim($req->input('categoria_nome')))
        ]);

        $dados['fk_id_categoria'] = $categoria->id_categoria;
        $dados['fk_doacao_id_usuario'] = Auth::id();
        $dados['status'] = 'Em Análise'; 

        Doacao::create($dados);
        return redirect()->route('admin.doacoes')->with('sucesso', 'Doação cadastrada para análise!');
    }

    public function editarDoacao($id)
    {
        $linha = Doacao::findOrFail($id);
        $categorias = Categoria::orderBy('nome', 'asc')->get();

        if ($linha->status === 'Integrada ao Estoque') {
            return redirect()->route('admin.doacoes')
                ->with('erro', 'Esta doação já foi integrada e virou um produto no estoque.');
        }

        return view('admin.doacoes.editarDoacao', compact('linha', 'categorias'));
    }

    public function atualizar(Request $req, $id)
    {
        $doacao = Doacao::findOrFail($id);
        
        if ($doacao->status === 'Integrada ao Estoque') {
            return redirect()->route('admin.doacoes')
                ->with('erro', 'Não é possível alterar uma doação concluída.');
        }

        // Mudança rápida de status na tabela
        if ($req->has('status') && !$req->has('nome')) {
            $req->validate([
                'status' => 'required|in:Em Análise,Aprovada,Integrada ao Estoque,Recusada,Cancelada'
            ]);
            $dados = ['status' => $req->status];
        } else {
            $req->validate([
                'nome'           => 'required|string',
                'categoria_nome' => 'required|string|max:255',
                'descricao'      => 'required|string',
                'caminho_img'    => 'required|image',
                'localizacao'    => $req->input('necessita_retirada') == '1' ? 'required|string' : 'nullable|string',
                'status'         => 'required|in:Em Análise,Aprovada,Integrada ao Estoque,Recusada,Cancelada'
            ]);
            $dados = $this->ajusteDados($req);

            $categoria = Categoria::firstOrCreate([
                'nome' => Str::title(trim($req->input('categoria_nome')))
            ]);
            $dados['fk_id_categoria'] = $categoria->id_categoria;

            if (!$req->hasFile('caminho_img')) {
                $dados['caminho_img'] = $doacao->caminho_img;
            }
        }

        $doacao->update($dados);
        return redirect()->route('admin.doacoes')->with('sucesso', 'Doação atualizada com sucesso.');
    }

    public function integrar(Request $req, $id)
    {
        $doacao = Doacao::findOrFail($id);

        $req->validate([
            'preco' => 'required|numeric|min:0'
        ]);

        $precoDigitado = $req->input('preco'); 

        if ($doacao->status === 'Integrada ao Estoque') {
            return redirect()->route('admin.doacoes')->with('erro', 'Este item já está no estoque.');
        }

        $doacao->update(['status' => 'Integrada ao Estoque']);

        $caminhoDoacaoFisico = public_path($doacao->caminho_img); 
        $nomeArquivo = basename($doacao->caminho_img); 
        
        $caminhoNovoProdutoRelativo = "img/produtos/" . $nomeArquivo;
        $caminhoProdutoFisico = public_path($caminhoNovoProdutoRelativo); 

        if (!File::exists(public_path('img/produtos'))) {
            File::makeDirectory(public_path('img/produtos'), 0755, true);
        }
        
        if (File::exists($caminhoDoacaoFisico)) {
            File::copy($caminhoDoacaoFisico, $caminhoProdutoFisico);
        }

        // Cria o produto herdando a chave de categoria dinâmica original da doação!
        Produto::create([
            'nome'             => $doacao->nome,
            'descricao'        => $doacao->descricao,
            'fk_id_categoria'  => $doacao->fk_id_categoria, 
            'caminho_img'      => $caminhoNovoProdutoRelativo, 
            'valor'            => $precoDigitado,
            'status'           => 'Disponível', 
            'excluido'         => false
        ]);

        return redirect()->route('admin.doacoes')->with('sucesso', 'Item recebido com sucesso e integrado à vitrine!');
    }

    public function aceitar($id)
    {
        $doacao = Doacao::findOrFail($id);
        $doacao->update(['status' => 'Aprovada']);

        return redirect()->route('admin.doacoes')->with('sucesso', 'Doação aprovada com sucesso!');
    }

    public function rejeitar($id)
    {
        $doacao = Doacao::findOrFail($id);
        $doacao->update(['status' => 'Recusada']);

        return redirect()->route('admin.doacoes')->with('sucesso', 'Doação recusada.');
    }
}