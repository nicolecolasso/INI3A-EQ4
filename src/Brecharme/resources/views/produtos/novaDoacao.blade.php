@extends('layout.site')

@section('titulo', 'Nova Doação')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/produtos.css') }}">
@endpush

@section('conteudo')
<div class="doacao-container">
    <div class="doacao-card">
        <h1 class="doacao-title">Faça uma nova doação e contribua para o brechó!</h1>

        <form action="{{ route('produtos.salvarDoacao') }}" method="POST" enctype="multipart/form-data" class="doacao-form">
            @csrf
           
            <div class="form-group">
                <label for="nome">Nome do produto</label>
                <input type="text" name="nome" id="nome" placeholder="Ex: Sapato de Salto Preto, Camiseta Vintage..." required>
            </div>

            <div class="form-group">
                <label for="categoria">Selecione uma categoria</label>
                <div class="select-wrapper">
                    <select name="categoria" id="categoria" required>
                        <option value="" disabled selected>Selecione</option>
                        <option value="Roupas">Roupas</option>
                        <option value="Calçados">Calçados</option>
                        <option value="Acessórios">Acessórios</option>
                        <option value="Eletrônicos">Eletrônicos</option>
                        <option value="Móveis">Móveis</option>
                        <option value="Brinquedos">Brinquedos</option>
                        <option value="Outros">Outros</option>
                    </select>
                    <i class="material-icons select-icon">expand_more</i>
                </div>
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
                    <input type="file" name="caminho_img" id="inputImagem" accept="image/*" style="display: none;" required>
                    <button type="button" class="btn-selecionar" onclick="document.getElementById('inputImagem').click()">Selecionar</button>
                </div>
            </div>

            <div class="form-group">
                <label for="localizacao">Localização para retirada</label>
                <input type="text" name="localizacao" id="localizacao" placeholder="Rua, número, bairro e cidade" required>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-enviar">Enviar solicitação</button>
            </div>
        </form>
    </div>
</div>

@endsection
@push('scripts')
    <script src="{{ asset('js/script.js') }}"></script>
@endpush