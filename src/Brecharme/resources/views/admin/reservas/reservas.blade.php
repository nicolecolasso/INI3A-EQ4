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
                            {{-- Loop para varrer os produtos vinculados a esta compra específica --}}
                            @php $totalReserva = 0; @endphp
                            
                            @if($linha->itens->count() > 0)
                                @foreach($linha->itens as $item)
                                    @if($item->produto)
                                        @php $totalReserva += $item->produto->valor; @endphp
                                        <div class="admin-item-row">
                                            <strong>{{ $item->produto->nome }}</strong> 
                                            <span class="admin-subtext admin-inline-note">
                                                (R$ {{ number_format($item->produto->valor, 2, ',', '.') }})
                                            </span>
                                        </div>
                                    @endif
                                @endforeach
                                
                                {{-- Se houver mais de um produto, exibe o total da reserva --}}
                                @if($linha->itens->count() > 1)
                                    <div class="reservation-total">
                                        Total: R$ {{ number_format($totalReserva, 2, ',', '.') }}
                                    </div>
                                @endif
                            @else
                                <span class="admin-italic-note">Nenhum produto atrelado</span>
                            @endif
                        </td>
                        <td data-label="Data">{{ \Carbon\Carbon::parse($linha->data_compra)->format('d/M/Y H:i') }}</td>
                        <td data-label="Status">
                            @if($linha->status == 'Reservado')
                                <span class="badge badge-reservado">
                                    Reservado
                                </span>
                            @elseif($linha->status == 'Carrinho')
                                <span class="badge badge-carrinho">
                                    No Carrinho
                                </span>
                            @elseif($linha->status == 'Concluída')
                                <span class="badge badge-concluida">
                                    Concluída
                                </span>
                            @else
                                <span class="badge badge-inactive">
                                    Cancelada
                                </span>
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