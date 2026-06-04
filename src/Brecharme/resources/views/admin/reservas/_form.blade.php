<div class="form-group">
    <label for="id_usuario">Cliente / Usuário</label>
    <select name="id_usuario" id="id_usuario" required>
        <option value="" disabled {{ !isset($linha) ? 'selected' : '' }}>Selecione um cliente...</option>
        @foreach($usuarios as $usuario)
            <option value="{{ $usuario->id }}" >
                {{ (isset($linha) && $linha->fk_compra_id_usuario == $usuario->id) ? 'selected' : '' }}>
                {{ $usuario->name }} ({{ $usuario->email }})
            </option>
        @endforeach
    </select>
</div>

@if(!isset($linha))
    <div class="form-group">
        <label for="id_produto">Produtos a serem Reservados (Segure CTRL para selecionar mais de um)</label>
        <select name="id_produto[]" id="id_produto" multiple required style="height: 120px;">
            @foreach($produtos as $produto)
                <option value="{{ $produto->id_produto }}">{{ $produto->nome }} - R$ {{ number_format($produto->valor, 2, ',', '.') }}</option>
            @endforeach
        </select>
    </div>
@endif

@if(isset($linha))
    <div class="form-group">
        <label>Data da Reserva/Compra</label>
        <input type="text" value="{{ $linha->data_compra ? $linha->data_compra->format('d/m/Y H:i') : '' }}" readonly disabled>
    </div>
@endif

<div class="form-group">
    <label for="status">Status da Reserva/Compra</label>
    <select name="status" id="status" required>
        <option value="Reservado" {{ (isset($linha) && $linha->status == 'Reservado') ? 'selected' : '' }}>Reservado</option>
        <option value="Carrinho" {{ (isset($linha) && $linha->status == 'Carrinho') ? 'selected' : '' }}>Carrinho</option>
        <option value="Concluída" {{ (isset($linha) && $linha->status == 'Concluída') ? 'selected' : '' }}>Concluída</option>
        <option value="Cancelada" {{ (isset($linha) && $linha->status == 'Cancelada') ? 'selected' : '' }}>Cancelada</option>
    </select>
</div>

<input type="hidden" name="sessao" value="{{ $linha->sessao ?? session()->getId() }}">