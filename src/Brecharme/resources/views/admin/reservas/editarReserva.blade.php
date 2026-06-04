@extends('layout.site')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/form.css') }}">
@endpush

@section('titulo', 'Editar Reserva/Compra')

@section('conteudo')
<div class="form-container-admin">
    <h3 class="form-titulo">Editar Reserva/Compra: {{ $linha->id_reserva }}</h3>
    
    <div class="row-form">
        <form action="{{ route('admin.reservas.atualizar', $linha->id) }}" method="post">
            {{ csrf_field() }}
            
            {{ method_field('put') }}
            
            @include('admin.reservas._form')
            
            <div class="form-actions">
                <button class="btn-submit dark-style">Atualizar Dados</button>
                <a href="{{ route('admin.reservas') }}" class="btn-cancel">Voltar</a>
            </div>
        </form>
    </div>
</div>
@endsection