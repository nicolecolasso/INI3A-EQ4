<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Compra;
use App\Models\Produto;
use Illuminate\Support\Facades\Auth;

class CarrinhoController extends Controller
{
    public function carrinho()
    {
        // Obtém apenas as compras pendentes do usuário autenticado
        $reservas = Compra::with('produtos')
            ->where('fk_compra_id_usuario', Auth::id())
            ->where('status', 'Carrinho')
            ->get();

        return view('carrinho.carrinho', compact('reservas'));
    }

    public function finalizar(Request $request, $id_compra)
    {
        // Garante que a compra existe E pertence ao usuário logado
        $compra = Compra::where('id_compra', $id_compra)
            ->where('fk_compra_id_usuario', Auth::id())
            ->where('status', 'Carrinho')
            ->firstOrFail();

        // Passa a compra para o status de "Reservado" no fluxo do brechó
        $compra->update([
            'status' => 'Reservado',
            'data_compra' => now()
        ]);

        // Carrega as peças vinculadas para mudar o status de estoque delas de 'Carrinho' para 'Reservado'
        $produtosId = $compra->produtos()->pluck('id_produto')->toArray();
        Produto::whereIn('id_produto', $produtosId)->update(['status' => 'Reservado']);

        return redirect()->route('carrinho.conclusaoReserva')->with('sucesso', 'Sua reserva foi efetuada com sucesso!');
    }

    public function conclusaoReserva()
    {
        // Captura o modelo do usuário logado na sessão de forma segura
        $cliente = Auth::user();

        // Busca as compras que mudaram recentemente para "Reservado" deste usuário
        $produtosReservados = Compra::with('produtos')
            ->where('fk_compra_id_usuario', $cliente->id)
            ->where('status', 'Reservado')
            ->orderBy('data_compra', 'desc')
            ->get();

        return view('conclusaoReserva', compact('cliente', 'produtosReservados'));
    }

    public function remover($id_produto)
    {
        // Busca a compra do tipo Carrinho do usuário logado
        $compra = Compra::where('fk_compra_id_usuario', Auth::id())
            ->where('status', 'Carrinho')
            ->first();

        if ($compra) {
            // Desvincula da tabela pivô
            $compra->produtos()->detach($id_produto);
            
            // 🔥 GARANTIA DO ENUM: Retorna o status do produto para 'Disponível'
            // Assim ele volta a aparecer imediatamente na vitrine e no Index.
            Produto::where('id_produto', $id_produto)->update(['status' => 'Disponível']);

            // Se o carrinho ficou completamente vazio, removemos a compra pai limpa
            if ($compra->produtos()->count() === 0) {
                $compra->delete();
            }
        }

        return redirect()->route('carrinho')->with('sucesso', 'Item removido do seu carrinho.');
    }

    public function adicionar($id_produto)
    {
        // 1. Verifica se o produto existe e se está REALMENTE "Disponível"
        $produto = Produto::where('id_produto', $id_produto)
            ->where('status', 'Disponível')
            ->where('excluido', false) // 💡 Segurança extra: não deixa adicionar o que foi deletado
            ->firstOrFail();

        // 2. Procura por um carrinho ativo ("Carrinho") para o utilizador logado
        $compra = Compra::where('fk_compra_id_usuario', Auth::id())
            ->where('status', 'Carrinho')
            ->first();

        // 3. Se não existir nenhum carrinho ativo, cria um novo
        if (!$compra) {
            $compra = Compra::create([
                'fk_compra_id_usuario' => Auth::id(),
                'status'               => 'Carrinho',
                'data_compra'          => now()
            ]);
        }

        // 4. Vincula o produto ao carrinho na tabela pivô (se já não estiver lá)
        if (!$compra->produtos()->where('fk_id_produto', $id_produto)->exists()) {
            $compra->produtos()->attach($id_produto);
            
            // Atualiza o status do produto para 'Carrinho'
            $produto->update(['status' => 'Carrinho']);
        }

        return redirect()->route('carrinho')->with('sucesso', 'Produto adicionado ao carrinho!');
    }
}