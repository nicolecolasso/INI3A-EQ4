@extends('layout.site')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/form.css') }}">
@endpush

@section('titulo', 'Editar Usuário')

@section('conteudo')
<div class="form-container-admin">
    <h3 class="form-titulo">Editar Usuário: {{ $linha->name }}</h3>

    @if ($errors->any())
        <div class="alert-danger" style="background-color: #f8d7da; color: #721c24; padding: 12px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    <div class="row-form">
        <form action="{{ route('admin.usuarios.atualizar', $linha->id) }}" method="post">
            {{ csrf_field() }}
            
            {{ method_field('put') }}
            
            @include('admin.usuarios._form')
            
            <div class="form-actions">
                <button class="btn-submit dark-style">Atualizar Dados</button>
                <a href="{{ route('admin.usuarios') }}" class="btn-cancel">Voltar</a>
            </div>
        </form>
    </div>
</div>
@endsection