<div class="input-field">
    <label for="nome">Nome do Item</label>
    <input type="text" name="nome" id="nome" value="{{ isset($linha->nome) ? $linha->nome : '' }}" required placeholder="Digite o nome do item doado">
</div>

<div class="form-group">
    <label for="categoria_nome">Categoria da Doação</label>
    <input type="text" 
           name="categoria_nome" 
           id="categoria_nome" 
           list="categorias_salvas" 
           value="{{ $linha->categoria->nome ?? '' }}" 
           required 
           placeholder="Digite para buscar ou criar uma nova...">
    
    <datalist id="categorias_salvas">
        @foreach($categorias as $categoria)
            <option value="{{ $categoria->nome }}">
        @endforeach
    </datalist>
</div>

<div class="input-field">
    <label for="descricao">Descrição do Item</label>
    <textarea name="descricao" id="descricao" required placeholder="Descreva os detalhes do item doado...">{{ isset($linha->descricao) ? $linha->descricao : '' }}</textarea>
</div>

<div class="input-field">
    <label>Foto do Item</label>
    <div class="upload-area">
        <div class="upload-preview">
            @if(isset($linha->caminho_img))
                <img src="{{ asset($linha->caminho_img) }}" alt="Foto do item" />
            @else
                <i class="material-icons">image</i>
            @endif
        </div>
        <input type="file" name="caminho_img" accept="image/*" {{ isset($linha) ? '' : 'required' }}>
        <button type="button" class="btn-selecionar">Selecionar</button>
    </div>
</div>

<div class="input-field checkbox-field">
    <input type="checkbox" name="necessita_retirada" id="necessita_retirada" value="1" 
           {{ (isset($linha->localizacao) && $linha->localizacao) ? 'checked' : '' }}>
    <label for="necessita_retirada">Solicitar que a equipe do Brechó faça a retirada no meu endereço</label>
</div>

{{-- Este bloco começará oculto por padrão se o checkbox não estiver marcado --}}
<div class="input-field" id="box-localizacao" style="display: none;">
    <label for="localizacao">Endereço Completo para Retirada</label>
    <input type="text" name="localizacao" id="localizacao" value="{{ $linha->localizacao ?? '' }}" placeholder="Rua, número, bairro e ponto de referência">
</div>

@if(isset($linha))
<div class="input-field">
    <label for="status">Status da Doação</label>
    <div class="select-wrapper">
        <select name="status" id="status" required>
            <option value="Em Análise" {{ $linha->status == 'Em Análise' ? 'selected' : '' }}>Em Análise</option>
            <option value="Aprovada" {{ $linha->status == 'Aprovada' ? 'selected' : '' }}>Aprovada (Aguardando Recebimento)</option>
            <option value="Integrada ao Estoque" {{ $linha->status == 'Integrada ao Estoque' ? 'selected' : '' }}>Integrada ao Estoque</option>
            <option value="Recusada" {{ $linha->status == 'Recusada' ? 'selected' : '' }}>Recusada</option>
            <option value="Cancelada" {{ $linha->status == 'Cancelada' ? 'selected' : '' }}>Cancelada</option>
        </select>
        <i class="material-icons select-icon">expand_more</i>
    </div>
</div>
@endif