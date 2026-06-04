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
            ->orderBy('data_compra', 'desc') // Desempata pelas movimentações mais recentes
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
        $produtos = \App\Models\Produto::where('status', 'Disponível')->get();  
        $usuarios = \App\Models\User::all();
        return view('admin.reservas.novaReserva', compact('produtos', 'usuarios'));
    }

    public function salvar(Request $req)
    {

        // 1. Cria a Compra 
        $compra = Compra::create([
            'status'               => $req->status,
            'sessao'               => $req->sessao,
            'fk_compra_id_usuario' => $req->id_usuario, 
            'data_compra'          => now()
        ]);

        // 2. Vincula cada produto selecionado na tabela intermediária)
        $produtosSelecionados = $req->input('id_produto', []); // Recebe o array do select multiple
        
        foreach ($produtosSelecionados as $idProduto) {
            \App\Models\ProdutoReserva::create([
                'fk_id_produto' => $idProduto,
                'fk_id_compra'  => $compra->id_compra, 
                'status'        => $req->status
            ]);
            
            \App\Models\Produto::where('id_produto', $idProduto)->update(['status' => 'Reservado']);
        }
        
        return redirect()->route('admin.reservas')->with('sucesso', 'Reserva criada com sucesso!');
    }

    public function editarReserva($id)
    {
        $linha = Compra::with('usuario')->find($id);

        $produtos = \App\Models\Produto::where('status', 'Disponível')
            ->orWhereIn('id_produto', function($query) use ($id) {
                $query->select('fk_id_produto')
                      ->from('produto_reserva')
                      ->where('fk_id_compra', $id);
            })->get();

        $usuarios = \App\Models\User::all(); 

        return view('admin.reservas.editarReserva', compact('linha', 'produtos', 'usuarios'));
    }

    public function atualizar(Request $req, $id)
    {
        // 1. Busca a compra antes de atualizar para saber o que ela tinha antes
        $compra = Compra::find($id);
        
        // Captura todos os IDs de produtos que faziam parte desta compra antes da edição
        $produtosAntigos = \App\Models\ProdutoReserva::where('fk_id_compra', $id)
            ->pluck('fk_id_produto')
            ->toArray();

        // 2. Atualiza os dados da compra (Status e Usuário)
        $compra->update([
            'status'               => $req->status,
            'fk_compra_id_usuario' => $req->id_usuario,
            'sessao'               => $req->sessao,
        ]);

        // 3. Pega a nova lista de produtos selecionados no formulário
        $produtosSelecionados = $req->input('id_produto', []); 

        // Descobrir quais produtos foram REMOVIDOS do select na edição
        // Itens que estavam antes na compra, mas não estão na nova lista selecionada
        $produtosRemovidos = array_diff($produtosAntigos, $produtosSelecionados);
        
        if (!empty($produtosRemovidos)) {
            // Se o produto saiu da reserva, ele volta a ficar "Disponível" no estoque!
            \App\Models\Produto::whereIn('id_produto', $produtosRemovidos)->update(['status' => 'Disponível']);
        }

        // 4. Limpa os vínculos antigos na tabela intermediária para reconstruir com a nova seleção
        \App\Models\ProdutoReserva::where('fk_id_compra', $id)->delete();

        // 5. Mapeia e atualiza o status de cada produto que ficou/entrou na compra
        foreach ($produtosSelecionados as $idProduto) {
            if ($req->status == 'Concluída') {
                $statusIntermediaria = 'Concluído';
            } elseif ($req->status == 'Cancelada') {
                $statusIntermediaria = 'Cancelado'; // Envia no masculino para bater com seu ENUM revisado
            } else {
                $statusIntermediaria = $req->status; // 'Carrinho' ou 'Reservado'
            }

            // Recria o vínculo na tabela intermediária com o termo perfeitamente aceito
            \App\Models\ProdutoReserva::create([
                'fk_id_produto' => $idProduto,
                'fk_id_compra'  => $id,
                'status'        => $statusIntermediaria
            ]);

            // CONTROLE DE RETORNO DO ESTOQUE (produto)
            if ($req->status == 'Cancelada') {
                // Se a compra foi cancelada, o produto volta a ficar "Disponível" imediatamente
                $novoStatusProduto = 'Disponível';
            } elseif ($req->status == 'Concluída') {
                // Se a compra foi finalizada, o produto foi "Vendido"
                $novoStatusProduto = 'Vendido';
            } else {
                // Se continuar como 'Reservado' ou 'Carrinho', o produto permanece 'Reservado'
                $novoStatusProduto = 'Reservado';
            }

            // Aplica a alteração de status diretamente no estoque do produto
            \App\Models\Produto::where('id_produto', $idProduto)->update(['status' => $novoStatusProduto]);
        }

        return redirect()->route('admin.reservas')->with('sucesso', 'Reserva e estoque atualizados com sucesso!');
    }

}
