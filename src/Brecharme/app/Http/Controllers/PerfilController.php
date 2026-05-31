<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\ProdutoReserva;
use App\Models\Doacao;

class PerfilController extends Controller
{
    public function meuPerfil()
    {
        $usuario = Auth::user();
        return view('perfil.meuPerfil', compact('usuario'));
    }

    public function meusDados()
    {
        $usuario = Auth::user();
        return view('perfil.meusDados', compact('usuario'));
    }

    public function minhasDoacoes()
    {
        $doacoes = Auth::user()->doacoes; 
        return view('perfil.minhasDoacoes', compact('doacoes'));
    }

    public function minhasReservas()
    {
        $compras = Auth::user()->compras;
        return view('perfil.minhasReservas', compact('compras'));
    }

    public function cancelarDoacao($id)
    {
        $doacao = Doacao::find($id);

        if ($doacao->fk_doacao_id_usuario !== Auth::id()) {
            abort(403, 'Acesso não autorizado.');
        }

        if ($doacao->status === 'Analise') {
            
            $doacao->delete(); 
            
            return redirect()->route('perfil.minhasDoacoes')
                             ->with('sucesso', 'Proposta de doação cancelada com sucesso!');
        }

        return redirect()->route('perfil.minhasDoacoes')
                         ->with('erro', 'Esta doação já está sendo processada e não pode ser cancelada.');
    }

    public function cancelarReserva($id)
    {
        $reserva = ProdutoReserva::find($id);
        if ($reserva->compra->fk_doacao_id_usuario !== Auth::id()) {
            abort(403, 'Acesso não autorizado.');
        }
        if ($reserva->status === 'Reservado' || $reserva->status === 'Carrinho') {
            $reserva->delete(); 
            return redirect()->route('perfil.minhasReservas')
                             ->with('sucesso', 'Reserva cancelada com sucesso!');
        }
        return redirect()->route('perfil.minhasReservas')
                         ->with('erro', 'Esta reserva não pode ser cancelada.');
    }   
}