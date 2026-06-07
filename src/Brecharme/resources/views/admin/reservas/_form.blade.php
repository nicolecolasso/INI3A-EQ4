{{-- Campo de Usuário/Cliente --}}
<div class="form-group">
    <label for="id_usuario">Cliente / Usuário</label>
    <select name="id_usuario" id="id_usuario" required>
        <option value="" disabled {{ !isset($linha) ? 'selected' : '' }}>Selecione um cliente...</option>
        @foreach($usuarios as $usuario)
            <option value="{{ $usuario->id }}" {{ (isset($linha) && $linha->fk_compra_id_usuario == $usuario->id) ? 'selected' : '' }}>
                {{ $usuario->name }} ({{ $usuario->email }})
            </option>
        @endforeach
    </select>
</div>

{{-- Campo de Seleção Múltipla de Produtos --}}
<div class="form-group">
    <label for="id_produto">Produtos da Reserva (Segure CTRL no Windows ou CMD no Mac para selecionar/desmarcar)</label>
    <select name="id_produto[]" id="id_produto" multiple required class="multiselect-tall multiselect-reservas">
        @foreach($produtos as $produto)
            @php
                $jaSelecionado = false;
                if(isset($linha) && isset($linha->produtos)) {
                    $jaSelecionado = $linha->produtos->contains('id_produto', $produto->id_produto);
                }
            @endphp
            <option value="{{ $produto->id_produto }}" {{ $jaSelecionado ? 'selected' : '' }}>
                @if($jaSelecionado)
                    📌 [RESERVADO NESTA COMPRA] - {{ $produto->nome }} (R$ {{ number_format($produto->valor, 2, ',', '.') }})
                @else
                    {{ $produto->nome }} (R$ {{ number_format($produto->valor, 2, ',', '.') }})
                @endif
            </option>
        @endforeach
    </select>
    <small class="form-text text-muted form-text-helper">
        Aviso: Itens desmarcados que já pertenciam a esta reserva voltarão automaticamente a ficar disponíveis após salvar.
    </small>
</div>

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