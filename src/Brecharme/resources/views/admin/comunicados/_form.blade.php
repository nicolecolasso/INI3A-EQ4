<div class="form-group">
    <label for="assunto">Assunto / Título do Comunicado</label>
    <input type="text" name="assunto" id="assunto" value="{{ $linha->assunto ?? '' }}" required placeholder="Ex: Mega Bazar no Brecharme!">
</div>

<div class="form-group">
    <label for="mensagem">Mensagem para o WhatsApp</label>
    <textarea name="mensagem" id="mensagem" rows="12" required placeholder="Digite aqui o texto do comunicado que os clientes vão receber no celular...">{{ $linha->mensagem ?? '' }}</textarea>
</div>