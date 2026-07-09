@extends('layout.site')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/galeria.css') }}">
@endpush

@section('titulo', 'Nosso Bazar & Destaques')

@section('conteudo')
<div class="bazar-container">
    
    <section class="insta-feed-section">
        <h2 class="bazar-secao-titulo"><i class="material-icons">star</i> Destaques do Instagram</h2>
        <p class="bazar-secao-subtitulo">Acompanhe as publicações mais importantes do nosso perfil <a href="https://www.instagram.com/brecharme_caritasbauru" target="_blank" style="color: var(--amarelo-brecho); text-decoration: none;">@brecharme_caritasbauru</a></p>
        
        <div class="items-slider">
            <button class="slider-arrow prev">
                <i class="material-icons">chevron_left</i>
            </button>
            
            <div class="items-grid insta-embeds-grid {{ $postsInstagram->isEmpty() ? 'empty' : 'has-items' }}">
                @forelse($postsInstagram as $post)
                    <div class="insta-embed-card item-card">
                        <blockquote class="instagram-media" 
                                    data-instgrm-permalink="{{ $post->link_post }}" 
                                    data-instgrm-version="14" 
                                    style="background:#FFF; border:0; padding:0; width:100%; margin:0;">
                        </blockquote>
                    </div>
                @empty
                    <p class="sem-registros">Nenhum post destacado no momento.</p>
                @endforelse
            </div>
            
            <button class="slider-arrow next">
                <i class="material-icons">chevron_right</i>
            </button>
        </div>
    </section>

    <hr class="bazar-divisor">

    <section class="eventos-galeria-section">
        <h2 class="bazar-secao-titulo"><i class="material-icons">collections</i> Galeria do Bazar</h2>
        <p class="bazar-secao-subtitulo">Registros fotográficos dos nossos eventos</p>
        
        <div class="galeria-fotos-grid">
            @forelse($fotosGaleria as $foto)
                <div class="galeria-item-card">
                    <div class="galeria-img-wrapper">
                        <img src="{{ asset($foto->caminho_img) }}" alt="{{ $foto->titulo_evento }}">
                    </div>
                    @if($foto->titulo_evento)
                        <div class="galeria-item-info"><span>{{ $foto->titulo_evento }}</span></div>
                    @endif
                </div>
            @empty
                <p class="sem-registros">Nenhuma foto adicionada à galeria local.</p>
            @endforelse
        </div>
    </section>
</div>

{{-- Script nativo do Instagram carregado uma única vez no rodapé da página --}}
<script async src="//www.instagram.com/embed.js"></script>
@endsection