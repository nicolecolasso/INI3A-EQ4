@extends('layout.site')

@section('titulo', 'Nova Doação')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/produtos.css') }}">
@endpush

@section('conteudo')
<div class="doacao-container">
    <div class="doacao-card">
        <h1 class="doacao-title">Faça uma nova doação e contribua para o brechó!</h1>

        @if ($errors->any())
            <div class="alert-danger" style="background-color: #f8d7da; color: #721c24; padding: 12px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('produtos.salvarDoacao') }}" method="POST" enctype="multipart/form-data" class="doacao-form">
            @csrf
           
            <div class="form-group">
                <label for="nome">Nome do produto</label>
                <input type="text" name="nome" id="nome" placeholder="Ex: Sapato de Salto Preto, Camiseta Vintage..." required>
            </div>

            <div class="form-group">
                <label for="categoria_nome">Categoria da Doação</label>
                <input type="text" 
                    name="categoria_nome" 
                    id="categoria_nome" 
                    list="categorias_salvas" 
                    value="{{ $doacao->categoria->nome ?? '' }}" 
                    required 
                    placeholder="Digite para buscar ou criar uma nova...">
                
                <datalist id="categorias_salvas">
                    @foreach($categorias as $categoria)
                        <option value="{{ $categoria->nome }}">
                    @endforeach
                </datalist>
            </div>

            <div class="form-group">
                <label for="descricao">Descrição do produto</label>
                <textarea name="descricao" id="descricao" placeholder="Descreva o estado do item, tamanho, etc." required></textarea>
            </div>

            <div class="form-group">
                <label>Selecione uma imagem do produto</label>
                <div class="upload-area">
                    <div class="upload-preview" id="uploadPreview">
                        <i class="material-icons">image</i>
                    </div>
                    <input type="file" name="caminho_img" id="inputImagem" accept="image/*" required>
                    <button type="button" class="btn-selecionar">Selecionar</button>
                </div>
            </div>

            <div class="form-group checkbox-field">
                <input type="checkbox" name="necessita_retirada" id="necessita_retirada" value="1">
                <label for="necessita_retirada">Solicitar que a equipe do Brechó faça a retirada no meu endereço</label>
            </div> 

            <div class="form-group" id="box-localizacao">
                <label for="localizacao">Localização para retirada</label>
                <input type="text" name="localizacao" id="localizacao" placeholder="Rua, número, bairro e cidade" >
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-enviar">Enviar solicitação</button>
            </div>
        </form>
    </div>
</div>

@endsection