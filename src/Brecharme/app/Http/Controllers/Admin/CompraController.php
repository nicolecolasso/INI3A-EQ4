<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Compra;
use App\Models\Produto;
use App\Models\User;

class CompraController extends Controller
{
    public function reservas()
    {
        $linhas = Compra::with(['usuario', 'produtos'])->get();
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
            'status'     => 'required'
        ]);

        $compra = Compra::create([
            'fk_compra_id_usuario' => $request->id_usuario,
            'status'               => $request->status,
            'data_compra'          => now()
        ]);

        $compra->produtos()->sync($request->id_produto);

        // Atualiza o status dos produtos inseridos baseado no status da compra
        $statusProduto = $request->status == 'Concluída' ? 'Vendido' : ($request->status == 'Carrinho' ? 'Carrinho' : 'Reservado');
        Produto::whereIn('id_produto', $request->id_produto)->update(['status' => $statusProduto]);

        return redirect()->route('admin.reservas')->with('sucesso', 'Reserva criada com sucesso!');
    }

    public function editarReserva($id)
    {
        $linha = Compra::with('produtos')->findOrFail($id);
        $usuarios = User::all();

        // Produtos que estão disponíveis ou que pertencem a esta compra específica
        $produtos = Produto::where('status', 'Disponível')
            ->orWhereHas('compras', function ($query) use ($id) {
                $query->where('compras.id_compra', $id);
            })
            ->get();

        return view('admin.reservas.editarReserva', compact('linha', 'usuarios', 'produtos'));
    }

    public function atualizar(Request $request, $id)
    {
        $request->validate([
            'id_usuario' => 'required',
            'id_produto' => 'required|array',
            'status'     => 'required'
        ]);

        $compra = Compra::findOrFail($id);
        
        // 1. Identificar produtos previamente associados
        $produtosAntigos = $compra->produtos()->pluck('id_produto')->toArray();
        $novosProdutos = $request->input('id_produto', []);
        
        // 2. Sincronizar relacionamento na tabela intermediária
        $compra->produtos()->sync($novosProdutos);
        
        // 3. Devolver produtos removidos  (Voltam a ficar Disponíveis)
        $removidos = array_diff($produtosAntigos, $novosProdutos);
        if (!empty($removidos)) {
            Produto::whereIn('id_produto', $removidos)->update(['status' => 'Disponível']);
        }
        
        // 4. Atualizar o estoque dos itens que permaneceram ou entraram o catálogo
        if (!empty($novosProdutos)) {
            
            if ($request->status == 'Concluída') {
                $statusProduto = 'Vendido';
            } elseif ($request->status == 'Cancelada') {
                $statusProduto = 'Disponível';
            } else {
                $statusProduto = $request->status == 'Carrinho' ? 'Carrinho' : 'Reservado';
            }

            Produto::whereIn('id_produto', $novosProdutos)->update(['status' => $statusProduto]);
        }
        
        $compra->update([
            'fk_compra_id_usuario' => $request->id_usuario,
            'status'               => $request->status
        ]);
        
        return redirect()->route('admin.reservas')->with('sucesso', 'Reserva e estoque atualizados com sucesso!');
    }
}