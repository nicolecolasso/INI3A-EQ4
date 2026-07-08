<div class="form-group">
    <label for="nome">Nome do Produto</label>
    <input type="text" name="nome" id="nome" value="{{ $linha->nome ?? '' }}" required placeholder="Digite o nome do produto">
</div>

<div class="form-group">
    <label for="categoria_nome">Categoria do Produto</label>
    <input type="text" 
           name="categoria_nome" 
           id="categoria_nome" 
           list="categorias_salvas" 
           value="{{ $linha->categoria->nome ?? '' }}" 
           required 
           placeholder="Digite para buscar ou criar uma nova...">
    
    {{-- Auto-complete --}}
    <datalist id="categorias_salvas">
        @foreach($categorias as $categoria)
            <option value="{{ $categoria->nome }}">
        @endforeach
    </datalist>
</div>

<div class="form-group">
    <label for="descricao">Descrição do Produto</label>
    <textarea name="descricao" id="descricao" required placeholder="Descreva os detalhes do produto...">{{ $linha->descricao ?? '' }}</textarea>
</div>

<div class="form-group">
    <label for="valor">Preço de Venda (R$)</label>
    <input type="number" name="valor" id="valor" step="0.01" min="0" value="{{ $linha->valor ?? '' }}" required placeholder="0,00">
</div>

<div class="form-group">
    <label>Foto do Produto</label>
    <div class="upload-area">
        <div class="upload-preview">
            @if(isset($linha->caminho_img))
                <img src="{{ asset($linha->caminho_img) }}" alt="Foto do produto" />
            @else
                <i class="material-icons">image</i>
            @endif
        </div>
        <input type="file" name="caminho_img" accept="image/*" {{ isset($linha) ? '' : 'required' }}>
        <button type="button" class="btn-selecionar">Selecionar</button>
    </div>
</div>

@if(isset($linha))
<div class="form-group">
    <label for="status">Status do Produto</label>
    <div class="select-wrapper">
        <select name="status" id="status" required>
            <option value="Disponível" {{ $linha->status == 'Disponível' ? 'selected' : '' }}>Disponível</option>
            <option value="Carrinho" {{ $linha->status == 'Carrinho' ? 'selected' : '' }}>Carrinho</option>
            <option value="Reservado" {{ $linha->status == 'Reservado' ? 'selected' : '' }}>Reservado</option>
            <option value="Vendido" {{ $linha->status == 'Vendido' ? 'selected' : '' }}>Vendido</option>
        </select>
        <i class="material-icons select-icon">expand_more</i>
    </div>
</div>
@endif

@if(isset($linha->fk_produto_id_doacao))
    <input type="hidden" name="fk_produto_id_doacao" value="{{ $linha->fk_produto_id_doacao }}">
@endif