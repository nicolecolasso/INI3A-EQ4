@extends('layout.site')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@endpush

@section('titulo', 'Produtos')

@section('conteudo')
<div class="admin-table-container">
    
    <header class="table-header-box">
        <h2>Gerenciamento de Produtos</h2>
        <a href="{{ route('admin.produtos.novoProduto') }}" class="btn-add-table">
            <i class="material-icons">add_shopping_cart</i> Novo Produto
        </a>
    </header>

    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Foto</th>
                    <th>Nome do Produto</th>
                    <th>Categoria</th>
                    <th>Preço</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($linhas as $linha)
                    <tr class="{{ $linha->excluido ? 'row-user-inactive' : '' }}">
                        <td>#{{ $linha->id_produto }}</td>
                        <td>
                            <div class="product-img-thumbnail">
                                <img src="{{ asset($linha->caminho_img ?? 'img/produto-placeholder.png') }}" alt="{{ $linha->nome }}">
                            </div>
                        </td>
                        <td>
                            <strong>{{ $linha->nome }}</strong>
                            <div style="font-size: 12px; color: #888; margin-top: 2px;" title="{{ $linha->descricao }}">
                                {{ Str::limit($linha->descricao, 40, '...') }}
                            </div>
                        </td>
                        <td>{{ $linha->categoria ?? 'Geral' }}</td>
                        <td class="product-price">R$ {{ number_format($linha->valor, 2, ',', '.') }}</td>
                        <td>
                            @if($linha->excluido)
                                <span class="badge badge-inactive">Inativo / Removido</span>
                            @else
                                <span class="badge badge-user">{{ $linha->status ?? 'Disponível' }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="action-buttons-flex">
                                <a href="{{ route('admin.produtos.editarProduto', $linha->id_produto) }}" class="btn-action edit" title="Editar Produto">
                                    <i class="material-icons">edit</i>
                                </a>
                                
                                @if(!$linha->excluido)
                                    <a href="{{ route('admin.produtos.excluir', $linha->id_produto) }}" 
                                       class="btn-action delete" 
                                       onclick="return confirm('Tem certeza que deseja desativar este produto?');" 
                                       title="Desativar Produto">
                                        <i class="material-icons">block</i>
                                    </a>
                                @else
                                    <button class="btn-action delete disabled" title="Produto já desativado" disabled style="opacity: 0.4; cursor: not-allowed;">
                                        <i class="material-icons">clear</i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="empty-table">Nenhum produto cadastrado no acervo do Brecharme.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection