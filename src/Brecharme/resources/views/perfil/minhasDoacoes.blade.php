@extends('layout.site')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/perfil.css') }}">
@endpush

@section('titulo', 'Minhas Doações')

@section('conteudo')
    {{-- 
      Lista o histórico de propostas de doações enviadas pelo usuário (GET /perfil/minhasDoacoes)
      
      O QUE DEVE TER NO BLADE DESTA VIEW CONFORME O PROTÓTIPO:
      - Título principal: "Minhas doações:"
      - Exibição de alertas de sucesso ou erro (Ex: session('sucesso') ou session('erro'))
      
      - Laço condicional @if($doacoes->isEmpty()) para exibir mensagem caso não existam dados.
      - Caso existam, fazer um @foreach($doacoes as $doacao) para renderizar os blocos acinzentados:
        
        Cada bloco cinza escuro deve conter internamente:
        - Lado Esquerdo:
          - Linha contendo o rótulo "Categoria:" + uma caixinha cinza escura com o valor da categoria (Ex: {{ $doacao->produto->categoria ?? 'Móvel' }})
          - Linha contendo o rótulo "Data:"      + uma caixinha cinza escura com a data formatada: {{ $doacao->created_at->format('d/m/Y') }}
        
        - Centro/Meio:
          - Linha contendo o rótulo "Status:"    + uma caixinha cinza escura com o status atual: {{ $doacao->status }}
          - Abaixo do status, o botão oval para cancelar se o status for igual a 'Analise':
            @if($doacao->status === 'Analise')
                <a href="{{ route('perfil.minhasDoacoes.cancelar', $doacao->id) }}" class="btn-cancelar-perfil">Cancelar Doação</a>
            @endif
            
        - Lado Direito:
          - A miniatura/imagem do produto enviado para a doação com bordas escuras e cantos arredondados ou retos.
          
      - Abaixo de toda a listagem de cards, deve haver um botão grande e vazado (ou Amarelo) para propor uma nova doação:
        <a href="{{ route('produtos.novaDoacao') }}" class="btn-nova-doacao-clear">Nova Doação</a>
        Lembrar que tudo tem que ser reponsivo
    --}}
@endsection