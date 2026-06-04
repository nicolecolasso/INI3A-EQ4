<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Compra;
use App\Models\ProdutoReserva;
use App\Models\Produto;
use App\Models\User;

class CompraController extends Controller
{
    public function reservas()
    {
        $linhas = Compra::with(['produto', 'usuario'])
            ->orderByRaw("
                CASE status
                    WHEN 'Reservado' THEN 1
                    WHEN 'Carrinho' THEN 2
                    WHEN 'Concluída' THEN 3
                    WHEN 'Cancelada' THEN 4
                    ELSE 5
                END ASC
            ")
            ->orderBy('data_compra', 'desc') // Desempata pelas movimentações mais recentes
            ->get();

        return view('admin.reservas.reservas', compact('linhas'));
    }

    public function listaReservas()
    {
        $linhas = Compra::all();
        return view('admin.reservas.listaReservas', compact('linhas'));
    }

    public function novaReserva()
    {
        $produtos = \App\Models\Produto::where('status', 'Disponível')->get();  
        $usuarios = \App\Models\User::all();
        return view('admin.reservas.novaReserva', compact('produtos', 'usuarios'));
    }

    public function salvar(Request $req)
    {
        // 1. Cria a Compra 
        $compra = Compra::create([
            'status'               => $req->status,
            'sessao'               => $req->sessao,
            'fk_compra_id_usuario' => $req->id_usuario, 
            'data_compra'          => now()
        ]);

        // 2. Vincula cada produto selecionado na tabela intermediária)
        $produtosSelecionados = $req->input('id_produto', []); // Recebe o array do select multiple
        
        foreach ($produtosSelecionados as $idProduto) {
            \App\Models\ProdutoReserva::create([
                'fk_id_produto' => $idProduto,
                'fk_id_compra'  => $compra->id_compra, 
                'status'        => $req->status
            ]);
            
            \App\Models\Produto::where('id_produto', $idProduto)->update(['status' => 'Reservado']);
        }
        
        return redirect()->route('admin.reservas')->with('sucesso', 'Reserva criada com sucesso!');
    }

    public function editarReserva($id)
    {
        $linha = Compra::with('usuario')->find($id);

        $produtos = \App\Models\Produto::where('status', 'Disponível')
            ->orWhereIn('id_produto', function($query) use ($id) {
                $query->select('fk_id_produto')
                      ->from('produto_reserva')
                      ->where('fk_id_compra', $id);
            })->get();

        $usuarios = \App\Models\User::all(); 

        return view('admin.reservas.editarReserva', compact('linha', 'produtos', 'usuarios'));
    }

    public function atualizar(Request $req, $id)
    {
        Compra::find($id)->update($req->all());
        return redirect()->route('admin.reservas');
    }

}
