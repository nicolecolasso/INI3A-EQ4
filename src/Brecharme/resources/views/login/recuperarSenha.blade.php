@extends('layout.site')


@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endpush


@section('titulo', 'Recuperar Senha')


@section('conteudo')
<div class="recuperar-senha-wrapper" style="max-width: 450px; margin: 80px auto; padding: 0 20px; min-height: 50vh; font-family: 'Montserrat', sans-serif;">
   
    <div style="background: #ffffff; border: 1px solid #e8e8e8; border-radius: 12px; padding: 40px 30px; box-shadow: 0 8px 25px rgba(0,0,0,0.03); text-align: center;">
       
        <i class="material-icons" style="font-size: 48px; color: #efb810; margin-bottom: 15px;">gpp_good</i>
        <h2 style="font-size: 1.6rem; color: #333; margin: 0 0 10px 0; font-weight: 500;">Nova Senha</h2>
        <p style="font-size: 0.95rem; color: #666; margin: 0 0 30px 0; line-height: 1.5;">
            Tudo certo! Agora digite e confirme a sua nova senha de acesso abaixo.
        </p>


        {{-- Formulário que bate com o padrão do Fortify/Auth do Laravel --}}
        <form action="{{ route('password.update') }}" method="POST" style="display: flex; flex-direction: column; gap: 20px;">
            @csrf
           
            {{-- Campos ocultos cruciais injetados pelo controller --}}
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">


            {{-- Campo: Nova Senha --}}
            <div style="text-align: left; display: flex; flex-direction: column; gap: 8px;">
                <label for="password" style="font-size: 0.9rem; color: #444; font-weight: 500;">Nova Senha</label>
                <input type="password" name="password" id="password" required placeholder="Digite a nova senha" style="width: 100%; padding: 14px 16px; border: 1px solid #ccc; border-radius: 8px; font-size: 1rem; font-family: 'Montserrat', sans-serif; box-sizing: border-box; transition: border-color 0.2s; background: #fafafa;">
            </div>


            {{-- Campo: Confirmação --}}
            <div style="text-align: left; display: flex; flex-direction: column; gap: 8px;">
                <label for="password_confirmation" style="font-size: 0.9rem; color: #444; font-weight: 500;">Confirme a Nova Senha</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required placeholder="Repita a nova senha" style="width: 100%; padding: 14px 16px; border: 1px solid #ccc; border-radius: 8px; font-size: 1rem; font-family: 'Montserrat', sans-serif; box-sizing: border-box; transition: border-color 0.2s; background: #fafafa;">
            </div>


            {{-- Botão Principal Amarelo com contorno preto rígido --}}
            <button type="submit" class="btn-alterar-senha" style="width: 100%; background-color: #efb810; color: #000; border: 2px solid #000; border-radius: 8px; padding: 14px; font-size: 1.05rem; font-weight: bold; font-family: 'Montserrat', sans-serif; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; justify-content: center; gap: 8px; margin-top: 10px;">
                <i class="material-icons">published_with_changes</i> Alterar Senha
            </button>
        </form>


    </div>
</div>


<style>
    input:focus { border-color: #efb810 !important; outline: none; background: #fff !important; }
    .btn-alterar-senha:hover { background-color: #efb810 !important; transform: translateY(-1px); }
    @media (max-width: 480px) {
        .recuperar-senha-wrapper { margin: 40px auto !important; }
        div[style*="padding: 40px 30px"] { padding: 30px 20px !important; }
    }
</style>
@endsection
