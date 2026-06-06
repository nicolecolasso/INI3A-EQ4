@extends('layout.site')
@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endpush
@section('titulo', 'Quem Somos')
@section('conteudo')

<div class="quem-somos-container">
    <section class="quem-somos-section">
        <h1 class="quem-somos-titulo">Quem somos?</h1>
        <div class="quem-somos-card">
            <p>
                &nbspO Brecharme é um brechó solidário vinculado à Cáritas de Bauru, organização que atua em ações de assistência e desenvolvimento social voltadas à comunidade. <br>
                &nbspComo uma extensão da Cáritas, o Brecharme tem como objetivo transformar doações de roupas, calçados e acessórios em oportunidades de apoio social, promovendo a sustentabilidade e o consumo consciente.<br>
                &nbspPor meio da comercialização de peças em bom estado por preços simbólicos, o Brecharme contribui para a manutenção de projetos sociais e amplia o acesso da população a produtos de qualidade e baixo custo. <br>
                &nbspDessa forma, une solidariedade, responsabilidade social e cuidado com o meio ambiente em benefício da comunidade.
            </p>
        </div>
    </section>

    <section class="quem-somos-section">
        <h2 class="quem-somos-subtitulo">Missão</h2>
        <div class="quem-somos-card">
            <p>
                &nbspPromover a solidariedade e a sustentabilidade por meio da comercialização de roupas e acessórios doados a preços simbólicos, tornando a moda acessível e contribuindo para o bem-estar da comunidade.
            </p>
        </div>
    </section>

    <section class="quem-somos-section">
        <h2 class="quem-somos-subtitulo">Visão</h2>
        <div class="quem-somos-card">
            <p>
                &nbspSer referência em brechó solidário, reconhecido pelo compromisso com a inclusão social, a sustentabilidade e a transformação positiva da comunidade por meio de ações que unem solidariedade e consumo consciente.
            </p>
        </div>
    </section>

    <section class="quem-somos-section">
        <h2 class="quem-somos-subtitulo">Valores</h2>
        <div class="quem-somos-card">
            <p>
                <li> Solidariedade: ajudar pessoas por meio de ações que promovam dignidade e inclusão. </li>
                <li> Sustentabilidade: incentivar a reutilização de produtos e a redução do desperdício. </li>
                <li> Respeito: tratar todas as pessoas com igualdade, empatia e acolhimento. </li>
                <li> Transparência: atuar com ética, responsabilidade e honestidade. </li>
                <li> Compromisso Social: gerar impacto positivo e contribuir para o desenvolvimento da comunidade. </li>
                <li> Acessibilidade: oferecer produtos de qualidade por preços justos e acessíveis. </li>
            </p>
        </div>
    </section>
</div>
@endsection