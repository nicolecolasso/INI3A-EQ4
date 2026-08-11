@extends('layout.site')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/form.css') }}">
@endpush

@section('titulo', 'Cadastrar Usuário')

@section('conteudo')
<div class="form-container-admin">
    <h3 class="form-titulo">Adicionar Novo Usuário</h3>
    
    <div class="row-form">
        <form action="{{ route('admin.usuarios.salvar') }}" method="post">
            {{ csrf_field() }}
            
            @include('admin.usuarios._form')

            @if ($errors->any())
                <div class="alert-danger" style="background-color: #f8d7da; color: #721c24; padding: 12px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
                    <ul style="margin: 0; padding-left: 20px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <div class="form-actions">
                <button class="btn-submit dark-style">Salvar Usuário</button>
                <a href="{{ route('admin.usuarios') }}" class="btn-cancel">Voltar</a>
            </div>
        </form>
    </div>
</div>
@endsection