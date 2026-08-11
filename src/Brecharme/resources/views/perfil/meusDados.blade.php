@extends('layout.site')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/perfil.css') }}">
@endpush

@section('titulo', 'Meus Dados')

@section('conteudo')
<div class="perfil-wrapper dados-wrapper-layout">
   
    {{-- Alerta de Sucesso no Perfil --}}
    @if (session('sucesso'))
        <div class="alert-success">
            <i class="material-icons">check_circle</i>
            <span>{{ session('sucesso') }}</span>
        </div>
    @endif

    {{-- Alerta de Erro no Perfil --}}
    @if (session('erro'))
        <div class="alert-danger">
            <i class="material-icons">error_outline</i>
            <span>{{ session('erro') }}</span>
        </div>
    @endif

    {{-- Lista de Erros de Validação (Ex: E-mail em uso, senha muito curta) --}}
    @if ($errors->any())
        <div class="alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="perfil-grid dados-grid-layout">
       
        <div class="perfil-card dados-usuario-card">
            <div class="avatar-container avatar-dados-margin">
                <i class="material-icons icon-avatar-dourado">account_circle</i>
            </div>
            <h3 class="nome-usuario-titulo">{{ $usuario->name }}</h3>
            <p class="subtitulo-usuario-texto">Alterando informações pessoais</p>
           
            <hr class="divisor-dados-hr">
           
            <a href="{{ route('perfil.meuPerfil') }}" class="btn-voltar-perfil btn-voltar-custom">
                <i class="material-icons icon-seta-voltar">arrow_back</i>
                Voltar ao Perfil
            </a>
        </div>

        <div class="perfil-dashboard dados-dashboard-card">
           
            <h2 class="titulo-meus-dados">Meus Dados</h2>

            <form action="{{ route('perfil.atualizarDados') }}" method="POST">
                @csrf                
                <div class="form-sections-grid grid-form-colunas">
                   
                    {{-- Coluna 1: Dados Pessoais --}}
                    <div class="form-column coluna-form-bloco">
                        <h4 class="subtitulo-secao-form">Dados pessoais</h4>
                       
                        <div class="input-block bloco-input-layout">
                            <label for="name" class="label-dados-form">Nome:</label>
                            <input type="text" id="name" name="name" value="{{ old('name', $usuario->name) }}" required class="input-field input-montserrat" placeholder="Seu nome completo">
                        </div>

                        <div class="input-block bloco-input-layout">
                            <label for="email" class="label-dados-form">Email:</label>
                            <input type="email" id="email" name="email" value="{{ old('email', $usuario->email) }}" required class="input-field input-montserrat" placeholder="Seu e-mail">
                        </div>

                        <div class="input-block bloco-input-layout">
                            <label for="telefone" class="label-dados-form">Telefone:</label>
                            <input type="text" id="telefone" name="telefone" value="{{ old('telefone', $usuario->telefone) }}" class="input-field input-montserrat" placeholder="(14) 99999-9999">
                        </div>

                        <div class="input-block bloco-input-layout checkbox-container">
                            <input type="checkbox" id="receber_avisos" name="receber_avisos" value="1" {{ old('receber_avisos', $usuario->receber_avisos) ? 'checked' : '' }} class="checkbox-field">
                            <label for="receber_avisos" class="label-dados-form label-checkbox">Desejo receber mensagens</label>
                        </div>
                    </div>

                    {{-- Coluna 2: Alterar Senha (Opcional) --}}
                    <div class="form-column coluna-form-bloco">
                        <h4 class="subtitulo-secao-form">Alterar Senha:</h4>
                       
                        <div class="input-block bloco-input-layout">
                            <input type="password" name="senha_atual" class="input-field input-montserrat" placeholder="Digite a senha atual">
                        </div>

                        <div class="input-block bloco-input-layout">
                            <input type="password" name="nova_senha" class="input-field input-montserrat" placeholder="Digite a nova senha">
                        </div>

                        <div class="input-block bloco-input-layout">
                            <input type="password" name="nova_senha_confirmation" class="input-field input-montserrat" placeholder="Confirme a nova senha">
                        </div>
                    </div>

                </div>

                <div class="form-actions btn-container-salvar">
                    <button type="submit" class="btn-salvar-dourado btn-salvar-custom-dados">
                        Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection