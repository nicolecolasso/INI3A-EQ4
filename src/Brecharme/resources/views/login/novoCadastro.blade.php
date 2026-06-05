@extends('layout.site')
@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endpush
@section('titulo', 'Novo Cadastro')
@section('conteudo')
    {{-- 
     Exibe a tela de novo cadastro
     O QUE DEVE TER NO BLADE DESTA VIEW:
        Um <form action="{{ route('login.salvarCadastro') }}" method="POST"> com @csrf
        <input type="text" name="name" required placeholder="Nome Completo">
        <input type="email" name="email" required placeholder="E-mail">
        <input type="password" name="password" required placeholder="Senha (Mínimo 6 caracteres)">
        <input type="text" name="telefone" placeholder="Telefone (Opcional)">
        Um <button type="submit">Cadastrar</button>
        Lembrar que tudo tem que ser reponsivo
    --}}
@endsection