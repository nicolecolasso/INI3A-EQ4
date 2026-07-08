@extends('layout.site')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@endpush

@section('titulo', 'Doações')

@section('conteudo')
<div class="admin-table-container">
    
    <header class="table-header-box">
        <h2>Gerenciamento de Doações</h2>
        <a href="{{ route('admin.doacoes.novaDoacao') }}" class="btn-add-table">
            <i class="material-icons">add_box</i> Nova Doação
        </a>
    </header>

    <div class="admin-filters-wrapper">
        <form action="{{ route('admin.doacoes.buscar') }}" method="GET" class="filters-form">

            <div class="filter-field">
                <label for="filter-termo">Item ou Doador</label>
                <div class="filter-input-icon">
                    <i class="material-icons">search</i>
                    <input type="text" name="termo" id="filter-termo" value="{{ request('termo') }}" placeholder="Nome do item ou doador...">
                </div>
            </div>

            <div class="filter-field">
                <label for="filter-status">Status</label>
                <div class="filter-input-icon">
                    <i class="material-icons">assignment</i>
                    <select name="status" id="filter-status">
                        <option value="">Todos os status</option>
                        <option value="Em Análise" {{ request('status') === 'Em Análise' ? 'selected' : '' }}>Em Análise</option>
                        <option value="Aprovada" {{ request('status') === 'Aprovada' ? 'selected' : '' }}>Aprovada</option>
                        <option value="Integrada ao Estoque" {{ request('status') === 'Integrada ao Estoque' ? 'selected' : '' }}>Integrada ao Estoque</option>
                        <option value="Recusada" {{ request('status') === 'Recusada' ? 'selected' : '' }}>Recusada</option>
                    </select>
                </div>
            </div>

            <div class="filter-field">
                <label for="filter-retirada">Retirada</label>
                <div class="filter-input-icon">
                    <i class="material-icons">local_shipping</i>
                    <select name="retirada" id="filter-retirada">
                        <option value="">Forma de recebimento</option>
                        <option value="sim" {{ request('retirada') === 'sim' ? 'selected' : '' }}>Necessita Retirada</option>
                        <option value="nao" {{ request('retirada') === 'nao' ? 'selected' : '' }}>Entrega no Brechó</option>
                    </select>
                </div>
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn-filter-submit" title="Filtrar Doações">
                    <i class="material-icons">search</i> Filtrar
                </button>
                @if(request()->filled('termo') || request()->filled('status') || request()->filled('retirada'))
                    <a href="{{ route('admin.doacoes') }}" class="btn-filter-clear" title="Limpar Filtros">
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
                    <th>Nome do Item</th>
                    <th>Doador</th> 
                    <th>Categoria</th>
                    <th>Data</th>
                    <th>Localização p/ Retirada</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($linhas as $linha)
                    {{-- Classes diferentes dependendo do status --}}
                    <tr class="{{ in_array($linha->status, ['Integrada ao Estoque', 'Recusada', 'Cancelada']) ? 'row-user-inactive' : '' }}">
                        <td data-label="ID">#{{ $linha->id_doacao }}</td>
                        <td data-label="Foto">
                            <div class="product-img-thumbnail">
                                <img src="{{ asset($linha->caminho_img) }}" alt="{{ $linha->nome }}">
                            </div>
                        </td>
                        <td data-label="Item"><strong>{{ $linha->nome }}</strong></td>
                        <td data-label="Doador">{{ $linha->usuario->name ?? 'Desconhecido' }}</td>
                        <td data-label="Categoria">{{ $linha->categoria->nome ?? 'Sem Categoria' }}</td>
                        <td data-label="Data">{{ \Carbon\Carbon::parse($linha->data_doacao)->format('d/m/Y') }}</td>
                        <td data-label="Localização">{{ $linha->localizacao ?? 'Entrega no Brechó' }}</td>
                        </td>
                        <td data-label="Status">
                            <span class="badge-status 
                                @if($linha->status === 'Em Análise') badge-analise
                                @elseif($linha->status === 'Aprovada') badge-aprovada
                                @elseif($linha->status === 'Integrada ao Estoque' || $linha->status === 'Cancelada') badge-inactive
                                @elseif($linha->status === 'Recusada') badge-recusado
                                @endif
                            ">
                                {{ $linha->status }}
                            </span>
                        </td>
                        <td data-label="Ações">
                            <div class="action-buttons-flex">
                                
                                {{-- Em Análise: Aceitar (Muda para Aprovada) ou Recusar --}}
                                @if($linha->status === 'Em Análise')
                                    <form action="{{ route('admin.doacoes.aceitar', $linha->id_doacao) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" 
                                                class="btn-action check" 
                                                style="border: none; background: lightgreen; cursor: pointer; padding: 0;"
                                                onclick="return confirm('Tem certeza que deseja aprovar esta doação?');"
                                                title="Aprovar Doação">
                                            <i class="material-icons">done</i>
                                        </button>
                                    </form>

                                    <form action="{{ route('admin.doacoes.rejeitar', $linha->id_doacao) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" 
                                                class="btn-action delete" 
                                                style="border: none; background: lightcoral; cursor: pointer; padding: 0;"
                                                onclick="return confirm('Tem certeza que deseja recusar esta doação?');"
                                                title="Recusar Doação">
                                            <i class="material-icons">close</i>
                                        </button>
                                    </form>

                                {{-- Aprovada: Exibir botão para abrir o Modal de Preço e Integrar ao Estoque --}}
                                @elseif($linha->status === 'Aprovada')
                                    <button type="button" 
                                            class="btn-action inventory btn-abrir-modal" 
                                            data-id="{{ $linha->id_doacao }}" 
                                            data-nome="{{ $linha->nome }}"
                                            title="Definir Preço e Mandar para Vitrine">
                                        <i class="material-icons">storefront</i>
                                    </button>
                                @endif

                                {{-- Botão de Visualizar/Editar Detalhes (Disponível apenas se não foi finalizada) --}}
                                @if($linha->status !== 'Integrada ao Estoque')
                                    <a href="{{ route('admin.doacoes.editarDoacao', ['id' => $linha->id_doacao]) }}" class="btn-action edit" title="Editar / Ver Detalhes">
                                        <i class="material-icons">edit</i>
                                    </a>
                                @endif

                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="empty-table">Nenhuma doação cadastrada no sistema.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Estrutura do Modal de Precificação --}}
<div id="modalPreco" class="modal-preco">
    <div class="modal-conteudo">
        <h3>Confirmar Retirada e Definir Preço</h3>
        <p id="modalTextoItem" class="modal-text"></p>
        
        <form id="formIntegrarPreco" action="" method="POST">
            @csrf
            <label for="preco_venda">Valor de Venda (R$):</label>
            <input type="number" name="preco" id="preco_venda" step="0.01" min="0" placeholder="0,00" required>
            
            <div class="modal-botoes">
                <button type="button" class="btn class-secondary btn-close-modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Confirmar e Cadastrar</button>
            </div>
        </form>
    </div>
</div>
@endsection