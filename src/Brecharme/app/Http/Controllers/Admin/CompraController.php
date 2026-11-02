<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Compra;
use App\Models\Produto;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Models\ProdutoReserva;

class CompraController extends Controller
{
    private function statusDerivados(string $statusCompra): array
    {
        return match ($statusCompra) {
            'Concluída' => ['produto' => 'Vendido',    'pivot' => 'Concluído'],
            'Cancelada' => ['produto' => 'Disponível', 'pivot' => 'Cancelado'],
            'Reservado' => ['produto' => 'Reservado',  'pivot' => 'Reservado'],
            default     => ['produto' => 'Carrinho',   'pivot' => 'Carrinho'],
        };
    }

    private function usuarioTemOutroCarrinho($idUsuario, $ignorarId = null): bool
    {
        return Compra::where('fk_compra_id_usuario', $idUsuario)
            ->where('status', 'Carrinho')
            ->when($ignorarId, fn ($q) => $q->where('id_compra', '!=', $ignorarId))
            ->exists();
    }

    public function reservas()
    {
        $linhas = Compra::with(['usuario', 'produtos'])->get();
        return view('admin.reservas.reservas', compact('linhas'));
    }

    public function buscar(Request $request){
        $query = Compra::with(['usuario', 'produtos']);

        if ($request->filled('termo')) {
            $termo = '%' . $request->input('termo') . '%';
            
            $query->whereHas('usuario', function ($q) use ($termo) {
                $q->where('name', 'ilike', $termo)
                  ->orWhere('email', 'ilike', $termo);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('data')) {
            $query->whereDate('data_compra', $request->input('data'));
        }

        $linhas = $query->orderBy('data_compra', 'desc')->get();

        return view('admin.reservas.reservas', compact('linhas'));
    }

    public function novaReserva()
    {
        $usuarios = User::where('excluido', false)->get();
        $produtos = Produto::where('status', 'Disponível')->get();
        return view('admin.reservas.novaReserva', compact('usuarios', 'produtos'));
    }

    public function salvar(Request $request)
    {
        $request->validate([
            'id_usuario' => 'required',
            'id_produto' => 'required|array',
            'status'     => 'required|in:Carrinho,Reservado,Concluída,Cancelada',
        ]);

        if ($request->status === 'Carrinho' && $this->usuarioTemOutroCarrinho($request->id_usuario)) {
            return back()->withInput()->with('erro', 'Este usuário já possui um carrinho ativo.');
        }

        $st = $this->statusDerivados($request->status);

        $compra = DB::transaction(function () use ($request, $st) {
            $compra = Compra::create([
                'fk_compra_id_usuario' => $request->id_usuario,
                'status'               => $request->status,
                'data_compra'          => now()
            ]);

            $vinculo = [];
            foreach ($request->id_produto as $idProd) {
                $vinculo[$idProd] = ['status' => $st['pivot']];
            }
            $compra->produtos()->sync($vinculo);

            Produto::whereIn('id_produto', $request->id_produto)
                ->update(['status' => $st['produto']]);

            return $compra;
        });

        $usuario = User::find($request->id_usuario);
        $nomesProdutos = Produto::whereIn('id_produto', $request->id_produto)->pluck('nome')->implode(', ');

        $this->notificarAdminsNovaReserva($compra, $usuario);
        $this->confirmarReservaUsuario($compra, $usuario, $nomesProdutos);

        return redirect()->route('admin.reservas')->with('sucesso', 'Reserva criada com sucesso!');
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

        /**
     * Classifica os itens que já pertenceram à compra.
     * ativos        = vínculo ativo (pivot != Cancelado)
     * recuperaveis  = vínculo cancelado, mas o produto está Disponível
     * comprometidos = vínculo cancelado e produto vendido/em outro carrinho/reservado/excluído
     */
    private function estadoItens(Compra $compra): array
    {
        $grupos = $compra->itens()->with('produto')->get()->groupBy('fk_id_produto');

        $ativos = [];
        $recuperaveis = [];
        $comprometidos = [];

        foreach ($grupos as $idProd => $linhas) {
            $produto = $linhas->first()->produto;
            if (!$produto) {
                continue;
            }

            if ($linhas->contains(fn ($i) => $i->status !== 'Cancelado')) {
                $ativos[] = (int) $idProd;
            } elseif ($produto->status === 'Disponível' && !$produto->excluido) {
                $recuperaveis[] = (int) $idProd;
            } else {
                $comprometidos[] = [
                    'nome'   => $produto->nome,
                    'valor'  => $produto->valor,
                    'motivo' => match (true) {
                        (bool) $produto->excluido          => 'Produto excluído do catálogo',
                        $produto->status === 'Vendido'     => 'Já foi vendido',
                        $produto->status === 'Carrinho'    => 'Está no carrinho de outro cliente',
                        $produto->status === 'Reservado'   => 'Está reservado para outro cliente',
                        default                            => 'Indisponível',
                    },
                ];
            }
        }

        $somenteLeitura = false;
        $motivo = null;

        if ($compra->status === 'Concluída') {
            $somenteLeitura = true;
            $motivo = 'Compra concluída: apenas visualização.';
        } elseif ($compra->status === 'Cancelada' && empty($recuperaveis)) {
            $somenteLeitura = true;
            $motivo = 'Todos os itens desta compra já estão comprometidos (vendidos, em outro carrinho/reserva ou excluídos). Ela não pode ser reativada.';
        }

        return compact('ativos', 'recuperaveis', 'comprometidos', 'somenteLeitura', 'motivo');
    }

    private function cancelarItensAtivos(Compra $compra): void
    {
        $ids = $compra->itens()->where('status', '!=', 'Cancelado')->pluck('fk_id_produto');

        $compra->itens()->where('status', '!=', 'Cancelado')->update(['status' => 'Cancelado']);
        Produto::whereIn('id_produto', $ids)->update(['status' => 'Disponível']);
    }

    public function editarReserva($id)
    {
        $linha = Compra::findOrFail($id);
        $e = $this->estadoItens($linha);

        // Selecionados no formulário: itens ativos; se a compra está cancelada,
        // os recuperáveis já vêm marcados (o admin pode desmarcar).
        $idsDaCompra = $e['ativos'];
        if ($linha->status === 'Cancelada') {
            $idsDaCompra = array_merge($idsDaCompra, $e['recuperaveis']);
        }

        if ($e['somenteLeitura']) {
            $produtos = Produto::whereIn('id_produto', $e['ativos'])->get();
        } else {
            $produtos = Produto::where('excluido', false)
                ->where(function ($q) use ($e) {
                    $q->where('status', 'Disponível')
                      ->orWhereIn('id_produto', $e['ativos']);
                })
                ->orderBy('nome')
                ->get();
        }

        $usuarios = User::where('excluido', false)
            ->orWhere('id', $linha->fk_compra_id_usuario)
            ->get();

        return view('admin.reservas.editarReserva', [
            'linha'          => $linha,
            'usuarios'       => $usuarios,
            'produtos'       => $produtos,
            'idsDaCompra'    => $idsDaCompra,
            'recuperaveis'   => $e['recuperaveis'],
            'comprometidos'  => $e['comprometidos'],
            'somenteLeitura' => $e['somenteLeitura'],
            'motivoLeitura'  => $e['motivo'],
        ]);
    }

    public function cancelar($id)
    {
        $compra = Compra::findOrFail($id);

        if (in_array($compra->status, ['Concluída', 'Cancelada'])) {
            return redirect()->route('admin.reservas')->with('erro', 'Esta reserva já foi finalizada.');
        }

        DB::transaction(function () use ($compra) {
            $this->cancelarItensAtivos($compra);
            $compra->update(['status' => 'Cancelada']);
        });

        return redirect()->route('admin.reservas')->with('sucesso', 'Reserva cancelada com sucesso!');
    }

    public function atualizar(Request $request, $id)
    {
        $request->validate([
            'id_usuario' => 'required',
            'id_produto' => 'required_unless:status,Cancelada|array',
            'status'     => 'required|in:Carrinho,Reservado,Concluída,Cancelada',
        ]);

        $compra = Compra::findOrFail($id);
        $e = $this->estadoItens($compra);

        if ($e['somenteLeitura']) {
            return redirect()->route('admin.reservas')->with('erro', $e['motivo']);
        }

        if ($request->status === 'Carrinho'
            && $this->usuarioTemOutroCarrinho($request->id_usuario, $compra->id_compra)) {
            return back()->withInput()->withErrors(['status' => 'Este usuário já possui outro carrinho ativo.']);
        }

        $st = $this->statusDerivados($request->status);
        $selecionados = array_values(array_unique(array_map('intval', $request->input('id_produto', []))));

        DB::transaction(function () use ($request, $compra, $e, $st, $selecionados) {

            if ($request->status === 'Cancelada') {
                $this->cancelarItensAtivos($compra);
            } else {
                // Trava os produtos selecionados para evitar corrida com o carrinho do cliente
                $produtos = Produto::whereIn('id_produto', $selecionados)
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id_produto');

                foreach ($selecionados as $idProd) {
                    $p = $produtos->get($idProd);
                    $jaNaCompra = in_array($idProd, $e['ativos'], true);

                    if (!$p || (!$jaNaCompra && ($p->status !== 'Disponível' || $p->excluido))) {
                        throw ValidationException::withMessages([
                            'id_produto' => 'O produto "' . ($p->nome ?? $idProd)
                                . '" não está mais disponível. Recarregue a página e tente novamente.',
                        ]);
                    }
                }

                // 1. Removidos: cancela o vínculo e libera o produto
                $removidos = array_diff($e['ativos'], $selecionados);
                if (!empty($removidos)) {
                    $compra->itens()
                        ->whereIn('fk_id_produto', $removidos)
                        ->where('status', '!=', 'Cancelado')
                        ->update(['status' => 'Cancelado']);

                    Produto::whereIn('id_produto', $removidos)->update(['status' => 'Disponível']);
                }

                // 2. Mantidos, recuperados ou novos: reaproveita a linha da pivot
                foreach ($selecionados as $idProd) {
                    ProdutoReserva::updateOrCreate(
                        ['fk_id_compra' => $compra->id_compra, 'fk_id_produto' => $idProd],
                        ['status' => $st['pivot']]
                    );
                }

                // 3. Status dos produtos na vitrine
                Produto::whereIn('id_produto', $selecionados)->update(['status' => $st['produto']]);
            }

            // 4. Compra
            $compra->update([
                'fk_compra_id_usuario' => $request->id_usuario,
                'status'               => $request->status,
            ]);
        });

        return redirect()->route('admin.reservas')->with('sucesso', 'Reserva e estoque atualizados com sucesso!');
    }

    public function avancar($id)
    {
        $compra = Compra::findOrFail($id);

        $proximo = match ($compra->status) {
            'Carrinho'  => 'Reservado',
            'Reservado' => 'Concluída',
            default     => null,
        };

        if (!$proximo) {
            return redirect()->route('admin.reservas')->with('erro', 'Esta reserva já foi finalizada.');
        }

        $st = $this->statusDerivados($proximo);

        $ids = $compra->itens()->where('status', '!=', 'Cancelado')->pluck('fk_id_produto');

        if ($ids->isEmpty()) {
            return redirect()->route('admin.reservas')->with('erro', 'Esta compra não possui itens ativos.');
        }

        DB::transaction(function () use ($compra, $proximo, $st, $ids) {
            $compra->itens()->where('status', '!=', 'Cancelado')->update(['status' => $st['pivot']]);
            Produto::whereIn('id_produto', $ids)->update(['status' => $st['produto']]);

            $dados = ['status' => $proximo];
            if ($proximo === 'Reservado') {
                $dados['data_compra'] = now();
            }
            $compra->update($dados);
        });

        return redirect()->route('admin.reservas')->with(
            'sucesso',
            $proximo === 'Reservado' ? 'Compra marcada como Reservada.' : 'Reserva concluída com sucesso!'
        );
    }
}