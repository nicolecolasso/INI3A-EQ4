<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Produto;
use App\Models\Doacao;

class ProdutoController extends Controller
{
    public function produtos()
    {
        return view('admin.produtos.produtos');
    }

    public function novoProduto()
    {
        $doacoes = Doacao::all(); 
        return view('admin.produtos.novoProduto', compact('doacoes'));
    }

    private function ajusteDados(Request $req)
    {
        $dados = $req->all();

        if ($req->hasFile('arquivo')) {
            $imagem = $req->file('arquivo');
            $num = rand(1111, 9999);
            $dir = "img/produtos/";
            $ex = $imagem->guessClientExtension();
            $nomeImagem = "imagem_" . $num . "." . $ex;
            $imagem->move($dir, $nomeImagem);
            
            $dados['caminho_img'] = $dir . $nomeImagem;
        }

        if (!isset($dados['excluido'])) {
            $dados['excluido'] = false;
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
        $linha = Produto::find($id);
        $doacoes = Doacao::all(); 
        
        return view('admin.produtos.editarProduto', compact('linha', 'doacoes'));
    }


    public function atualizar(Request $req, $id)
    {
        $dados = $this->ajusteDados($req);
        Produto::find($id)->update($dados);

        return redirect()->route('admin.produtos');
    }

    public function excluir($id)
    {
        Produto::find($id)->update(['excluido' => true]);
        Produto::find($id)->update(['data_exclusao' => now()]);
        return redirect()->route('admin.produtos');
    }
}
