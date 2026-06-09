@extends('layout.site')


@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endpush


@section('titulo', 'Novo Cadastro')


@section('conteudo')
<div class="login-screen-wrapper">
    <div class="login-card-box">
       
        <div class="login-avatar-header">
            <div class="avatar-circle icon-highlight">
                <i class="material-icons">person_add</i>
            </div>
        </div>


        <h4 class="login-main-title">Criar Conta</h4>


        @if ($errors->any())
            <div class="alert-error-login">
                <i class="material-icons">error_outline</i>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif


        <form action="{{ route('login.salvarCadastro') }}" method="POST" class="login-native-form">
            {{ csrf_field() }}


            <div class="native-input-group">
                <label for="name" class="native-label">Nome Completo</label>
                <div class="input-with-icon">
                    <i class="material-icons input-icon">badge</i>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name') }}"
                        placeholder="Digite seu nome completo"
                        required>
                </div>
            </div>


            <div class="native-input-group">
                <label for="email" class="native-label">E-mail</label>
                <div class="input-with-icon">
                    <i class="material-icons input-icon">email</i>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="Digite seu e-mail"
                        required>
                </div>
            </div>


            <div class="native-input-group">
                <label for="telefone" class="native-label">Telefone <span class="optional-note">(Opcional)</span></label>
                <div class="input-with-icon">
                    <i class="material-icons input-icon">phone</i>
                    <input
                        type="tel"
                        id="telefone"
                        name="telefone"
                        value="{{ old('telefone') }}"
                        placeholder="(14) 99999-9999">
                </div>
            </div>


            <div class="native-input-group">
                <label for="password" class="native-label">Senha</label>
                <div class="input-with-icon">
                    <i class="material-icons input-icon">lock</i>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Mínimo 6 caracteres"
                        minlength="6"
                        required>
                    <i class="material-icons toggle-password-btn" id="toggle-password">visibility_off</i>
                </div>
            </div>

            <div class="native-input-group">
                <label for="password_confirmation" class="native-label">Confirme a Senha</label>
                <div class="input-with-icon">
                    <i class="material-icons input-icon">lock_reset</i>
                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        placeholder="Repita a senha digitada"
                        minlength="6"
                        required>
                    <i class="material-icons toggle-password-btn" id="toggle-password-confirm">visibility_off</i>
                </div>
            </div>


            <button type="submit" class="btn-login-premium">
                Cadastrar
            </button>
        </form>


        <div class="login-footer-navigation">
            <p class="signup-text-wrapper">
                Já tem uma conta?
                <a href="{{ route('login') }}" class="footer-nav-link registration-link">
                    Faça Login
                </a>
            </p>
        </div>


    </div>
</div>

@endsection
