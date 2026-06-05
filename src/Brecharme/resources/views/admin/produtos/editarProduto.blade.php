@extends('layout.site')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/form.css') }}">
    <link rel="stylesheet" href="{{ asset('css/produtos.css') }}">
@endpush

@section('titulo', 'Editar Produto')

@section('conteudo')
<div class="form-container-admin"> <h3 class="form-titulo">Editar Produto: {{ $linha->nome }}</h3>
    
    <div class="row-form"> 
        <form action="{{ route('admin.produtos.atualizar', $linha->id_produto) }}" method="post" enctype="multipart/form-data">
            {{ csrf_field() }}
            {{ method_field('put') }}
            
            @include('admin.produtos._form')
            
            <div class="form-actions"> <button class="btn-submit dark-style">Atualizar Produto</button>
                <a href="{{ route('admin.produtos') }}" class="btn-cancel">Voltar</a>
            </div> 
        </form>
    </div> 
</div> 
@endsection
@push('scripts')
    <script src="{{ asset('js/script.js') }}"></script>
@endpush