@extends('layout.site')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/form.css') }}">
@endpush

@section('titulo', 'Cadastrar Produto')

@section('conteudo')
<div class="form-container-admin">
    <h3 class="form-titulo">Adicionar Novo Produto ao Acervo</h3>
    
    <div class="row-form">
        <form action="{{ route('admin.produtos.salvar') }}" method="post" enctype="multipart/form-data">
            {{ csrf_field() }}
            
            @include('admin.produtos._form')
            
            <div class="form-actions">
                <button class="btn-submit dark-style">Salvar Produto</button>
                <a href="{{ route('admin.produtos') }}" class="btn-cancel">Voltar</a>
            </div>
        </form>
    </div>
</div>
@endsection
@push('scripts')
    <script src="{{ asset('js/script.js') }}"></script>
@endpush