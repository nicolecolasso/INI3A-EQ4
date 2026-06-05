@extends('layout.site')

@section('titulo', $produto->nome ?? 'Detalhes do Produto')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/produtos.css') }}">
@endpush

@section('conteudo')
<div class="produto-detalhe-container">
    <div class="produto-layout">
        <div class="produto-imagens-col">
            <div class="imagem-principal-moldura">
                <img src="{{ asset($produto->caminho_img) }}" id="imagemPrincipal" alt="{{ $produto->nome ?? 'Produto' }}">
            </div>
        </div>

        <div class="produto-info-col">
            <h1 class="produto-nome">{{ $produto->nome ?? 'Nome do Produto' }}</h1>
            
            <p class="produto-card-preco" style="font-size: 1.8rem; text-align: left; margin: 1rem 0;">
                R$ {{ number_format($produto->valor ?? 0, 2, ',', '.') }}
            </p>
           
            <div class="produto-descricao">
                <p>{{ $produto->descricao ?? 'Nenhuma descrição detalhada fornecida para este produto.' }}</p>
            </div>

            <form action="{{ route('carrinho.adicionar', $produto->id_produto ?? 0) }}" method="POST" class="form-carrinho">
                @csrf
                <button type="submit" class="btn-adicionar-carrinho">
                    Adicionar ao Carrinho
                </button>
            </form>
        </div>
    </div>
</div>
@endsection