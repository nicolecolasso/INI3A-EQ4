@extends('layout.site')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/perfil.css') }}">
@endpush

@section('titulo', 'Minhas Reservas')

@section('conteudo')
<div class="perfil-wrapper reservas-container-main">
   
    {{-- Topo da Página --}}
    <div class="header-reservas-flex">
        <h2 class="titulo-reservas-seasons">Minhas reservas:</h2>
        <a href="{{ route('perfil.meuPerfil') }}" class="link-voltar-dourado-reservas">
            <i class="material-icons">arrow_back</i> Voltar ao Perfil
        </a>
    </div>

    {{-- Alertas do Sistema --}}
    @if (session('sucesso'))
        <div class="alert-reservas-sucesso">
            <i class="material-icons">check_circle</i>
            <span>{{ session('sucesso') }}</span>
        </div>
    @endif

    @if (session('erro'))
        <div class="alert-reservas-erro">
            <i class="material-icons">error_outline</i>
            <span>{{ session('erro') }}</span>
        </div>
    @endif

    {{-- Verificação de Conteúdo da Lista --}}
    @if($compras->isEmpty())
        <div class="card-vazio-reservas">
            <i class="material-icons icon-vazio-reserva">receipt_long</i>
            <p class="texto-vazio-reserva">Você ainda não possui nenhuma reserva realizada.</p>
            <a href="{{ route('produtos.vitrine') }}" class="link-vazio-reserva">Ir para a vitrine escolher peças →</a>
        </div>
    @else
        {{-- Contêiner Isolado da Tabela Responsiva --}}
        <div class="table-responsive-wrapper">
            <table class="tabela-perfil-custom">
                <thead>
                    <tr>
                        <th>Código (ID)</th>
                        <th>Data da Reserva</th>
                        <th>Valor Total</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($compras as $compra)
                        <tr class="reserva-row">
                            <td data-label="Código (ID)">#{{ $compra->id_compra }}</td>
                           
                            <td data-label="Data da Reserva">{{ $compra->created_at->format('d/m/Y') }}</td>
                           
                            <td data-label="Valor Total">R$ {{ number_format($compra->valor_total, 2, ',', '.') }}</td>
                           
                            <td data-label="Status">
                                @php
                                    $statusLower = strtolower($compra->status);
                                    $statusClass = 'status-reservado';
                                    
                                    if($statusLower == 'carrinho') $statusClass = 'status-carrinho';
                                    if($statusLower == 'concluída' || $statusLower == 'concluida') $statusClass = 'status-concluida';
                                    if($statusLower == 'cancelada') $statusClass = 'status-cancelada-custom';
                                @endphp
                                <span class="status-badge-reserva {{ $statusClass }}">
                                    {{ $compra->status }}
                                </span>
                            </td>
                           
                            <td data-label="Ações">
                                <div class="actions-reservas-flex">
                                    <button type="button" 
                                            class="btn-abrir-detalhes" 
                                            data-id="{{ $compra->id_compra }}"
                                            data-data="{{ $compra->created_at->format('d/m/Y') }}"
                                            data-status="{{ $compra->status }}"
                                            data-total="R$ {{ number_format($compra->valor_total, 2, ',', '.') }}"
                                            data-produtos="{{ json_encode($compra->itens ?? $compra->produtos ?? []) }}">
                                        <i class="material-icons">visibility</i> Visualizar
                                    </button>

                                    @if(strtolower($compra->status) == 'reservado' || strtolower($compra->status) == 'carrinho')
                                        <a href="{{ route('perfil.minhasReservas.cancelar', $compra->id_compra) }}"
                                           onclick="return confirm('Deseja realmente cancelar esta reserva?')"
                                           class="link-cancelar-reserva">
                                            <i class="material-icons">cancel</i> Cancelar
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

</div>

{{-- ESTRUTURA DA MODAL DE DETALHES --}}
<div id="modalDetalhesReserva" class="modal-reservas-overlay">
    <div class="modal-reservas-content">
        <div class="modal-reservas-header">
            <h3>Detalhes da Reserva <span id="modalIdReserva"></span></h3>
            <button type="button" class="modal-reservas-close" id="btnFecharModal">&times;</button>
        </div>
        <div class="modal-reservas-body">
            <div class="modal-meta-grid">
                <p><strong>Data:</strong> <span id="modalDataReserva"></span></p>
                <p><strong>Status:</strong> <span id="modalStatusReserva"></span></p>
            </div>
            
            <h4 class="modal-subtitulo-itens">Itens Reservados:</h4>
            <div id="modalListaProdutos" class="modal-lista-produtos">
                </div>

            <div class="modal-total-wrapper">
                <span>Total:</span>
                <span id="modalTotalReserva"></span>
            </div>
        </div>
    </div>
</div>
@endsection