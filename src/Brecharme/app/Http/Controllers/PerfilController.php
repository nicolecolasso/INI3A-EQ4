<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\ProdutoReserva;
use App\Models\Doacao;
use App\Models\User;
use App\Http\Controllers\Controller;


class PerfilController extends Controller
{
    /**
     * Exibe o painel principal do perfil do usuário (GET /perfil/meuPerfil)
     */
    public function meuPerfil()
    {
        $usuario = User::findOrFail(Auth::id());

        $totalDoacoes = $usuario->doacoes()->where('status', 'Aprovado')->count();

        $totalReservas = $usuario->compras()->where('status', 'Concluída')->count();

        // Envia o usuário e os dois contadores para a View
        return view('perfil.meuPerfil', compact('usuario', 'totalDoacoes', 'totalReservas'));
    }

    /**
     * Exibe a tela com o formulário de dados pessoais (GET /perfil/meusDados)
     */
    public function meusDados()
    {
        $usuario = User::findOrFail(Auth::id());
        return view('perfil.meusDados', compact('usuario'));
    }

    /**
     * Processa a atualização dos dados e/ou troca de senha (POST /perfil/atualizarDados)
     * Opcional: Adicione esta rota se quiser salvar as alterações da tela "Meus Dados"
     */
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

    /**
     * Lista o histórico de doações feitas pelo usuário (GET /perfil/minhasDoacoes)
     */
    public function minhasDoacoes()
    {
        $usuario = User::findOrFail(Auth::id());
        $doacoes = $usuario->doacoes; 
        return view('perfil.minhasDoacoes', compact('doacoes'));
    }

    /**
     * Lista o histórico de reservas feitas pelo usuário (GET /perfil/minhasReservas)
     */
    public function minhasReservas()
    {
        // O with(['itens.produto']) traz a tabela pivot e os produtos de uma só vez
        $compras = \App\Models\Compra::with(['itens.produto'])
            ->where('fk_compra_id_usuario', Auth::id())
            ->orderBy('id_compra', 'desc')
            ->get();

        return view('perfil.minhasReservas', compact('compras'));
    }

    /**
     * Cancela uma doação em análise (GET /perfil/minhasDoacoes/cancelar/{id})
     */
    public function cancelarDoacao($id)
    {
        // IMPORTANTE: Se o seu banco usa "id_doacao", mude o 'find($id)' para: where('id_doacao', $id)->first()
        $doacao = Doacao::find($id);

        if (!$doacao) {
            return redirect()->route('perfil.minhasDoacoes')->with('erro', 'Doação não encontrada.');
        }

        if ($doacao->fk_doacao_id_usuario !== Auth::id()) {
            abort(403, 'Acesso não autorizado.');
        }

        // Permite cancelar se estiver em Análise
        if ($doacao->status === 'Analise') {
            
            // 🛠️ EM VEZ DE DELETAR, SALVAMOS O STATUS COMO CANCELADA
            $doacao->update(['status' => 'Cancelada']); 

            return redirect()->route('perfil.minhasDoacoes')
                             ->with('sucesso', 'Proposta de doação cancelada com sucesso!');
        }

        return redirect()->route('perfil.minhasDoacoes')
                         ->with('erro', 'Esta doação já está sendo processada e não pode ser cancelada.');
    }

    /**
     * Cancela um item reservado (GET /perfil/minhasReservas/cancelar/{id})
     */
    public function cancelarReserva($id)
    {
        $compra = \App\Models\Compra::find($id);

        if (!$compra) {
            return redirect()->route('perfil.minhasReservas')->with('erro', 'Reserva não encontrada.');
        }

        if ($compra->fk_compra_id_usuario !== Auth::id()) {
            abort(403, 'Acesso não autorizado.');
        }

        if (strtolower($compra->status) === 'Reservado' || strtolower($compra->status) === 'Carrinho') {
            
            // Se existirem itens na tabela pivot vinculados a esta compra, mude o status deles ou remova-os
            \App\Models\ProdutoReserva::where('fk_id_compra', $compra->id_compra)->update(['status' => 'Cancelado']);
            
            // Devolve os produtos para o estado Disponível
            $itens = \App\Models\ProdutoReserva::where('fk_id_compra', $compra->id_compra)->get();
            foreach($itens as $item) {
                \App\Models\Produto::where('id_produto', $item->fk_id_produto)->update(['status' => 'Disponível']);
            }

            // Cancela ou deleta a compra mãe
            $compra->update(['status' => 'Cancelada']); 

            return redirect()->route('perfil.minhasReservas')
                             ->with('sucesso', 'Reserva cancelada com sucesso e itens devolvidos ao acervo!');
        }

        return redirect()->route('perfil.minhasReservas')
                         ->with('erro', 'Esta reserva já foi processada e não pode ser cancelada.');
    }   
}