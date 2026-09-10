@extends('layout.site')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/form.css') }}">
@endpush

@section('titulo', 'Novo Comunicado')

@section('conteudo')
<div class="form-container-admin">
    <h3 class="form-titulo">Novo Comunicado (WhatsApp)</h3>

    <div class="row-form">
        <form action="{{ route('admin.comunicados.salvar') }}" method="post">
            {{ csrf_field() }}
            
            @include('admin.comunicados._form')
            
            <div class="form-actions">
                <button class="btn-primary"> Disparar Comunicado</button>
                <a href="{{ route('admin.gerenciar') }}" class="btn-secondary">Voltar</a>
            </div>
        </form>
    </div>
</div>
@endsection