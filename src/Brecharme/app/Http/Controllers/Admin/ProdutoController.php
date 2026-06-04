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
        $linhas = Produto::orderBy('id_produto', 'desc')->get();

        return view('admin.produtos.produtos', compact('linhas'));
    }

    public function novoProduto()
    {
        $doacoes = Doacao::all(); 
        return view('admin.produtos.novoProduto', compact('doacoes'));
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
