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
                        <td>#{{ $linha->id_compra }}</td>
                        <td>
                            <strong>{{ $linha->usuario->name ?? 'Usuário não encontrado' }}</strong>
                            <div style="font-size: 12px; color: #888;">{{ $linha->usuario->email ?? '' }}</div>
                        </td>
                        <td>
                            {{-- Loop para varrer os produtos vinculados a esta compra específica --}}
                            @php $totalReserva = 0; @endphp
                            
                            @if($linha->itens->count() > 0)
                                @foreach($linha->itens as $item)
                                    @if($item->produto)
                                        @php $totalReserva += $item->produto->valor; @endphp
                                        <div style="margin-bottom: 4px;">
                                            <strong>{{ $item->produto->nome }}</strong> 
                                            <span style="color: #666; font-size: 12px;">
                                                (R$ {{ number_format($item->produto->valor, 2, ',', '.') }})
                                            </span>
                                        </div>
                                    @endif
                                @endforeach
                                
                                {{-- Se houver mais de um produto, exibe o total da reserva --}}
                                @if($linha->itens->count() > 1)
                                    <div style="font-size: 12px; color: #b39012; font-weight: 600; border-top: 1px dashed #ddd; margin-top: 4px; padding-top: 4px;">
                                        Total: R$ {{ number_format($totalReserva, 2, ',', '.') }}
                                    </div>
                                @endif
                            @else
                                <span style="color: #999; font-style: italic;">Nenhum produto atrelado</span>
                            @endif
                        </td>
                        <td>{{ \Carbon\Carbon::parse($linha->data_compra)->format('d/M/Y H:i') }}</td>
                        <td>
                            @if($linha->status == 'Reservado')
                                <span class="badge badge-admin" style="background-color: #FFF9E6; color: #B38F00; border: 1px solid #FFE0B2;">
                                    Reservado
                                </span>
                            @elseif($linha->status == 'Carrinho')
                                <span class="badge badge-user" style="background-color: #E0F7FA; color: #006064;">
                                    No Carrinho
                                </span>
                            @elseif($linha->status == 'Concluída')
                                <span class="badge" style="background-color: #E8F5E9; color: #2E7D32;">
                                    Concluída
                                </span>
                            @else
                                <span class="badge badge-inactive">
                                    Cancelada
                                </span>
                            @endif
                        </td>
                        <td>
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