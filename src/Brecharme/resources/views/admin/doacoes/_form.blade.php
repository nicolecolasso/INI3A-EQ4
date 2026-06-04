<div class="input-field">
    <label for="nome">Nome do Item</label>
    <input type="text" name="nome" id="nome" value="{{ isset($linha->nome) ? $linha->nome : '' }}" required placeholder="Digite o nome do item doado">
</div>

<div class="input-field">
    <label for="categoria">Categoria da Doação</label>
    <select name="categoria" id="categoria" required>
        <option value="Outros" {{ (isset($linha->categoria) && $linha->categoria == 'Outros') ? 'selected' : '' }}>Outros</option>
        <option value="Roupas" {{ (isset($linha->categoria) && $linha->categoria == 'Roupas') ? 'selected' : '' }}>Roupas</option>
        <option value="Calçados" {{ (isset($linha->categoria) && $linha->categoria == 'Calçados') ? 'selected' : '' }}>Calçados</option>
        <option value="Acessórios" {{ (isset($linha->categoria) && $linha->categoria == 'Acessórios') ? 'selected' : '' }}>Acessórios</option>
        <option value="Eletrônicos" {{ (isset($linha->categoria) && $linha->categoria == 'Eletrônicos') ? 'selected' : '' }}>Eletrônicos</option>
        <option value="Móveis" {{ (isset($linha->categoria) && $linha->categoria == 'Móveis') ? 'selected' : '' }}>Móveis</option>
        <option value="Brinquedos" {{ (isset($linha->categoria) && $linha->categoria == 'Brinquedos') ? 'selected' : '' }}>Brinquedos</option>
    </select>
</div>

<div class="input-field">
    <label for="descricao">Descrição do Item</label>
    <textarea name="descricao" id="descricao" required placeholder="Descreva os detalhes do item doado...">{{ isset($linha->descricao) ? $linha->descricao : '' }}</textarea>
</div>

<div class="input-field">
    <label for="caminho_img">Foto do Item</label>
    <input type="file" name="caminho_img" id="caminho_img" accept="image/*" {{ isset($linha) ? '' : 'required' }}>
    @if(isset($linha->caminho_img))
        <small style="display: block; margin-top: 5px; color: gray;">Já existe uma foto salva. Selecione outra apenas se quiser mudar.</small>
    @endif
</div>

<div class="input-field">
    <label for="localizacao">Localização</label>
    <input type="text" name="localizacao" id="localizacao" value="{{ isset($linha->localizacao) ? $linha->localizacao : '' }}" required placeholder="Digite o ponto de retirada ou endereço">
</div>

@if(isset($linha))
<div class="input-field">
    <label for="status">Status da Doação</label>
    <select name="status" id="status" required>
        <option value="Analise" {{ $linha->status == 'Analise' ? 'selected' : '' }}>Analise</option>
        <option value="Aprovada" {{ $linha->status == 'Aprovada' ? 'selected' : '' }}>Aprovada</option>
        <option value="Rejeitada" {{ $linha->status == 'Rejeitada' ? 'selected' : '' }}>Rejeitada</option>
        <option value="Retirada" {{ $linha->status == 'Retirada' ? 'selected' : '' }}>Retirada</option>
    </select>
</div>
@endif