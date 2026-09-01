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
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        @if (session('erro'))
            <div class="alert-error-login">
                <i class="material-icons">error_outline</i>
                <span>{{ session('erro') }}</span>
            </div>
        @endif

        @if (session('sucesso'))
            <div class="alert-success-login" style="background-color: #d4edda; color: #155724; padding: 12px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 8px; border: 1px solid #c3e6cb;">
                <i class="material-icons">check_circle</i>
                <span>{{ session('sucesso') }}</span>
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
                    <i class="material-icons toggle-password-btn" id="toggle-senha">visibility_off</i>
                </div>
            </div>

            <div class="remember-me-wrapper">
                <label class="remember-me-label">
                    <input type="checkbox" name="remember" id="remember" value="1">
                    <span>Manter-me conectado</span>
                </label>
            </div>

            <button type="submit" class="btn-login-premium">
                Entrar
            </button>
            </form>

            <div class="login-divider">
                <span>ou</span>
            </div>

            <a href="{{ route('login.google') }}" class="btn-google-login">
                <svg class="google-icon" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                    <path fill="#FFC107" d="M43.611 20.083H42V20H24v8h11.303c-1.649 4.657-6.08 8-11.303 8-6.627 0-12-5.373-12-12s5.373-12 12-12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4 12.955 4 4 12.955 4 24s8.955 20 20 20 20-8.955 20-20c0-1.341-.138-2.65-.389-3.917z"/>
                    <path fill="#FF3D00" d="M6.306 14.691l6.571 4.819C14.655 15.108 18.961 12 24 12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4 16.318 4 9.656 8.337 6.306 14.691z"/>
                    <path fill="#4CAF50" d="M24 44c5.166 0 9.86-1.977 13.409-5.192l-6.19-5.238C29.211 35.091 26.715 36 24 36c-5.202 0-9.619-3.317-11.283-7.946l-6.522 5.025C9.505 39.556 16.227 44 24 44z"/>
                    <path fill="#1976D2" d="M43.611 20.083H42V20H24v8h11.303a12.04 12.04 0 0 1-4.087 5.571l.003-.002 6.19 5.238C36.971 39.205 44 34 44 24c0-1.341-.138-2.65-.389-3.917z"/>
                </svg>
                <span>Entrar com o Google</span>
            </a>
        <div class="login-footer-navigation">
            <a href="{{ route('login.esqueciSenha') }}" class="footer-nav-link forgot-password">
                Esqueci minha senha
            </a>
            
            <p class="signup-text-wrapper">
                Não possui cadastro? 
                <a href="{{ route('login.novoCadastro') }}" class="footer-nav-link registration-link">
                    Cadastre-se
                </a>
            </p>
        </div>

    </div>
</div>
@endsection