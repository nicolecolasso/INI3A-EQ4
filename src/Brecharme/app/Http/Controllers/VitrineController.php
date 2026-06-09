<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produto;
use App\Models\Doacao;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

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
        $produto = Produto::where('id_produto', $id)
                        ->where('excluido', false)
                        ->where('status', 'Disponível')
                        ->firstOrFail(); 

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
        $dados = $this->ajusteDados($request);

        $dados['fk_doacao_id_usuario'] = Auth::id();
        $dados['data_doacao'] = now();
        $dados['status']      = 'Analise'; 

        Doacao::create($dados);

        return redirect()->route('perfil.minhasDoacoes')->with('sucesso', 'Sua proposta de doação foi enviada para análise!');
    }
}