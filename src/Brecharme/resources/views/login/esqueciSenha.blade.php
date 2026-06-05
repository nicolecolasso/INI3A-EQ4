@extends('layout.site')
@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endpush
@section('titulo', 'Esqueci minha senha')
@section('conteudo')
    {{-- 
      Exibe a tela de "Esqueci minha senha"
      O QUE DEVE TER NO BLADE DESTA VIEW:
      - Exibição de alertas se houver na sessão:
        @if(session('status')) -> Exibir mensagem verde de sucesso
        @if(session('erro'))   -> Exibir mensagem vermelha de erro

      - Um <form action="{{ route('password.email') }}" method="POST"> com @csrf
      - <input type="email" name="email" required placeholder="Digite seu e-mail cadastrado">
      - Um <button type="submit">Enviar link de recuperação</button>
      Lembrar que tudo tem que ser reponsivo
    --}}
@endsection