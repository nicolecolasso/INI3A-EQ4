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

        return view('carrinho.carrinho', compact('reservas'));
    }

    public function finalizar()
    {
        // 1. Busca todas as compras com status "Carrinho" pertencentes a este utilizador
        $reservasNoCarrinho = Compra::where('fk_compra_id_usuario', Auth::id())
                                    ->where('status', 'Carrinho')
                                    ->get();

        if ($reservasNoCarrinho->isEmpty()) {
            return redirect()->route('produtos.vitrine')->with('erro', 'O seu carrinho está vazio.');
        }

        // 2. Transiciona o status dos produtos através da tabela pivot intermediária
        foreach ($reservasNoCarrinho as $reserva) {
            $itensPivot = ProdutoReserva::where('fk_id_compra', $reserva->id_compra)->get();

            foreach ($itensPivot as $item) {
                // Atualiza o produto para mantê-lo fora da vitrine pública de compras
                Produto::where('id_produto', $item->fk_id_produto)->update(['status' => 'Reservado']);
                
                // Consolida o estado na tabela pivot
                $item->update(['status' => 'Reservado']);
            }

            // 3. Modifica o estado global da compra de Carrinho para Reservado de facto
            $reserva->update([
                'status' => 'Reservado',
                'data_compra' => now()
            ]);
        }

        return redirect()->route('carrinho')->with('sucesso', 'Reserva de peças confirmada! Venha até a loja física para efetuar o pagamento.');
    }

    public function remover($id)
    {
        $reserva = ProdutoReserva::where('fk_id_produto', $id)
            ->whereHas('compra', function($query) {
                $query->where('fk_compra_id_usuario', Auth::id())
                    ->where('status', 'Carrinho');
            })
            ->first();

        if (!$reserva) {
            return redirect()->back()->with('erro', 'Produto não localizado no carrinho.');
        }

        $idCompra = $reserva->fk_id_compra;
        $reserva->delete();

        // O produto volta a ficar "Disponível" instantaneamente para qualquer outro cliente ver na vitrine
        Produto::where('id_produto', $id)->update(['status' => 'Disponível']);

        $qtdeItems = ProdutoReserva::where('fk_id_compra', $idCompra)->count();
        if ($qtdeItems === 0) {
            Compra::where('id_compra', $idCompra)->update(['status' => 'Cancelada']);
        }

        return redirect()->route('carrinho')->with('sucesso', 'Item removido e devolvido à vitrine pública.');
    }
}