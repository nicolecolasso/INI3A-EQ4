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
        // 1. Busca o produto pela chave primária (id_produto)
        $produto = Produto::findOrFail($id);

        // 2. Checa se este produto já está no carrinho ou reservado por alguém
        // Como a relação está na tabela ProdutoReserva, verificamos por ela!
        $jaReservado = ProdutoReserva::where('fk_id_produto', $id)
            ->whereIn('status', ['Carrinho', 'Reservado', 'Concluída'])
            ->exists();

        // Alternativa segura: Você também pode checar o status direto no modelo do Produto
        if ($jaReservado || in_array($produto->status, ['Carrinho', 'Reservado', 'Concluída'])) {
            return redirect()->back()->with('erro', 'Ops! Este produto já foi reservado por outro cliente.');
        }

        // 3. Cria o "cabeçalho" da compra atrelada ao usuário logado
        // Nota: Adicionei um valor padrão para a string 'sessao' exigida na sua migration
        $novaCompra = Compra::create([
            'fk_compra_id_usuario' => Auth::id(),
            'status'               => 'Carrinho',
            'sessao'               => session()->getId(), // Preenche o campo 'sessao' obrigatório
        ]);

        // 4. Cria o vínculo na tabela pivô/auxiliar (Onde a mágica acontece)
        // Usamos o ID gerado da compra. Caso seu modelo use 'id_compra' como chave primária, usamos ele.
        ProdutoReserva::create([
            'fk_id_produto' => $id,
            'fk_id_compra'  => $novaCompra->id_compra ?? $novaCompra->id,
            'status'        => 'Carrinho',
        ]);

        // 5. Atualiza o status do produto para 'Carrinho' para controle da vitrine
        Produto::where('id_produto', $id)->update(['status' => 'Carrinho']);

        // 6. Redireciona para o carrinho com mensagem de sucesso
        return redirect()->route('carrinho')->with('sucesso', 'Produto adicionado ao seu carrinho!');
    }

    public function remover($id)
    {
        // 1. Busca o vínculo na tabela intermediária para garantir que o produto pertence ao carrinho do usuário
        $reserva = ProdutoReserva::where('fk_id_produto', $id)
            ->whereHas('compra', function($query) {
                $query->where('fk_compra_id_usuario', Auth::id())
                    ->where('status', 'Carrinho');
            })
            ->first();

        if (!$reserva) {
            return redirect()->back()->with('erro', 'Produto não encontrado no seu carrinho.');
        }

        // Guarda o ID da compra antes de deletar o item para verificar depois se era o último item da compra
        $idCompra = $reserva->fk_id_compra;

        // 2. Remove o vínculo do produto específico na tabela auxiliar
        $reserva->delete();

        // 3. Atualiza o status do produto para 'Disponível' para que ele volte correndo para a vitrine
        Produto::where('id_produto', $id)->update(['status' => 'Disponível']);

        // 4. Verifica se ainda algum produto atrelado a essa mesma compra
        $qtdeItems = ProdutoReserva::where('fk_id_compra', $idCompra)->count();

        if ($qtdeItems === 0) {
            // Se não sobrou nenhum, a compra passa a ser 'Cancelada'
            Compra::where('id_compra', $idCompra)->update(['status' => 'Cancelada']);
        }

        // 5. Redireciona para o carrinho com mensagem de sucesso
        return redirect()->route('carrinho')->with('sucesso', 'Produto removido do seu carrinho!');
    }
}
