@extends('layout.site')
@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endpush
@section('titulo', 'Home')
@section('conteudo')

<!-- Link para o CSS específico da Home -->
<link rel="stylesheet" href="{{ asset('css/index.css') }}">

<main class="home-container">
    <!-- 1. Carrossel / Hero Section -->
    <section class="hero-carousel">
        <div class="carousel-inner">
            <img src="{{ asset('img/Imagem_roupas.jpg') }}" alt="Loja Brecharme" class="hero-image">
            <div class="carousel-dots">
                <span class="dot active"></span>
                <span class="dot"></span>
                <span class="dot"></span>
            </div>
        </div>
    </section>

    <!-- 2. Itens em Destaque -->
    <section class="featured-items">
        <h2 class="section-title">Itens em destaque</h2>
        <div class="items-slider">
            <button class="slider-arrow prev"><i class="material-icons">chevron_left</i></button>
            <div class="items-grid">
                <div class="item-card">
                    <img src="{{ asset('img/Imagem_roupas.jpg') }}" alt="Item 1">
                </div>
                <div class="item-card">
                    <img src="{{ asset('img/tenis.jpg') }}" alt="Tênis">
                </div>
                <div class="item-card">
                    <img src="{{ asset('img/Imagem_roupas.jpg') }}" alt="Item 3">
                </div>
                <div class="item-card">
                    <img src="{{ asset('img/Imagem_roupas.jpg') }}" alt="Item 4">
                </div>
            </div>
            <button class="slider-arrow next"><i class="material-icons">chevron_right</i></button>
        </div>
    </section>

    <!-- 3. Sobre a Cáritas -->
    <section class="about-caritas">
        <a href="{{ route('institucional.quemSomos') }}" class="banner-link">
            <div class="about-banner" style="background-image: url({{ asset('img/Imagem_roupas.jpg') }});"> 
                <div class="about-overlay">
                    <div class="about-label">Sobre a Cáritas</div>
                    <span class="btn-saiba-mais">Saiba mais</span>
                </div>
            </div>
        </a>
    </section>

    <!-- 4. Encontre-nos -->
    <section class="find-us">
        <a href="https://maps.app.goo.gl/Qv6UfzH5sycVVMWh9" target="_blank" class="banner-link">
            <div class="find-banner" style="background-image: url({{ asset('img/Imagem_roupas.jpg') }});">
                <div class="find-overlay">
                    <div class="find-label">
                        Encontre-nos <i class="material-icons">location_on</i>
                    </div>
                    <div class="find-logo">
                        <img src="{{ asset('img/logo.png') }}" alt="Brecharme Logo">
                    </div>
                </div>
            </div>
        </a>
    </section>
</main>
@endsection