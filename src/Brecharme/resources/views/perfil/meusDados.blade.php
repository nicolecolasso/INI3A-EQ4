@extends('layout.site')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/perfil.css') }}">
@endpush

@section('titulo', 'Meus Dados')

@section('conteudo')
    {{-- 
      Exibe a tela com as informações do usuário e troca de senha (GET /perfil/meusDados)
      
      O QUE DEVE TER NO BLADE DESTA VIEW CONFORME O PROTÓTIPO:
      - Exibição de alertas de sucesso ou erro vindos da sessão (Ex: session('sucesso') ou session('erro'))
      
      - Título principal: "Meus Dados"
      
      - Um <form action="{{ route('perfil.atualizarDados') }}" method="POST"> com @csrf
        O layout deve ser dividido em duas colunas horizontais paralelas:

        COLUNA 1: "Dados pessoais"
        - Alinhamento de Linha [ Rótulo "Nome:"     + Input cinza com valor: {{ $usuario->name }} ]
        - Alinhamento de Linha [ Rótulo "Email:"    + Input cinza com valor: {{ $usuario->email }} (Adicionar 'readonly' ou 'disabled') ]
        - Alinhamento de Linha [ Rótulo "Telefone:" + Input cinza com valor: {{ $usuario->telefone }} ]

        COLUNA 2: "Alterar Senha:"
        - Input cinza com placeholder exato: placeholder="Digite a senha atual" (name="senha_atual")
        - Input cinza com placeholder exato: placeholder="Digite a nova senha" (name="nova_senha")
        - Input cinza com placeholder exato: placeholder="Confirme a nova senha" (name="nova_senha_confirmation")

      - Um botão de envio (Amarelo) centralizado abaixo das colunas para processar o formulário:
        <button type="submit" class="btn-perfil-action-yellow">Salvar</button>
        Lembrar que tudo tem que ser reponsivo
    --}}
@endsection