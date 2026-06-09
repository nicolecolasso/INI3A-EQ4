@extends('layout.site')


@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endpush


@section('titulo', 'Recuperar Senha')


@section('conteudo')
<div class="recuperar-senha-wrapper">
   
    <div class="auth-card-content">
       
        <i class="material-icons auth-icon">gpp_good</i>
        <h2 class="auth-title">Nova Senha</h2>
        <p class="auth-subtitle">
            Tudo certo! Agora digite e confirme a sua nova senha de acesso abaixo.
        </p>

        @if ($errors->any())
            <div class="auth-error-block">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Formulário que bate com o padrão do Fortify/Auth do Laravel --}}
        <form action="{{ route('password.update') }}" method="POST" class="auth-form">
            @csrf
           
            {{-- Campos ocultos cruciais injetados pelo controller --}}
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">


            {{-- Campo: Nova Senha --}}
            <div class="auth-fieldset">
                <label for="password" class="auth-label">Nova Senha</label>
                <input type="password" name="password" id="password" required placeholder="Digite a nova senha" class="auth-input">
            </div>


            {{-- Campo: Confirmação --}}
            <div class="auth-fieldset">
                <label for="password_confirmation" class="auth-label">Confirme a Nova Senha</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required placeholder="Repita a nova senha" class="auth-input">
            </div>


            {{-- Botão Principal Amarelo com contorno preto rígido --}}
            <button type="submit" class="btn-alterar-senha">
                <i class="material-icons">published_with_changes</i> Alterar Senha
            </button>
        </form>


    </div>
</div>
@endsection
