@extends('layout.site')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/form.css') }}">
@endpush

@section('titulo', 'Editar Doação')

@section('conteudo')
<div class="form-container-admin">
    <h3 class="form-titulo">Editar Doação: {{ $linha->categoria }}</h3>
    
    <div class="row-form">
        <form action="{{ route('admin.doacoes.atualizar', ['id' => $linha->id_doacao]) }}" method="post" enctype="multipart/form-data">
            {{ csrf_field() }}
            
            {{ method_field('put') }}
            
            @include('admin.doacoes._form')
            
            <div class="form-actions">
                <button class="btn-submit dark-style">Atualizar Dados</button>
                <a href="{{ route('admin.doacoes') }}" class="btn-cancel">Voltar</a>
            </div>
        </form>
    </div>
</div>
@endsection
@push('scripts')
    <script src="{{ asset('js/script.js') }}"></script>
@endpush