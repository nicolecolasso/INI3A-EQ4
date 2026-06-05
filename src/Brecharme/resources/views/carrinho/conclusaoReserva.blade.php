@extends('layout.site')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/carrinho.css') }}">
@endpush    

@section('titulo', 'Conclusão da Reserva')

@section('conteudo')
<div class="sucesso-container">
    <h1 class="carrinho-title centralizado">Confirmação de Reserva - {{ $cliente->name ?? 'Cliente' }}</h1>

    <div class="sucesso-block">
        <div class="sucesso-header-flex">
            <div class="sucesso-badge-icon">
                <i class="fa-solid fa-check">✔</i>
            </div>
            <div class="sucesso-text-content">
                <h2>Reserva confirmada!</h2>
                <p>Parabéns! Sua solicitação de reserva foi realizada. Venha até o Brecharme para realizar o pagamento e retirar seus itens.</p>
            </div>
        </div>
    </div>

    <div class="sucesso-block">
        <h3 class="detalhes-titulo">Detalhes da reserva:</h3>
        <p class="texto-sub-detalhe">Abaixo estão os produtos confirmados vinculados ao seu usuário:</p>
        
        <div class="detalhes-lista-produtos">
            @php $itemReservados = false; @endphp

            @foreach($produtosReservados as $reservaConfirmada)
                {{-- Como ->produto é uma coleção via hasManyThrough, iteramos sobre ela --}}
                @foreach($reservaConfirmada->produto as $produto)
                    @php $itemReservados = true; @endphp
                    <div class="detalhe-produto-linha">
                        <span>
                            <strong>• {{ $produto->nome ?? 'Produto' }}</strong> 
                            <small class="status-badge-inline">(Status: {{ $reservaConfirmada->status }})</small>
                        </span>
                        <span>R$ {{ number_format($produto->valor ?? 0, 2, ',', '.') }}</span>
                    </div>
                @endforeach
            @endforeach

            @if(!$itemReservados)
                <p class="texto-vazio">Nenhum produto reservado encontrado recentemente.</p>
            @endif
        </div>

        <div class="container-botao-voltar">
            <a href="{{ route('produtos.vitrine') }}" class="btn-voltar-vitrine">Voltar para a Vitrine</a>
        </div>
    </div>
</div>
@endsection