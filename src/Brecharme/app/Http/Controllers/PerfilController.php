<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\ProdutoReserva;
use App\Models\Doacao;
use App\Models\Compra;
use App\Models\User;
use App\Models\Produto;
use App\Http\Controllers\Controller;


class PerfilController extends Controller
{
    public function meuPerfil()
    {
        $usuario = User::findOrFail(Auth::id());

        $totalDoacoes = $usuario->doacoes()->where('status', 'Aprovado')->count();

        $totalReservas = $usuario->compras()->where('status', 'Concluída')->count();

        return view('perfil.meuPerfil', compact('usuario', 'totalDoacoes', 'totalReservas'));
    }


    public function meusDados()
    {
        $usuario = User::findOrFail(Auth::id());
        return view('perfil.meusDados', compact('usuario'));
    }


    public function atualizarDados(Request $request)
    {
        $usuario = User::findOrFail(Auth::id());

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users,email,' . $usuario->id,
            'telefone' => 'nullable|string|max:20',
            'senha_atual' => 'nullable|required_with:nova_senha',
            'nova_senha'  => 'nullable|string|min:6|confirmed'
        ]);

        // Se o usuário tentou mudar a senha, valida se a atual está correta
        if ($request->filled('nova_senha')) {
            if (!Hash::check($request->senha_atual, $usuario->password)) {
                return redirect()->back()->with('erro', 'A senha atual digitada está incorreta.');
            }
            $usuario->password = Hash::make($request->nova_senha);
        }

        $usuario->name = $request->input('name');
        $usuario->email = $request->input('email');
        $usuario->telefone = $request->input('telefone');
        $usuario->save();

        return redirect()->route('perfil.meusDados')->with('sucesso', 'Dados atualizados com sucesso!');
    }

    public function minhasDoacoes()
    {
        $usuario = User::findOrFail(Auth::id());
        $doacoes = $usuario->doacoes; 
        return view('perfil.minhasDoacoes', compact('doacoes'));
    }

    public function minhasReservas()
    {
        // O with(['itens.produto']) traz a tabela intermediária e os produtos de uma só vez
        $compras = Compra::with(['itens.produto'])
            ->where('fk_compra_id_usuario', Auth::id())
            ->orderBy('id_compra', 'desc')
            ->get();

        return view('perfil.minhasReservas', compact('compras'));
    }

    public function cancelarDoacao($id)
    {
        $doacao = Doacao::where('id_doacao', $id)->first();

        if (!$doacao) {
            return redirect()->route('perfil.minhasDoacoes')->with('erro', 'Doação não encontrada.');
        }

        if ($doacao->fk_doacao_id_usuario !== Auth::id()) {
            abort(403, 'Acesso não autorizado.');
        }

        // Permite cancelar se estiver em Análise
        if ($doacao->status === 'Analise') {
            
            $doacao->update(['status' => 'Cancelada']); 

            return redirect()->route('perfil.minhasDoacoes')
                             ->with('sucesso', 'Proposta de doação cancelada com sucesso!');
        }

        return redirect()->route('perfil.minhasDoacoes')
                         ->with('erro', 'Esta doação já está sendo processada e não pode ser cancelada.');
    }

    public function cancelarReserva($id)
    {
        $compra = Compra::where('id_compra', $id)->first();

        if (!$compra) {
            return redirect()->route('perfil.minhasReservas')->with('erro', 'Reserva não encontrada.');
        }

        if ($compra->fk_compra_id_usuario !== Auth::id()) {
            abort(403, 'Acesso não autorizado.');
        }

        if ($compra->status === 'Reservado' || $compra->status === 'Carrinho') {
            
            // Se existirem itens na tabela intermediária vinculados a esta compra, mude o status deles
            ProdutoReserva::where('fk_id_compra', $compra->id_compra)->update(['status' => 'Cancelado']);
            
            // Devolve os produtos para o estado Disponível
            $itens = ProdutoReserva::where('fk_id_compra', $compra->id_compra)->get();
            foreach($itens as $item) {
                Produto::where('id_produto', $item->fk_id_produto)->update(['status' => 'Disponível']);
            }

            // Cancela a compra principal
            $compra->update(['status' => 'Cancelada']); 

            return redirect()->route('perfil.minhasReservas')
                             ->with('sucesso', 'Reserva cancelada com sucesso e itens devolvidos ao acervo!');
        }

        return redirect()->route('perfil.minhasReservas')
                         ->with('erro', 'Esta reserva já foi processada e não pode ser cancelada.');
    }   
}