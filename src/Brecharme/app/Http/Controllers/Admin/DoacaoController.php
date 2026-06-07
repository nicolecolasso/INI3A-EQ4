<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Doacao;
use App\Models\Produto;
use Illuminate\Support\Facades\File; 

class DoacaoController extends Controller
{
    public function doacoes()
    {
        $linhas = Doacao::with('usuario')
            ->orderByRaw("
                CASE status
                    WHEN 'Analise' THEN 1
                    WHEN 'Aprovada' THEN 2
                    WHEN 'Retirada' THEN 3
                    WHEN 'Rejeitada' THEN 4
                    ELSE 5
                END ASC
            ")
            ->orderBy('data_doacao', 'desc')
            ->get();

        return view('admin.doacoes.doacoes', compact('linhas'));
    }

    public function novaDoacao()
    {
        return view('admin.doacoes.novaDoacao');
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
        $dados = $this->ajusteDados($req);
        Doacao::create($dados);
        return redirect()->route('admin.doacoes');
    }

    public function editarDoacao($id)
    {
        $linha = Doacao::findOrFail($id);

        // 🔒 TRAVA: Se já virou produto, gerencia na aba de produtos
        if ($linha->status === 'Retirada') {
            return redirect()->route('admin.doacoes')
                ->with('erro', 'Esta doação já foi retirada e virou um produto. Gerencie-a na aba de Produtos.');
        }

        return view('admin.doacoes.editarDoacao', compact('linha'));
    }

    public function atualizar(Request $req, $id)
    {
        $doacao = Doacao::findOrFail($id);
        
        // 🔒 TRAVA: Impede atualizações se já foi integrada ao estoque
        if ($doacao->status === 'Retirada') {
            return redirect()->route('admin.doacoes')
                ->with('erro', 'Não é possível alterar uma doação cujo produto já está no estoque.');
        }

        // Se a requisição veio do botão rápido (só o status: Analise -> Aprovada / Rejeitada)
        if ($req->has('status') && !$req->has('nome')) {
            $dados = ['status' => $req->status];
        } else {
            $dados = $this->ajusteDados($req);
            if (!$req->hasFile('caminho_img')) {
                $dados['caminho_img'] = $doacao->caminho_img;
            }
        }

        // ✨ LÓGICA CORRETA: Apenas atualiza a doação. O produto não existe ainda!
        $doacao->update($dados);

        return redirect()->route('admin.doacoes')->with('sucesso', 'Doação atualizada com sucesso.');
    }

    public function retirar(Request $req, $id)
    {
        $doacao = Doacao::findOrFail($id);
        $precoDigitado = $req->input('preco'); 

        if ($doacao->status === 'Retirada') {
            return redirect()->route('admin.doacoes')->with('erro', 'Este item já foi retirado anteriormente.');
        }

        $doacao->update(['status' => 'Retirada']);

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

        // Aqui nasce o produto real no estoque público
        Produto::create([
            'nome'        => $doacao->nome,
            'descricao'   => $doacao->descricao,
            'categoria'   => $doacao->categoria,
            'caminho_img' => $caminhoNovoProdutoRelativo, 
            'valor'       => $precoDigitado,
            'status'      => 'Disponível', 
            'excluido'    => false,
            'created_at'  => now(),
            'updated_at'  => now()
        ]);

        return redirect()->route('admin.doacoes')->with('sucesso', 'Item retirado com sucesso e produto disponível na vitrine!');
    }

    public function rejeitar($id)
    {
        $doacao = Doacao::findOrFail($id);
        $doacao->update(['status' => 'Rejeitada']);

        return redirect()->route('admin.doacoes')->with('sucesso', 'Doação rejeitada com sucesso.');
    }
}