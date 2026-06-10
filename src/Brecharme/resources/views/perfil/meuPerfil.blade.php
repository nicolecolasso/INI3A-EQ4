@extends('layout.site')


@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/perfil.css') }}">
@endpush


@section('titulo', 'Meu Perfil')


@section('conteudo')
<div class="perfil-wrapper">
   
    {{-- Mensagem de sucesso flutuante --}}
    @if (session('sucesso'))
        <div class="alert-success">
            <i class="material-icons">check_circle</i>
            <span>{{ session('sucesso') }}</span>
        </div>
    @endif


    <div class="perfil-grid">
       
        <div class="perfil-card">
            <div class="avatar-container">
                <i class="material-icons">account_circle</i>
            </div>
            <h3 class="perfil-card-title">{{ Auth::user()->name }}</h3>
           
            <p class="perfil-card-subtitle">
                @if(Auth::user()->admin)
                    Administrador
                @else
                    Cliente Brecharme
                @endif
            </p>
           
            <hr class="perfil-separator">
           
            <div class="user-stats">
                <p class="user-stat">
                    <i class="material-icons">shopping_bag</i>
                    <strong>Reservas:</strong> {{ Auth::user()->reservas?->count() ?? 0 }}
                </p>
                <p class="user-stat">
                    <i class="material-icons">volunteer_activism</i>
                    <strong>Doações:</strong> {{ Auth::user()->doacoes?->count() ?? 0 }}
                </p>
            </div>


            <a href="{{ route('perfil.meusDados') }}" class="btn-meus-dados">
                <i class="material-icons">manage_accounts</i>
                Meus Dados
            </a>
        </div>


        <div class="perfil-dashboard">
           
            <div class="welcome-box">
                <h4>Olá, {{ explode(' ', Auth::user()->name)[0] }}!</h4>
                <p>Bem-vindo ao seu espaço Brecharme. Aqui você pode acompanhar suas intenções de doação e verificar suas reservas feitas na nossa vitrine solidária.</p>
            </div>


            <div class="actions-grid">
               
                <div class="action-card">
                    <div>
                        <i class="material-icons action-card-icon">history</i>
                        <h5 class="action-card-title">Minhas Doações</h5>
                        <p class="action-card-text">Acompanhe o status e o histórico dos desapegos que você trouxe para o brechó.</p>
                    </div>
                    <a href="{{ route('perfil.minhasDoacoes') }}" class="action-card-link">
                        Ver minhas doações <i class="material-icons">arrow_forward</i>
                    </a>
                </div>


                <div class="action-card">
                    <div>
                        <i class="material-icons action-card-icon">receipt_long</i>
                        <h5 class="action-card-title">Minhas Compras</h5>
                        <p class="action-card-text">Gerencie as peças de roupas que você selecionou e separou na vitrine.</p>
                    </div>
                    <a href="{{ route('perfil.minhasReservas') }}" class="action-card-link">
                        Ver minhas reservas <i class="material-icons">arrow_forward</i>
                    </a>
                </div>


            </div>


            <div class="logout-container">
                <a href="{{ route('login.sair') }}" class="btn-sair-dourado">
                    Sair
                </a>
            </div>


        </div>


    </div>
</div>
@endsection