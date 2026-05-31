<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\ProdutoReserva;
use App\Models\Produto;

use Illuminate\Support\Facades\Auth;

class CarrinhoController extends Controller
{
    public function index()
    {
        $reservas = Compra::where('fk_compra_id_usuario', Auth::id())
                           ->where('status', 'Carrinho')
                           ->with('produto') 
                           ->get();

        return view('carrinho.index', compact('reservas'));
    }

    public function finalizar()
    {
        // 1. Busca todas as reservas deste usuário que ainda estão no carrinho
        $reservasNoCarrinho = Compra::where('fk_compra_id_usuario', Auth::id())
                                    ->where('status', 'Carrinho')
                                    ->get();

        // Se o carrinho estiver vazio por algum motivo, aborta
        if ($reservasNoCarrinho->isEmpty()) {
            return redirect()->route('produtos.vitrine')->with('erro', 'Seu carrinho está vazio.');
        }

        // 2. Loop para atualizar o status de cada item e dar baixa no estoque do produto
        foreach ($reservasNoCarrinho as $reserva) {
            // Dá baixa no estoque do produto reservado
            $produto = Produto::find($reserva->fk_compra_id_produto);
            if ($produto) {
                $produto->status = 'Reservado'; 
                $produto->save();
            }
        }

        Compra::where('fk_compra_id_usuario', Auth::id())
              ->where('status', 'Carrinho')
              ->update(['status' => 'Reservado', 'data_compra' => now()]);

        ProdutoReserva::whereIn('fk_id_compra', $reservasNoCarrinho->pluck('id_compra'))
                      ->update(['status' => 'Reservado']);

        // 3. Redireciona para a página de agradecimento/sucesso
        return redirect()->route('carrinho.conclusaoReserva', ['id_usuario' => Auth::id()]);
    }

    public function conclusaoReserva($id_usuario)
    {
        $cliente = Auth::user();
        $produtosReservados = Compra::where('fk_compra_id_usuario', $id_usuario)
                                    ->where('status', 'Reservado')
                                    ->with('produto')
                                    ->latest() 
                                    ->get();
        
        return view('carrinho.conclusaoReserva', compact('cliente', 'produtosReservados'));
    }

    public function adicionar($id)
    {
        $produto = Produto::findOrFail($id);

        // 1. Checa se este produto já está reservado por alguém
        $jaReservado = Compra::where('fk_compra_id_produto', $id)
                              ->whereIn('status', ['Carrinho', 'Reservado', 'Concluída'])
                              ->exists();

        if ($jaReservado) {
            return redirect()->back()->with('erro', 'Ops! Este produto já foi reservado por outro client.');
        }

        // 2. Cria a reserva atrelada ao usuário logado
        Compra::create([
            'fk_compra_id_usuario'    => Auth::id(),
            'fk_compra_id_produto' => $produto->id,
            'status'     => 'Carrinho',
        ]);

        Produto::where('id_produto', $id)->update(['status' => 'Carrinho']);

        ProdutoReserva::create([
            'fk_id_produto' => $produto->id,
            'fk_id_compra' => Compra::latest()->first()->id,
        ]);

        // 3. Redireciona direto para o carrinho com mensagem de sucesso
        return redirect()->route('carrinho')->with('sucesso', 'Produto adicionado ao seu carrinho!');
    }
}
