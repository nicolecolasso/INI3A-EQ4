<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Compra;
use App\Models\ProdutoReserva;
use App\Models\Produto;
use App\Models\User;

class CompraController extends Controller
{
    public function reservas()
    {
        $linhas = Compra::with(['usuario'])
            ->orderByRaw("
                CASE status
                    WHEN 'Reservado' THEN 1
                    WHEN 'Carrinho' THEN 2
                    WHEN 'Concluída' THEN 3
                    WHEN 'Cancelada' THEN 4
                    ELSE 5
                END ASC
            ")
            ->orderBy('data_compra', 'desc')
            ->get();
        return view('admin.reservas.reservas', compact('linhas'));
    }

    public function listaReservas()
    {
        $linhas = Compra::all();
        return view('admin.reservas.listaReservas', compact('linhas'));
    }

    public function novaReserva()
    {
        $produtos = Produto::where('status', 'Disponível')->get();  
        $usuarios = User::all();
        return view('admin.reservas.novaReserva', compact('produtos', 'usuarios'));
    }

    public function salvar(Request $req)
    {
        $compra = Compra::create([
            'status'               => $req->status,
            'sessao'               => $req->sessao ?? session()->getId(),
            'data_compra'          => now(),
            'fk_compra_id_usuario' => $req->id_usuario
        ]);

        if ($req->has('id_produto')) {
            foreach ($req->id_produto as $idProduto) {
                $statusIntermediaria = ($req->status == 'Cancelada') ? 'Cancelado' : $req->status;

                ProdutoReserva::create([
                    'fk_id_produto' => $idProduto,
                    'fk_id_compra'  => $compra->id_compra,
                    'status'        => $statusIntermediaria
                ]);

                if ($req->status == 'Cancelada') {
                    $novoStatusProduto = 'Disponível';
                } elseif ($req->status == 'Concluída') {
                    $novoStatusProduto = 'Vendido';
                } else {
                    $novoStatusProduto = 'Reservado';
                }

                Produto::where('id_produto', $idProduto)->update(['status' => $novoStatusProduto]);
            }
        }

        return redirect()->route('admin.reservas')->with('sucesso', 'Reserva criada com sucesso!');
    }

    public function editarReserva($id)
    {
        $linha = Compra::with('itens')->find($id);
        $usuarios = User::all();
        $produtos = Produto::where('status', 'Disponível')
            ->orWhereHas('reservas', function($q) use ($id) {
                $q->where('fk_id_compra', $id);
            })->get();

        return view('admin.reservas.editarReserva', compact('linha', 'usuarios', 'produtos'));
    }

    public function atualizar(Request $req, $id)
    {
        $compra = Compra::findOrFail($id);
        
        // 1. Descobrir quais os produtos estavam originalmente associados a esta compra antes da edição
        $produtosAntigos = ProdutoReserva::where('fk_id_compra', $id)->pluck('fk_id_produto')->toArray();
        $produtosNovos = $req->input('id_produto', []);

        // 2. Se algum produto foi REMOVIDO da lista pelo administrador, ele volta a ficar Disponível na vitrine imediatamente
        $produtosRemovidos = array_diff($produtosAntigos, $produtosNovos);
        if (!empty($produtosRemovidos)) {
            Produto::whereIn('id_produto', $produtosRemovidos)->update(['status' => 'Disponível']);
        }

        // 3. Atualiza os dados principais da compra mestre
        $compra->update([
            'status'               => $req->status,
            'fk_compra_id_usuario' => $req->id_usuario,
            'sessao'               => $req->sessao ?? $compra->sessao
        ]);

        // 4. Limpa os vínculos antigos na pivot para reconstruir o estado atualizado
        ProdutoReserva::where('fk_id_compra', $id)->delete();

        // 5. Varre a nova lista redefinindo as amarrações de estoque corretas
        foreach ($produtosNovos as $idProduto) {
            if ($req->status == 'Concluída') {
                $statusIntermediaria = 'Concluída';
                $novoStatusProduto = 'Vendido';
            } elseif ($req->status == 'Cancelada') {
                $statusIntermediaria = 'Cancelado';
                $novoStatusProduto = 'Disponível';
            } else {
                $statusIntermediaria = $req->status; // 'Carrinho' ou 'Reservado'
                $novoStatusProduto = 'Reservado';
            }

            ProdutoReserva::create([
                'fk_id_produto' => $idProduto,
                'fk_id_compra'  => $id,
                'status'        => $statusIntermediaria
            ]);

            Produto::where('id_produto', $idProduto)->update(['status' => $novoStatusProduto]);
        }

        return redirect()->route('admin.reservas')->with('sucesso', 'Reserva e sincronismo de estoque atualizados perfeitamente!');
    }
}