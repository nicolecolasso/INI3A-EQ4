@extends('layout.site')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/form.css') }}">
@endpush

@section('titulo', 'Cadastrar Reserva/Compra')

@section('conteudo')
<div class="form-container-admin">
    <h3 class="form-titulo">Adicionar Nova Reserva/Compra</h3>
    
    <div class="row-form">
        <form action="{{ route('admin.reservas.salvar') }}" method="post">
            {{ csrf_field() }}
            
            @include('admin.reservas._form')
            
            <div class="form-actions">
                <button class="btn-submit dark-style">Salvar Reserva/Compra</button>
                <a href="{{ route('admin.reservas') }}" class="btn-cancel">Voltar</a>
            </div>
        </form>
    </div>
</div>
@endsection