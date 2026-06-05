<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produto;
use App\Models\Doacao;
use Illuminate\Support\Facades\Auth;

class VitrineController extends Controller
{
    public function vitrine(Request $request)
    {
        $query = Produto::where('status', 'Disponível')
                        ->where('excluido', false);

        if ($request->has('categoria') && $request->categoria != '') {
            $query->where('categoria', $request->categoria);
        }

        $produtos = $query->paginate(12);

        return view('produtos.vitrine', compact('produtos'));
    }

    public function detalheProduto($id)
    {
        $produto = Produto::where('id_produto', $id)->firstOrFail();
        return view('produtos.produto', compact('produto'));
    }

    public function novaDoacao()
    {
        return view('produtos.novaDoacao');
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

    public function salvarDoacao(Request $request)
    {
        $dados = $this->ajusteDados($request);

        $dados['fk_doacao_id_usuario'] = Auth::id();
        $dados['data_doacao'] = now();
        $dados['status']      = 'Analise'; 

        Doacao::create($dados);

        return redirect()->route('perfil.minhasDoacoes')->with('sucesso', 'Sua proposta de doação foi enviada para análise!');
    }
}