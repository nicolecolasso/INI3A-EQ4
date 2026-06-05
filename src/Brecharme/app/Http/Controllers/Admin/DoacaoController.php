<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Doacao;
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
            
            // Pega a extensão real do arquivo (ex: png, jpg)
            $ex = $imagem->getClientOriginalExtension(); 
            $nomeImagem = "imagem_" . $num . "." . $ex;
            
            // Move o arquivo fisicamente para public/img/doacoes/
            $imagem->move(public_path($dir), $nomeImagem);
            
            // Grava no array de dados o caminho completo que vai para o banco
            $dados['caminho_img'] = $dir . $nomeImagem;
        }

        return $dados;
    }

 
    public function salvar(Request $req)
    {
        $dados = $this->ajusteDados($req);
        
        $dados['fk_doacao_id_usuario'] = \Illuminate\Support\Facades\Auth::id();
        
        $dados['data_doacao'] = now();

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
        // Procura o registro original antes de atualizar
        $doacao = Doacao::where('id_doacao', $id)->firstOrFail();
        
        $dados = $this->ajusteDados($req);
        
        if (!$req->hasFile('caminho_img')) {
            $dados['caminho_img'] = $doacao->caminho_img;
        }

        $doacao->update($dados);

        return redirect()->route('admin.doacoes')->with('sucesso', 'Doação atualizada com sucesso!');
    }

    public function aprovar($id)
    {
        Doacao::where('id_doacao', $id)->update(['status' => 'Aprovada']);
        return redirect()->route('admin.doacoes')->with('sucesso', 'Doação aprovada! Aguardando a retirada do item.');
    }

    public function retirar(Request $req, $id)
    {
        // Procura pela chave primária id_doacao
        $doacao = Doacao::where('id_doacao', $id)->first();

        if ($doacao) {
            $doacao->update(['status' => 'Retirada']);

            $precoDigitado = $req->input('preco', 0.00);
            
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

            \App\Models\Produto::create([
                'nome'        => $doacao->nome,
                'descricao'   => $doacao->descricao,
                'categoria'   => $doacao->categoria,
                'caminho_img' => $caminhoNovoProdutoRelativo, 
                'valor'       => $precoDigitado,
                'status'      => 'Disponível',
                'created_at'  => now(),
                'updated_at'  => now()
            ]);

            return redirect()->route('admin.doacoes')->with('sucesso', 'Item retirado! Produto criado na vitrine e histórico de doação preservado.');
        }

        return redirect()->route('admin.doacoes')->with('erro', 'Doação não encontrada.');
    }

    public function rejeitar($id)
    {
        Doacao::where('id_doacao', $id)->update(['status' => 'Rejeitada']);
        return redirect()->route('admin.doacoes')->with('aviso', 'Doação rejeitada.');
    }

}
