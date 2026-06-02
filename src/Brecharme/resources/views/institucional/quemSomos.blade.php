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
                Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor 
                incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis 
                nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. 
                Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore 
                eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, 
                sunt in culpa qui officia deserunt mollit anim id est laborum.
            </p>
        </div>
    </section>

    <section class="quem-somos-section">
        <h2 class="quem-somos-subtitulo">Missão</h2>
        <div class="quem-somos-card">
            <p>
                Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor 
                incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud.
            </p>
        </div>
    </section>

    <section class="quem-somos-section">
        <h2 class="quem-somos-subtitulo">Visão</h2>
        <div class="quem-somos-card">
            <p>
                Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor 
                incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud.
            </p>
        </div>
    </section>

    <section class="quem-somos-section">
        <h2 class="quem-somos-subtitulo">Valores</h2>
        <div class="quem-somos-card">
            <p>
                Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor 
                incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud.
            </p>
        </div>
    </section>
</div>
@endsection