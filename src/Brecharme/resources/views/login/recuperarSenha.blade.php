@extends('layout.site')
@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endpush
@section('titulo', 'Recuperar Senha')
@section('conteudo')
    {{-- 
      Exibe a tela para digitar a nova senha vindo do e-mail (GET /recuperar-senha/{token})
       O QUE DEVE TER NO BLADE DESTA VIEW (Ex: login/recuperarSenha.blade.php):
      - Um <form action="{{ route('password.update') }}" method="POST"> com @csrf
      - DOIS campos ocultos cruciais recebidos via Controller:
      <input type="hidden" name="token" value="{{ $token }}">
      <input type="hidden" name="email" value="{{ $email }}">
      - <input type="password" name="password" required placeholder="Nova Senha">
      - <input type="password" name="password_confirmation" required placeholder="Confirme a Nova Senha">
      - Um <button type="submit">Alterar Senha</button>
      Lembrar que tudo tem que ser reponsivo
    --}}
@endsection