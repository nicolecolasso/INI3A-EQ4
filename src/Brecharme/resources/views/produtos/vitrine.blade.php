@extends('layout.site')

@section('titulo', 'Vitrine')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/produtos.css') }}">
@endpush

@section('conteudo')
<div class="vitrine-container">
    <h1 class="vitrine-title">Produtos</h1>
    
    <div class="vitrine-header">
        <form action="{{ route('produtos.buscar') }}" method="GET" class="vitrine-search-filter-inline-form">
            
            {{-- 1. Campo de Busca Principal --}}
            <div class="inline-search-box">
                <input type="search" id="campo-busca" name="q" value="{{ request('q') }}" placeholder="Digite o que procura...">
                <button type="submit" class="inline-search-btn">
                    <i class="material-icons">search</i>
                </button>
            </div>

            {{-- 2. Seletor Compacto de Categorias --}}
            <div class="inline-filter-select-wrapper">
                <select name="categoria" id="categoria">
                    <option value="">Todas as categorias</option>
                    @foreach($categorias as $cat)
                        <option value="{{ $cat->id_categoria }}" {{ request('categoria') == $cat->id_categoria ? 'selected' : '' }}>
                            {{ $cat->nome }}
                        </option>
                    @endforeach
                </select>
                <i class="material-icons inline-select-seta">expand_more</i>
            </div>

            {{-- 3. Inputs Compactos de Preço Lado a Lado --}}
            <div class="inline-price-range-group">
                <input type="number" name="preco_min" id="preco_min" min="0" step="0.01" value="{{ request('preco_min') }}" placeholder="Min R$">
                <span class="inline-price-separator">até</span>
                <input type="number" name="preco_max" id="preco_max" min="0" step="0.01" value="{{ request('preco_max') }}" placeholder="Max R$">
            </div>

            {{-- 4. Botão de Ação --}}
            <button type="submit" class="btn-inline-filtrar" title="Aplicar Filtros">
                <i class="material-icons">tune</i>
                <span>Filtrar</span>
            </button>

            {{-- 5. Link de Limpar (Se houver filtros ativos) --}}
            @if(request()->filled('categoria') || request()->filled('preco_min') || request()->filled('preco_max') || request()->filled('q'))
                <a href="{{ route('produtos.buscar') }}" class="btn-inline-limpar" title="Limpar Tudo">
                    <i class="material-icons">clear</i>
                </a>
            @endif

        </form>
    </div>

    <div class="produtos-grid">
        @forelse($produtos as $produto)
            <div class="produto-card">
                <a href="{{ route('produtos.detalheProduto', $produto->id_produto) }}" class="produto-link">
                    <div class="produto-imagem">
                        <img src="{{ asset($produto->caminho_img) }}" alt="{{ $produto->nome }}" class="produto-img">
                    </div>
                    
                    <div class="produto-info">
                        <p class="produto-card-nome">{{ $produto->nome }}</p>
                        <p class="produto-card-preco">R$ {{ number_format($produto->valor, 2, ',', '.') }}</p>
                    </div>
                </a>
            </div>
        @empty
            <div class="sem-produtos">
                <p>Nenhum produto encontrado nesta categoria.</p>
            </div>
        @endforelse
    </div>

    {{-- Sistema de paginação nativo do Laravel --}}
    @if($produtos->hasPages())
        <div class="paginacao-container">
            <div class="paginacao">
                @if($produtos->onFirstPage())
                    <span class="paginacao-item desabilitado">&lt;</span>
                @else
                    <a href="{{ $produtos->previousPageUrl() }}" class="paginacao-item">&lt;</a>
                @endif

                @foreach($produtos->getUrlRange(1, $produtos->lastPage()) as $page => $url)
                    @if($page == $produtos->currentPage())
                        <span class="paginacao-item ativo">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="paginacao-item">{{ $page }}</a>
                    @endif
                @endforeach

                @if($produtos->hasMorePages())
                    <a href="{{ $produtos->nextPageUrl() }}" class="paginacao-item">&gt;</a>
                    <a href="{{ $produtos->path() }}?page={{ $produtos->lastPage() }}" class="paginacao-item">&gt;&gt;</a>
                @else
                    <span class="paginacao-item desabilitado">&gt;</span>
                    <span class="paginacao-item desabilitado">&gt;&gt;</span>
                @endif
            </div>
        </div>
    @endif
</div>

@endsection