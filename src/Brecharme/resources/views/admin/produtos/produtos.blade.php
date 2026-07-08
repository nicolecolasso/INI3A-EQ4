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

    <div class="admin-filters-wrapper">
        <form action="{{ route('admin.produtos.buscar') }}" method="GET" class="filters-form">
    
            <div class="filter-field">
                <label for="filter-nome">Nome</label>
                <div class="filter-input-icon">
                    <i class="material-icons">search</i>
                    <input type="text" name="nome" id="filter-nome" value="{{ request('nome') }}" placeholder="Buscar por nome...">
                </div>
            </div>

            <div class="filter-field">
                <label for="filter-categoria">Categoria</label>
                <div class="filter-input-icon">
                    <i class="material-icons">category</i>
                    <select name="categoria" id="filter-categoria">
                        <option value="">Todas as categorias</option>
                        @foreach($categorias as $cat)
                            <option value="{{ $cat->id_categoria }}" {{ request('categoria') == $cat->id_categoria ? 'selected' : '' }}>
                                {{ $cat->nome }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="filter-field">
                <label for="filter-status">Status</label>
                <div class="filter-input-icon">
                    <i class="material-icons">shopping_bag</i>
                    <select name="status" id="filter-status">
                        <option value="">Todos os status</option>
                        <option value="Disponível" {{ request('status') === 'Disponível' ? 'selected' : '' }}>Disponível</option>
                        <option value="Carrinho" {{ request('status') === 'Carrinho' ? 'selected' : '' }}>No Carrinho</option>
                        <option value="Reservado" {{ request('status') === 'Reservado' ? 'selected' : '' }}>Reservado</option>
                        <option value="Vendido" {{ request('status') === 'Vendido' ? 'selected' : '' }}>Vendido</option>
                    </select>
                </div>
            </div>

            <div class="filter-field">
                <label for="filter-excluido">Visibilidade</label>
                <div class="filter-input-icon">
                    <i class="material-icons">visibility</i>
                    <select name="excluido" id="filter-excluido">
                        <option value="">Todos</option>
                        <option value="ativo" {{ request('excluido') === 'ativo' ? 'selected' : '' }}>Apenas Ativos</option>
                        <option value="inativo" {{ request('excluido') === 'inativo' ? 'selected' : '' }}>Apenas Inativos</option>
                    </select>
                </div>
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn-filter-submit" title="Aplicar filtros">
                    <i class="material-icons">search</i> Filtrar
                </button>
                @if(request()->filled('nome') || request()->filled('categoria') || request()->filled('status') || request()->filled('excluido'))
                    <a href="{{ route('admin.produtos') }}" class="btn-filter-clear" title="Limpar Filtros">
                        <i class="material-icons">clear</i>
                    </a>
                @endif
            </div>
        </form>
    </div>

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
                        <td data-label="Categoria">{{ $linha->categoria->nome ?? 'Sem Categoria' }}</td>                        <td data-label="Preço">R$ {{ number_format($linha->valor, 2, ',', '.') }}</td>
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