@extends('layout.site')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@endpush

@section('titulo', 'Cadastrar Usuário')

@section('conteudo')
<div class="form-container-admin">
    <h3 class="form-titulo">Adicionar Novo Usuário</h3>
    
    <div class="row-form">
        <form action="{{ route('admin.usuarios.salvar') }}" method="post">
            {{ csrf_field() }}
            
            @include('admin.usuarios._form')
            
            <div class="form-actions">
                <button class="btn-submit dark-style">Salvar Usuário</button>
                <a href="{{ route('admin.usuarios') }}" class="btn-cancel">Voltar</a>
            </div>
        </form>
    </div>
</div>
@endsection