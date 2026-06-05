@extends('layout.site')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endpush

@section('titulo', 'Home')

@section('conteudo')
<div class="home-wrapper">
    
    <section class="hero-carousel">
        <div class="carousel-inner">
            <img src="{{ asset('img/brecharme1.png') }}" alt="Loja Brecharme" class="hero-image">
            <div class="carousel-dots">
                <span class="dot"></span>
                <span class="dot active"></span>
                <span class="dot"></span>
            </div>
        </div>
    </section>

    <section class="featured-items">
        <h2 class="section-title">Itens em destaque</h2>
        
        <div class="items-slider">
            <button class="slider-arrow prev">
                <i class="material-icons">chevron_left</i>
            </button>
            
            <div class="items-grid">
                {{-- Loop dinâmico usando os atributos reais do seu Model Produto --}}
                @forelse($produtos as $produto)
                    <div class="item-card">
                        <a href="{{ route('produtos.detalheProduto', $produto->id_produto) }}" class="item-link">
                            
                            <div class="item-card-image">
                                <img src="{{ asset($produto->caminho_img) }}" alt="{{ $produto->nome }}">
                            </div>

                            <div class="item-info-overlay">
                                <h3 class="item-name">{{ $produto->nome }}</h3>
                                <p class="item-price">R$ {{ number_format($produto->valor, 2, ',', '.') }}</p>
                            </div>
                        </a>
                    </div>
                @empty
                    <p class="sem-produtos" style="font-family: var(--fonte-texto); color: var(--cinza-detalhe);">
                        Nenhum item em destaque no momento.
                    </p>
                @endforelse
            </div>
            
            <button class="slider-arrow next">
                <i class="material-icons">chevron_right</i>
            </button>
        </div>
    </section>

    <section class="banners-double-row">
        
        <div class="banner-box caritas-box">
            <a href="https://caritasbauru.org.br/" target="_blank" class="banner-link">
                <div class="banner-bg-img" style="background-image: url('{{ asset('img/caritas.png') }}');">
                    <div class="banner-ui-overlay">
                        <div class="banner-pill-label label-caritas">Sobre a Cáritas</div>
                        <span class="btn-action-trigger red-trigger">Saiba mais</span>
                    </div>
                </div>
            </a>
        </div>

        <div class="banner-box find-box">
            <a href="https://maps.app.goo.gl/Qv6UfzH5sycVVMWh9" target="_blank" class="banner-link">
                <div class="banner-bg-img" style="background-image: url('{{ asset('img/localizacao.png') }}');">
                    <div class="banner-ui-overlay">
                        <div class="banner-pill-label label-find">
                            Encontre-nos 
                            <i class="material-icons">location_on</i>
                        </div>
                    </div>
                </div>
            </a>
        </div>

    </section>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const heroImages = [
            "{{ asset('img/brecharme1.png') }}",
            "{{ asset('img/brecharme2.png') }}",
            "{{ asset('img/brecharme3.png') }}"
        ];

        const heroImageElement = document.querySelector('.hero-image');
        const dots = document.querySelectorAll('.carousel-dots .dot');
        let currentHeroIndex = 0;
        let heroInterval = null;

        function setHeroSlide(index) {
            currentHeroIndex = index;
            heroImageElement.src = heroImages[index];
            dots.forEach((dot, dotIndex) => {
                dot.classList.toggle('active', dotIndex === index);
            });
        }

        function startHeroRotation() {
            heroInterval = setInterval(() => {
                setHeroSlide((currentHeroIndex + 1) % heroImages.length);
            }, 4000);
        }

        function resetHeroRotation() {
            clearInterval(heroInterval);
            startHeroRotation();
        }

        if (heroImageElement && dots.length) {
            dots.forEach((dot, index) => {
                dot.addEventListener('click', () => {
                    setHeroSlide(index);
                    resetHeroRotation();
                });
            });

            setHeroSlide(0);
            startHeroRotation();
        }

        const itemsGrid = document.querySelector('.items-grid');
        const prevButton = document.querySelector('.slider-arrow.prev');
        const nextButton = document.querySelector('.slider-arrow.next');

        if (itemsGrid && prevButton && nextButton) {
            const cards = itemsGrid.querySelectorAll('.item-card');
            if (cards.length === 0) {
                prevButton.style.display = 'none';
                nextButton.style.display = 'none';
            } else {
                const scrollAmount = () => Math.round(itemsGrid.clientWidth * 0.8);

                prevButton.addEventListener('click', () => {
                    itemsGrid.scrollBy({ left: -scrollAmount(), behavior: 'smooth' });
                });

                nextButton.addEventListener('click', () => {
                    itemsGrid.scrollBy({ left: scrollAmount(), behavior: 'smooth' });
                });
            }
        }
    });
</script>
@endsection