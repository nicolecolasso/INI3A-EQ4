@extends('layout.site')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/form.css') }}">
@endpush

@section('titulo', 'Cadastrar Doação')

@section('conteudo')
<div class="form-container-admin">
    <h3 class="form-titulo">Adicionar Nova Doação</h3>
    
    <div class="row-form">
        <form action="{{ route('admin.doacoes.salvar') }}" method="post" enctype="multipart/form-data">
            {{ csrf_field() }}
            
            @include('admin.doacoes._form')
            
            <div class="form-actions">
                <button class="btn-submit dark-style">Salvar Doação</button>
                <a href="{{ route('admin.doacoes') }}" class="btn-cancel">Voltar</a>
            </div>
        </form>
    </div>
</div>
@endsection