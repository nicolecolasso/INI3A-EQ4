<div class="input-field">
    <label for="name">Nome Completo</label>
    <input type="text" name="name" id="name" value="{{ isset($linha->name) ? $linha->name : '' }}" required placeholder="Digite o nome do usuário">
</div>

<div class="input-field">
    <label for="email">E-mail</label>
    <input type="email" name="email" id="email" value="{{ isset($linha->email) ? $linha->email : '' }}" required placeholder="exemplo@brecharme.com">
</div>

<div class="input-field">
    <label for="telefone">Telefone / WhatsApp</label>
    <input type="text" name="telefone" id="telefone" value="{{ isset($linha->telefone) ? $linha->telefone : '' }}" placeholder="(00) 00000-0000" maxlength="15" inputmode="numeric">
</div>

<div class="input-field">
    <label for="senha">Senha</label>
    <input type="password" name="senha" id="senha" {{ isset($linha) ? '' : 'required' }} placeholder="{{ isset($linha) ? 'Deixe em branco para não alterar' : 'Digite uma senha segura' }}">
</div>

<label class="checkbox-row">
    <input type="checkbox" name="receber_avisos" id="receber_avisos" value="1" {{ (isset($linha->receber_avisos) && $linha->receber_avisos) ? 'checked' : '' }}>
    <span>Deseja receber mensagens?</span>
</label>

<label class="checkbox-row">
    <input type="checkbox" name="admin" value="1" {{ (isset($linha->admin) && $linha->admin) ? 'checked' : '' }}>
    <span>Este usuário é Administrador?</span>
</label>