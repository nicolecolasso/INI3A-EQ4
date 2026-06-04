{{-- Campo de Usuário/Cliente --}}
<div class="form-group">
    <label for="id_usuario">Cliente / Usuário</label>
    <select name="id_usuario" id="id_usuario" required>
        <option value="" disabled {{ !isset($linha) ? 'selected' : '' }}>Selecione um cliente...</option>
        @foreach($usuarios as $usuario)
            {{-- 🎯 CORRIGIDO: Sintaxe do selected limpa e dentro da tag <option> --}}
            <option value="{{ $usuario->id }}" {{ (isset($linha) && $linha->fk_compra_id_usuario == $usuario->id) ? 'selected' : '' }}>
                {{ $usuario->name }} ({{ $usuario->email }})
            </option>
        @endforeach
    </select>
</div>

{{-- Campo de Produtos (Agora liberado para Novo E Editar) --}}
<div class="form-group">
    <label for="id_produto">Produtos da Reserva (Segure CTRL para selecionar/desmarcar)</label>
    <select name="id_produto[]" id="id_produto" multiple required style="height: 150px;">
        @foreach($produtos as $produto)
            @php
                // VERIFICAÇÃO: Checa se o produto atual já está vinculado a esta compra através dos itens
                $jaSelecionado = false;
                if(isset($linha) && isset($linha->itens)) {
                    $jaSelecionado = $linha->itens->contains('fk_id_produto', $produto->id_produto);
                }
            @endphp
            <option value="{{ $produto->id_produto }}" {{ $jaSelecionado ? 'selected' : '' }}>
                {{ $produto->nome }} - R$ {{ number_format($produto->valor, 2, ',', '.') }} 
                {{ $jaSelecionado ? '📌 (Já selecionado)' : '' }}
            </option>
        @endforeach
    </select>
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

<input type="hidden" name="sessao" value="{{ $linha->sessao ?? session()->getId() }}">