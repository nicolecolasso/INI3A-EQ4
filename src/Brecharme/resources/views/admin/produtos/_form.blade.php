<div style="display: flex; gap: 20px; flex-wrap: wrap; margin-bottom: 24px;">
    
    <div class="form-group" style="flex: 1; min-width: 200px;">
        <label for="valor">Preço de Venda (R$)</label>
        <input type="number" name="valor" id="valor" step="0.01" value="{{ $linha->valor ?? '' }}" required placeholder="0,00">
    </div>

    <div class="form-group" style="flex: 1; min-width: 200px;">
        <label for="arquivo">Foto do Produto</label>
        <input type="file" name="arquivo" id="arquivo" {{ isset($linha) ? '' : 'required' }} accept="image/*">
        
        @if(isset($linha->caminho_img))
            <div style="margin-top: 8px; font-size: 12px; color: #666;">
                📸 Já existe uma foto salva. Selecione outra apenas se quiser mudar.
            </div>
        @endif
    </div>
</div>