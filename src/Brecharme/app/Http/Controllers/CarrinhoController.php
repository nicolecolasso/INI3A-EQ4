<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\ProdutoReserva;
use Illuminate\Support\Facades\Auth;

class CarrinhoController extends Controller
{
    public function index()
    {
        return view('carrinho.carrinho');
    }

    public function conclusao($id_compra)
    {

        $compra = Compra::find($id_compra);

        if ($compra->fk_doacao_id_usuario !== Auth::id()) {
            abort(403, 'Acesso não autorizado.');
        }

        if (!$compra) {
            abort(404, 'Compra não encontrada.');
        }

        $itensReservados = ProdutoReserva::with('produto')
            ->where('fk_doacao_id_compra', $id_compra)
            ->get();

        return view('carrinho.conclusaoReserva', [
            'compra' => $compra,
            'itens'  => $itensReservados,
            'usuario' => Auth::user()
        ]);
    }
}
