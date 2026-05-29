<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DoacaoController extends Controller
{
    public function doacoes()
    {
        return view('admin.doacoes.doacoes');
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

}
