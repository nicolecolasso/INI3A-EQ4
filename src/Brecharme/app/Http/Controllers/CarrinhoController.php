<?php

namespace App\Http\Controllers;

use App\Models\ProdutoReserva;
use App\Models\Compra;
use App\Models\Produto;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class CarrinhoController extends Controller
{
    public function carrinho()
    {
        $reservas = Compra::with('produtos')
            ->where('fk_compra_id_usuario', Auth::id())
            ->where('status', 'Carrinho')
            ->get();

        return view('carrinho.carrinho', compact('reservas'));
    }

    public function finalizar($id_compra)
    {
        $compra = Compra::where('id_compra', $id_compra)
            ->where('fk_compra_id_usuario', Auth::id())
            ->where('status', 'Carrinho')
            ->firstOrFail();

        $compra->update([
            'status' => 'Reservado',
            'data_compra' => now()
        ]);

        $produtosId = $compra->produtos()->pluck('id_produto')->toArray();
        $compra->produtos()->whereIn('id_produto', $produtosId)->update(['status' => 'Reservado']);
        Produto::whereIn('id_produto', $produtosId)->update(['status' => 'Reservado']);

        $usuario = Auth::user();
        $nomesProdutos = Produto::whereIn('id_produto', $produtosId)->pluck('nome')->implode(', ');

        $this->notificarAdminsNovaReserva($compra, $usuario);
        $this->confirmarReservaUsuario($compra, $usuario, $nomesProdutos);

        return redirect()->route('carrinho.conclusaoReserva')->with('sucesso', 'Sua reserva foi efetuada com sucesso!');
    }

    private function notificarAdminsNovaReserva(Compra $compra, ?User $usuario)
    {
        $admins = User::where('admin', true)->where('excluido', false)->pluck('email');

        if ($admins->isEmpty()) {
            return;
        }

        $nomeCliente = $usuario->name ?? 'Cliente';

        Mail::send([], [], function ($message) use ($admins, $compra, $nomeCliente) {
            $message->to($admins->toArray())
                    ->subject('Nova reserva registrada - Brechó')
                    ->html("<h3>Uma nova reserva foi registrada</h3>
                            <p><strong>Cliente:</strong> {$nomeCliente}</p>
                            <p><strong>Status:</strong> {$compra->status}</p>");
        });
    }

    private function confirmarReservaUsuario(Compra $compra, ?User $usuario, string $nomesProdutos)
    {
        if (!$usuario || !$usuario->receber_avisos) {
            return;
        }

        Mail::send([], [], function ($message) use ($usuario, $compra, $nomesProdutos) {
            $message->to($usuario->email)
                    ->subject('Confirmação de reserva - Brechó')
                    ->html("<h3>Olá, {$usuario->name}!</h3>
                            <p>Sua reserva foi registrada com sucesso.</p>
                            <p><strong>Itens:</strong> {$nomesProdutos}</p>
                            <p><strong>Status:</strong> {$compra->status}</p>");
        });
    }

    public function conclusaoReserva()
    {
        $cliente = Auth::user();

        // Busca as compras que mudaram recentemente para "Reservado" deste usuário
        $produtosReservados = Compra::with('produtos')
            ->where('fk_compra_id_usuario', $cliente->id)
            ->where('status', 'Reservado')
            ->orderBy('data_compra', 'desc')
            ->get();

        return view('carrinho.conclusaoReserva', compact('cliente', 'produtosReservados'));
    }

    public function remover($id_produto)
    {
        $compra = Compra::where('fk_compra_id_usuario', Auth::id())
            ->where('status', 'Carrinho')
            ->first();

        if ($compra) {
            // Atualiza a tabela intermediária
            $compra->produtos()->updateExistingPivotIds([$id_produto], ['status' => 'Cancelado']);            
            // Retorna o status do produto para 'Disponível'
            Produto::where('id_produto', $id_produto)->update(['status' => 'Disponível']);

            // Verifica se o carrinho ficou completamente vazio
            $itensAtivosNoCarrinho = ProdutoReserva::where('fk_id_compra', $compra->id_compra)
                ->where('status', 'Carrinho')
                ->count();

            // Se não restou nenhum item ativo no carrinho muda o status da compra
            if ($itensAtivosNoCarrinho === 0) {
                $compra->update(['status' => 'Cancelada']);
            }
        }

        return redirect()->route('carrinho')->with('sucesso', 'Item removido do seu carrinho.');
    }

    public function adicionar($id_produto)
    {
        $produto = Produto::where('id_produto', $id_produto)
            ->where('status', 'Disponível')
            ->where('excluido', false) 
            ->firstOrFail();

        // Procura por um carrinho ativo o usuário
        $compra = Compra::where('fk_compra_id_usuario', Auth::id())
            ->where('status', 'Carrinho')
            ->first();

        // Se não existir nenhum carrinho ativo, cria um novo
        if (!$compra) {
            $compra = Compra::create([
                'fk_compra_id_usuario' => Auth::id(),
                'status'               => 'Carrinho',
                'data_compra'          => now()
            ]);
        }

        // Verifica se o produto já não está ativo no carrinho atual
        $jaNoCarrinho = $compra->produtos()
            ->where('fk_id_produto', $id_produto)
            ->wherePivot('status', 'Carrinho')
            ->exists();


        if (!$jaNoCarrinho) {
            // Insere na tabela intermediária 
            $compra->produtos()->attach($id_produto, ['status' => 'Carrinho']);
            
            // Atualiza o produto principal
            $produto->update(['status' => 'Carrinho']);
            return redirect()->route('carrinho')->with('sucesso', 'Produto adicionado ao carrinho!');

        }
        return redirect()->route('carrinho')->with('erro', 'Este produto já está no seu carrinho.');


    }
}