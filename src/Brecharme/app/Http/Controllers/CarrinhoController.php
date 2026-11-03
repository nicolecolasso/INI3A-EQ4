<?php

namespace App\Http\Controllers;

use App\Models\ProdutoReserva;
use App\Models\Compra;
use App\Models\Produto;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class CarrinhoController extends Controller
{
    public function carrinho()
    {
        $reservas = Compra::with(['produtos' => function ($query) {
                $query->wherePivot('status', 'Carrinho');
            }])
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

        // Só os itens que continuam no carrinho (ignora os cancelados)
        $produtosId = $compra->produtos()
            ->wherePivot('status', 'Carrinho')
            ->pluck('id_produto')
            ->toArray();

        // Se não sobrou nada, não há o que reservar
        if (empty($produtosId)) {
            return redirect()->route('carrinho')->with('erro', 'Seu carrinho está vazio.');
        }

        DB::transaction(function () use ($compra, $produtosId) {
            $compra->update([
                'status'      => 'Reservado',
                'data_compra' => now()
            ]);

            // Atualiza o status na tabela intermediária
            foreach ($produtosId as $id) {
                $compra->produtos()->updateExistingPivot($id, ['status' => 'Reservado']);
            }

            // Atualiza o status na tabela de produtos
            Produto::whereIn('id_produto', $produtosId)->update(['status' => 'Reservado']);
        });

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
        $produtosReservados = Compra::with(['produtos' => function ($q) {
            $q->wherePivot('status', 'Reservado');
        }])
        ->where('fk_compra_id_usuario', $cliente->id)
        ->where('status', 'Reservado')
        ->orderBy('data_compra', 'desc')
        ->get();

        return view('carrinho.conclusaoReserva', compact('cliente', 'produtosReservados'));
    }

    public function remover($id_produto)
    {
        $item = ProdutoReserva::where('fk_id_produto', $id_produto)
            ->where('status', 'Carrinho')
            ->whereHas('compra', function ($q) {
                $q->where('fk_compra_id_usuario', Auth::id())
                ->where('status', 'Carrinho');
            })
            ->first();

        if ($item) {
            DB::transaction(function () use ($item) {
                $item->update(['status' => 'Cancelado']);

                Produto::where('id_produto', $item->fk_id_produto)
                    ->update(['status' => 'Disponível']);

                $restantes = ProdutoReserva::where('fk_id_compra', $item->fk_id_compra)
                    ->where('status', 'Carrinho')
                    ->count();

                if ($restantes === 0) {
                    Compra::where('id_compra', $item->fk_id_compra)
                        ->update(['status' => 'Cancelada']);
                }
            });
        }

        return redirect()->route('carrinho')->with('sucesso', 'Item removido do seu carrinho.');
    }

    public function adicionar($id_produto)
    {
        $produto = Produto::where('id_produto', $id_produto)
            ->where('status', 'Disponível')
            ->where('excluido', false)
            ->firstOrFail();

        $adicionado = DB::transaction(function () use ($produto, $id_produto) {
            $compra = Compra::firstOrCreate(
                ['fk_compra_id_usuario' => Auth::id(), 'status' => 'Carrinho'],
                ['data_compra' => now()]
            );

            $jaNoCarrinho = $compra->itens()
                ->where('fk_id_produto', $id_produto)
                ->where('status', 'Carrinho')
                ->exists();

            if ($jaNoCarrinho) {
                return false;
            }

            $compra->produtos()->attach($id_produto, ['status' => 'Carrinho']);
            $produto->update(['status' => 'Carrinho']);

            return true;
        });

        return $adicionado
            ? redirect()->route('carrinho')->with('sucesso', 'Produto adicionado ao carrinho!')
            : redirect()->route('carrinho')->with('erro', 'Este produto já está no seu carrinho.');
    }
}
