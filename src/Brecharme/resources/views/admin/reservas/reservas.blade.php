@extends('layout.site')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@endpush

@section('titulo', 'Reservas e Compras')

@section('conteudo')
<div class="admin-table-container">
    
    <header class="table-header-box">
        <h2>Gerenciamento de Reservas / Compras</h2>
        <a href="{{ route('admin.reservas.novaReserva') }}" class="btn-add-table">
            <i class="material-icons">shopping_bag</i> Nova Reserva/Compra
        </a>
    </header>

    @if (session('sucesso'))
        <div class="alert-success-custom" style="background-color: #d4edda; color: #155724; padding: 12px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e6cb; display: flex; align-items: center; gap: 8px;">
            <i class="material-icons">check_circle</i>
            <span>{{ session('sucesso') }}</span>
        </div>
    @endif

    @if (session('erro'))
        <div class="alert-error-custom" style="background-color: #f8d7da; color: #721c24; padding: 12px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #f5c6cb; display: flex; align-items: center; gap: 8px;">
            <i class="material-icons">error_outline</i>
            <span>{{ session('erro') }}</span>
        </div>
    @endif

    <div class="admin-filters-wrapper">
        <form action="{{ route('admin.reservas.buscar') }}" method="GET" class="filters-form">

            <div class="filter-field">
                <label for="filter-termo">Cliente ou email</label>
                <div class="filter-input-icon">
                    <i class="material-icons">search</i>
                    <input type="text" name="termo" id="filter-termo" value="{{ request('termo') }}" placeholder="Nome ou e-mail...">
                </div>
            </div>

            <div class="filter-field">
                <label for="filter-status">Status</label>
                <div class="filter-input-icon">
                    <i class="material-icons">assignment</i>
                    <select name="status" id="filter-status">
                        <option value="">Todos os status</option>
                        <option value="Reservado" {{ request('status') === 'Reservado' ? 'selected' : '' }}>Reservado</option>
                        <option value="Carrinho" {{ request('status') === 'Carrinho' ? 'selected' : '' }}>No Carrinho</option>
                        <option value="Concluída" {{ request('status') === 'Concluída' ? 'selected' : '' }}>Concluída</option>
                        <option value="Cancelada" {{ request('status') === 'Cancelada' ? 'selected' : '' }}>Cancelada</option>
                    </select>
                </div>
            </div>

            <div class="filter-field">
                <label for="meuCalendario">Data</label>
                <div class="filter-input-icon">
                    <i class="material-icons">calendar_today</i>
                    <input type="date" name="data" id="meuCalendario" value="{{ request('data') }}">
                </div>
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn-filter-submit" title="Filtrar Resultados">
                    <i class="material-icons">search</i> Filtrar
                </button>
                @if(request()->filled('termo') || request()->filled('status') || request()->filled('data'))
                    <a href="{{ route('admin.reservas') }}" class="btn-filter-clear" title="Limpar Filtros">
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
                    <th>ID Reserva</th>
                    <th>Cliente</th>
                    <th>Produto</th>
                    <th>Data</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($linhas as $linha)
                    <tr class="{{ in_array($linha->status, ['Concluída', 'Cancelada']) ? 'row-user-inactive' : '' }}">
                        <td data-label="ID Reserva">#{{ $linha->id_compra }}</td>
                        <td data-label="Cliente">
                            <strong>{{ $linha->usuario->name ?? 'Usuário não encontrado' }}</strong>
                            <div class="admin-subtext">{{ $linha->usuario->email ?? '' }}</div>
                        </td>
                        <td data-label="Produto">
                            {{-- Modificado para ler diretamente a relação de produtos da compra --}}
                            @if($linha->produtos->count() > 0)
                                @foreach($linha->produtos as $produto)
                                    <div class="admin-item-row">
                                        <strong>{{ $produto->nome }}</strong> 
                                        <span class="admin-subtext admin-inline-note">
                                            (R$ {{ number_format($produto->valor, 2, ',', '.') }})
                                        </span>
                                    </div>
                                @endforeach
                                
                                {{-- Se houver mais de um produto, exibe o total acumulado de forma otimizada --}}
                                @if($linha->produtos->count() > 1)
                                    <div class="reservation-total">
                                        Total: R$ {{ number_format($linha->produtos->sum('valor'), 2, ',', '.') }}
                                    </div>
                                @endif
                            @else
                                <span class="admin-italic-note">Nenhum produto atrelado</span>
                            @endif
                        </td>
                        <td data-label="Data">{{ \Carbon\Carbon::parse($linha->data_compra)->format('d/M/Y H:i') }}</td>
                        <td data-label="Status">
                            @if($linha->status == 'Reservado')
                                <span class="badge badge-reservado">Reservado</span>
                            @elseif($linha->status == 'Carrinho')
                                <span class="badge badge-carrinho">No Carrinho</span>
                            @elseif($linha->status == 'Concluída')
                                <span class="badge badge-concluida">Concluída</span>
                            @else
                                <span class="badge badge-inactive">Cancelada</span>
                            @endif
                        </td>
                        <td data-label="Ações">
                            <div class="action-buttons-flex">
                                <a href="{{ route('admin.reservas.editarReserva', $linha->id_compra) }}" class="btn-action edit" title="Mudar Status / Editar">
                                    <i class="material-icons">edit</i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="empty-table">Nenhuma movimentação ou reserva encontrada no sistema.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection