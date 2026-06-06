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
            $num = rand(1111, 9999);
            $dir = "img/doacoes/";
            
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
        Doacao::create($dados);
        return redirect()->route('admin.doacoes');
    }

    public function editarDoacao($id)
    {
        $linha = Doacao::find($id);
        return view('admin.doacoes.editarDoacao', compact('linha'));
    }

    public function atualizar(Request $req, $id)
    {
        $doacao = Doacao::findOrFail($id);
        $dados = $this->ajusteDados($req);

        if (!$req->hasFile('caminho_img')) {
            $dados['caminho_img'] = $doacao->caminho_img;
        }

        $doacao->update($dados);

        // Se o admin rebaixar o status de uma doação que já virou produto, removemos o produto correspondente da vitrine
        if (in_array($req->status, ['Rejeitada', 'Analise'])) {
            Produto::where('nome', $doacao->nome)
                ->where('categoria', $doacao->categoria)
                ->update([
                    'status' => 'Reservado',
                    'excluido' => true
                ]);
        }

        return redirect()->route('admin.doacoes')->with('sucesso', 'Doação atualizada e stock protegido contra inconsistências.');
    }

    public function retirar(Request $req, $id)
    {
        $doacao = Doacao::find($id);
        $precoDigitado = $req->input('valor_produto');

        if ($doacao) {
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

            return redirect()->route('admin.doacoes')->with('sucesso', 'Item retirado com sucesso e produto disponibilizado na vitrine!');
        }

        return redirect()->route('admin.doacoes')->with('erro', 'Doação não encontrada.');
    }

    public function rejeitar($id)
    {
        $doacao = Doacao::findOrFail($id);
        $doacao->update(['status' => 'Rejeitada']);

        // Remove o produto correspondente caso ele já tivesse sido gerado previamente
        Produto::where('nome', $doacao->nome)
            ->where('categoria', $doacao->categoria)
            ->update([
                'status' => 'Reservado',
                'excluido' => true
            ]);

        return redirect()->route('admin.doacoes')->with('sucesso', 'Doação rejeitada e stock limpo com segurança.');
    }
}