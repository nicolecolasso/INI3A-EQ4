@extends('layout.site')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endpush

@section('titulo', 'Login')

@section('conteudo')

<div class="login-screen-wrapper">
    <div class="login-card-box">
        
        <div class="login-avatar-header">
            <div class="avatar-circle icon-highlight">
                <i class="material-icons">account_circle</i>
            </div>
        </div>

        <h4 class="login-main-title">Entrar</h4>

        @if ($errors->any())
            <div class="alert-error-login">
                <i class="material-icons">error_outline</i>
                <span>{{ $errors->first() ?? session('erro') }}</span>
            </div>
        @endif

        @if (session('erro'))
            <div class="alert-error-login">
                <i class="material-icons">error_outline</i>
                <span>{{ session('erro') }}</span>
            </div>
        @endif

        <form action="{{ route('login.entrar') }}" method="POST" class="login-native-form">
            {{ csrf_field() }}

            <div class="native-input-group">
                <label for="email" class="native-label">E-mail</label>
                <div class="input-with-icon">
                    <i class="material-icons input-icon">email</i>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        placeholder="Digite seu e-mail" 
                        required>
                </div>
            </div>

            <div class="native-input-group">
                <label for="senha" class="native-label">Senha</label>
                <div class="input-with-icon">
                    <i class="material-icons input-icon">lock</i>
                    <input 
                        type="password" 
                        id="senha" 
                        name="senha" 
                        placeholder="Digite sua senha" 
                        required>
                </div>
            </div>

            <button type="submit" class="btn-login-premium">
                Entrar
            </button>
        </form>

        <div class="login-footer-navigation">
            <a href="{{ url('/recuperar-senha') }}" class="footer-nav-link forgot-password">
                Esqueci minha senha
            </a>
            
            <p class="signup-text-wrapper">
                Não possui cadastro? 
                <a href="{{ url('/novo-cadastro') }}" class="footer-nav-link registration-link">
                    Cadastre-se
                </a>
            </p>
        </div>

    </div>
</div>

@endsection