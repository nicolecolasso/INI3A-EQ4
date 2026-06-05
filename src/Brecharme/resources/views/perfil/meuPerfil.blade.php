@extends('layout.site')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/perfil.css') }}">
@endpush

@section('titulo', 'Meu Perfil')

@section('conteudo')
    {{-- 
      Exibe o painel principal do perfil do usuário logado (GET /perfil/meuPerfil)
      
      O QUE DEVE TER NO BLADE DESTA VIEW CONFORME O PROTÓTIPO:
      - Título principal h2 ou h3: "Meu Perfil"
      - Mensagem de boas-vindas: "Olá, seja bem - vindo!"
      
      - Um grid ou bloco contendo os 6 botões cinzas arredondados com bordas escuras organizados em duas linhas:
        
        Linha 1 (Contadores):
        1. Botão "Reservas : {{ $totalReservas }}" -> Exibe a quantidade de reservas concluídas
        2. Botão "Doações : {{ $totalDoacoes }}"   -> Exibe a quantidade de doações concluídas
        3. Botão "Administrador"   -> @if($usuario->admin) <a href="{{ route('admin.gerenciar') }}" class="perfil-menu-btn">Administrador</a> @endif
        
        Linha 2 (Navegação):
        4. Botão "Meus Dados"       -> <a href="{{ route('perfil.meusDados') }}" class="perfil-menu-btn">Meus Dados</a>
        5. Botão "Minhas Reservas"  -> <a href="{{ route('perfil.minhasReservas') }}" class="perfil-menu-btn">Minhas Reservas</a>
        6. Botão "Minhas Doações"   -> <a href="{{ route('perfil.minhasDoacoes') }}" class="perfil-menu-btn">Minhas Doações</a>
           
      - Um botão centralizado de destaque (Amarelo) para efetuar o logout:
        <a href="{{ route('login.sair') }}" class="btn-perfil-action-yellow">Sair</a>
        Lembrar que tudo tem que ser reponsivo
    --}}
@endsection