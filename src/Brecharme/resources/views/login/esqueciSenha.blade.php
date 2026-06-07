@extends('layout.site')


@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endpush


@section('titulo', 'Esqueci minha senha')


@section('conteudo')
<div class="esqueci-senha-wrapper" style="max-width: 450px; margin: 80px auto; padding: 0 20px; min-height: 50vh; font-family: 'Montserrat', sans-serif;">
   
    <div style="background: #ffffff; border: 1px solid #e8e8e8; border-radius: 12px; padding: 40px 30px; box-shadow: 0 8px 25px rgba(0,0,0,0.03); text-align: center;">
       
        <i class="material-icons" style="font-size: 48px; color: #efb810; margin-bottom: 15px;">lock_reset</i>
        <h2 style="font-size: 1.6rem; color: #333; margin: 0 0 10px 0; font-weight: 500;">Recuperar Senha</h2>
        <p style="font-size: 0.95rem; color: #666; margin: 0 0 30px 0; line-height: 1.5;">
            Digite o seu e-mail cadastrado abaixo. Enviaremos um link para você redefinir sua senha com segurança.
        </p>


        @if(session('status'))
            <div class="alert-success" style="background-color: #d4edda; color: #155724; padding: 12px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 8px; border: 1px solid #c3e6cb; font-size: 0.9rem; text-align: left;">
                <i class="material-icons" style="font-size: 20px;">check_circle</i>
                <span>{{ session('status') }}</span>
            </div>
        @endif


        @if(session('erro'))
            <div class="alert-error" style="background-color: #f8d7da; color: #721c24; padding: 12px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 8px; border: 1px solid #f5c6cb; font-size: 0.9rem; text-align: left;">
                <i class="material-icons" style="font-size: 20px;">error_outline</i>
                <span>{{ session('erro') }}</span>
            </div>
        @endif


        <form action="{{ route('password.email') }}" method="POST" style="display: flex; flex-direction: column; gap: 20px;">
            @csrf
           
            <div style="text-align: left; display: flex; flex-direction: column; gap: 8px;">
                <label for="email" style="font-size: 0.9rem; color: #444; font-weight: 500;">E-mail Cadastrado</label>
                <input type="email" name="email" id="email" required placeholder="Digite seu e-mail cadastrado" style="width: 100%; padding: 14px 16px; border: 1px solid #ccc; border-radius: 8px; font-size: 1rem; font-family: 'Montserrat', sans-serif; box-sizing: border-box; transition: border-color 0.2s; background: #fafafa;">
            </div>


            <button type="submit" class="btn-recuperar-senha" style="width: 100%; background-color: #efb810; color: #000; border: 2px solid #000; border-radius: 8px; padding: 14px; font-size: 1.05rem; font-weight: bold; font-family: 'Montserrat', sans-serif; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; justify-content: center; gap: 8px; margin-top: 5px;">
                <i class="material-icons">send</i> Enviar link de recuperação
            </button>
        </form>


        <div style="margin-top: 25px; border-top: 1px solid #eee; padding-top: 20px;">
            <a href="{{ route('login') }}" style="color: #efb810; text-decoration: none; font-size: 0.9rem; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
                <i class="material-icons" style="font-size: 16px;">arrow_back</i> Voltar para o Login
            </a>
        </div>
    </div>
</div>


<style>
    input:focus { border-color: #efb810 !important; outline: none; background: #fff !important; }
    .btn-recuperar-senha:hover { background-color: #efb810 !important; transform: translateY(-1px); }
    @media (max-width: 480px) {
        .esqueci-senha-wrapper { margin: 40px auto !important; }
        div[style*="padding: 40px 30px"] { padding: 30px 20px !important; }
    }
</style>
@endsection
