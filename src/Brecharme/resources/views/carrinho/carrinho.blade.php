@extends('layout.site')

@section('titulo', 'Carrinho')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/carrinho.css') }}">
@endpush

@section('conteudo')
<div class="carrinho-main-container">
    <h1 class="carrinho-title">Carrinho - {{ Auth::user()->name ?? 'Cliente' }}</h1>

    @if(session('sucesso'))
        <div class="alert-success">
            {{ session('sucesso') }}
        </div>
    @endif

    <div class="carrinho-grid">
        @php $temProdutos = false; @endphp

        @foreach($reservas as $reserva)
            @foreach($reserva->produtos as $item)
                @php $temProdutos = true; @endphp
                
                <div class="carrinho-item-card">
                    <div class="carrinho-img-wrapper">
                        @if($item->caminho_img)
                            <img src="{{ asset($item->caminho_img) }}" alt="{{ $item->nome }}">
                        @else
                            <img src="{{ asset('img/default-produto.png') }}" alt="Sem Imagem">
                        @endif
                    </div>
                    
                    <div class="carrinho-item-info">
                        <h4>{{ $item->nome }}</h4>
                        <p>Status: No carrinho</p>
                    </div>
                    
                    <div class="carrinho-item-preco">
                        R$ {{ number_format($item->valor ?? 0, 2, ',', '.') }}
                    </div>

                    <form action="{{ route('carrinho.remover', $item->id_produto ?? 0) }}" method="POST" class="form-remover-carrinho">
                        @csrf
                        <button type="submit" class="btn-remover-carrinho">
                            <i class="material-icons">delete</i>
                        </button>
                    </form>
                </div>
            @endforeach
        @endforeach

        @if(!$temProdutos)
            <div class="carrinho-vazio">
                <h3>Seu carrinho está vazio</h3>
                <a href="{{ route('produtos.vitrine') }}" class="btn-voltar-vitrine">Ir para a Vitrine</a>
            </div>
        @endif

        @if($temProdutos)
            <div class="carrinho-actions">
                {{-- Envia via POST o ID da compra atrelada ao carrinho --}}
                <form action="{{ route('carrinho.finalizar', $reservas->first()->id_compra) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-confirmar-reserva">Confirmar Reserva</button>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection