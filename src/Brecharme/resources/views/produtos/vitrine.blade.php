@extends('layout.site')

@section('titulo', 'Vitrine')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/produtos.css') }}">
@endpush

@section('conteudo')
<div class="vitrine-container">
    <div class="vitrine-header">
        <h1 class="vitrine-title">Produtos</h1>
       
        <div class="filtrar-container">
            <button class="filtrar-btn" onclick="toggleFiltro()">
                <i class="material-icons">tune</i>
                <span>Filtrar</span>
            </button>
           
            <div class="filtro-dropdown" id="filtroDropdown">
                <a href="{{ route('produtos.vitrine') }}" class="filtro-item">Todos</a>
                <a href="{{ route('produtos.vitrine', ['categoria' => 'Roupas']) }}" class="filtro-item">Roupas</a>
                <a href="{{ route('produtos.vitrine', ['categoria' => 'Calçados']) }}" class="filtro-item">Calçados</a>
                <a href="{{ route('produtos.vitrine', ['categoria' => 'Acessórios']) }}" class="filtro-item">Acessórios</a>
                <a href="{{ route('produtos.vitrine', ['categoria' => 'Eletrônicos']) }}" class="filtro-item">Eletrônicos</a>
                <a href="{{ route('produtos.vitrine', ['categoria' => 'Móveis']) }}" class="filtro-item">Móveis</a>
                <a href="{{ route('produtos.vitrine', ['categoria' => 'Brinquedos']) }}" class="filtro-item">Brinquedos</a>
                <a href="{{ route('produtos.vitrine', ['categoria' => 'Outros']) }}" class="filtro-item">Outros</a>
            </div>
        </div>
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

<script>
    function toggleFiltro() {
        const dropdown = document.getElementById('filtroDropdown');
        dropdown.classList.toggle('ativo');
    }

    document.addEventListener('click', function(event) {
        const filtroContainer = document.querySelector('.filtrar-container');
        if (!filtroContainer.contains(event.target)) {
            document.getElementById('filtroDropdown').classList.remove('ativo');
        }
    });
</script>
@endsection