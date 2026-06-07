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
                        <td data-label="ID">#{{ $linha->id_produto }}</td>
                        <td data-label="Foto">
                            <div class="product-img-thumbnail">
                                <img src="{{ asset($linha->caminho_img) }}" alt="{{ $linha->nome }}">
                            </div>
                        </td>
                        <td data-label="Nome">
                            <strong>{{ $linha->nome }}</strong>
                            @if($linha->excluido)
                                <br><span class="text-danger-bold">(Desativado)</span>
                            @endif
                        </td>
                        <td data-label="Categoria">{{ $linha->categoria }}</td>
                        <td data-label="Preço">R$ {{ number_format($linha->valor, 2, ',', '.') }}</td>
                        <td data-label="Status">
                            @if($linha->excluido)
                                <span class="badge badge-arquivado">Inativo</span>
                            @else
                                <span class="badge badge-{{ strtolower($linha->status) }}">{{ $linha->status }}</span>
                            @endif
                        </td>
                        <td data-label="Ações">
                            <div class="action-buttons-flex">
                                
                                
                                @if(!$linha->excluido)
                                    <a href="{{ route('admin.produtos.editarProduto', $linha->id_produto) }}" class="btn-action edit" title="Editar Produto">
                                        <i class="material-icons">edit</i>
                                    </a>
                                    {{-- Botão de Desativar --}}
                                    <a href="{{ route('admin.produtos.excluir', $linha->id_produto) }}" 
                                       class="btn-action delete" 
                                       onclick="return confirm('Tem certeza que deseja desativar este produto do catálogo público?');" 
                                       title="Desativar Produto">
                                        <i class="material-icons">block</i>
                                    </a>
                                @else
                                    {{--  Botão de Reativar se o produto estiver oculto --}}
                                    <a href="{{ route('admin.produtos.ativar', $linha->id_produto) }}" 
                                       class="btn-action check" 
                                       onclick="return confirm('Deseja reativar este produto e torná-lo visível na vitrine novamente?');" 
                                       title="Reativar Produto / Voltar para Vitrine">
                                        <i class="material-icons">settings_backup_restore</i>
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="empty-table">Nenhum produto cadastrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection