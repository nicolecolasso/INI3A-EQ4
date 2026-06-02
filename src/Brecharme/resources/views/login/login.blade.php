@extends('layout.site')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endpush

@section('titulo', 'Login')

@section('conteudo')

<div class="container">
    <div class="row" style="margin-top:50px; margin-bottom:50px;">

        <div class="col s12 m8 offset-m2 l6 offset-l3">
            <div class="card">
                <div class="card-content">

                    <span class="card-title center">
                        <h4>Entrar</h4>
                    </span>

                    <form action="{{ route('login') }}" method="POST">
                        {{ csrf_field() }}

                        <div class="input-field">
                            <input
                                type="email"
                                id="email"
                                name="email"
                                required>
                            <label for="email">E-mail</label>
                        </div>

                        <div class="input-field">
                            <input
                                type="password"
                                id="senha"
                                name="senha"
                                required>
                            <label for="senha">Senha</label>
                        </div>

                        <div class="center">
                            <button
                                type="submit"
                                class="btn deep-orange">
                                Entrar
                            </button>
                        </div>

                    </form>

                    <div class="center" style="margin-top:20px;">
                        <a href="{{ url('/recuperar-senha') }}">
                            Esqueci minha senha
                        </a>
                    </div>

                    <div class="center" style="margin-top:10px;">
                        Não possui cadastro?
                        <a href="{{ url('/novo-cadastro') }}">
                            Cadastre-se
                        </a>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>

@endsection