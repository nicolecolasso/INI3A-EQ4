@extends('layout.site') @push('estilos')
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@endpush

@section('titulo', 'Gerenciar')

@section('conteudo')
<div class="gerenciar-mobile-container">
    
    <h1 class="gerenciar-titulo">Gerenciar</h1>

    <div class="gerenciar-menu-buttons">
        
        <a href="{{ route('admin.produtos') }}" class="btn-pill-admin dark-border">
            Produtos
        </a>
        
        <a href="{{ route('admin.usuarios') }}" class="btn-pill-admin dark-border">
            Usuários
        </a>
        
        <a href="{{ route('admin.reservas') }}" class="btn-pill-admin dark-border">
            Reservas
        </a>
        
        <a href="{{ route('admin.doacoes') }}" class="btn-pill-admin dark-border">
            Doações
        </a>

        <a href="{{ route('admin.banners') }}" class="btn-pill-admin dark-border">
            Banners
        </a>

        <a href="{{ route('admin.galeria') }}" class="btn-pill-admin dark-border">
            Galeria
        </a>
        
        <a href="{{ route('admin.comunicados.novoComunicado') }}" class="btn-pill-admin yellow-border">
            Novo Comunicado
        </a>
        
        <a href="{{ route('admin.comunicados.reenviarComunicado') }}" class="btn-pill-admin yellow-border">
            Reenvio Comunicado
        </a>

    </div>

</div>
@endsection