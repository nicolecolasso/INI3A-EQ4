@extends('layout.site')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@endpush

@section('titulo', 'Editar Usuário')

@section('conteudo')
<div class="form-container-admin">
    <h3 class="form-titulo">Editando o Usuário</h3>
    
    <div class="row-form">
        <form action="{{ route('admin.usuarios.atualizar', $linha->id) }}" method="post">
            {{ csrf_field() }}
            <input type="hidden" name="_method" value="put">
            
            @include('admin.usuarios._form')
            
            <div class="form-actions">
                <button class="btn-submit dark-style">Atualizar Dados</button>
                <a href="{{ route('admin.usuarios') }}" class="btn-cancel">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection