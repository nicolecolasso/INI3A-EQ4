<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Doacao;

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

        if ($req->hasFile('arquivo')) {
            $imagem = $req->file('arquivo');
            $num = rand(1111, 9999);
            $dir = "img/doacoes/";
            $ex = $imagem->guessClientExtension();
            $nomeImagem = "imagem_" . $num . "." . $ex;
            $imagem->move($dir, $nomeImagem);
            
            $dados['caminho_img'] = $dir . $nomeImagem;
        }

        return $dados;
    }

 
    public function salvar(Request $req)
    {
        $dados = $this->ajusteDados($req);
        $dados['fk_doacao_id_usuario'] = $dados['id_usuario'];
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
        $dados = $this->ajusteDados($req);
        
        Doacao::find($id)->update($dados);

        return redirect()->route('admin.doacoes');
    }

    public function aprovar($id)
    {
        Doacao::where('id_doacao', $id)->update(['status' => 'Aprovada']);
        return redirect()->route('admin.doacoes')->with('sucesso', 'Doação aprovada com sucesso!');
    }

    public function rejeitar($id)
    {
        Doacao::where('id_doacao', $id)->update(['status' => 'Rejeitada']);
        return redirect()->route('admin.doacoes')->with('aviso', 'Doação rejeitada.');
    }

}
