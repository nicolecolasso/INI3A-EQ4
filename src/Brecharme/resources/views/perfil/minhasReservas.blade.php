@extends('layout.site')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/perfil.css') }}">
@endpush

@section('titulo', 'Minhas Reservas')

@section('conteudo')
    {{-- 
      Lista o histórico de reservas e compras efetuadas pelo usuário (GET /perfil/minhasReservas)
      
      O QUE DEVE TER NO BLADE DESTA VIEW:
      - Exibição de alertas de sucesso ou erro (Ex: session('sucesso') ou session('erro'))

      - Verificação se existem compras/reservas na lista:
        @if($compras->isEmpty())
            <p>Você ainda não possui nenhuma reserva realizada.</p>
        @else
            - Uma tabela estruturada (.reserva-table) com as seguintes colunas de cabeçalho (<th>):
              Código (ID) | Data da Reserva | Valor Total | Status | Ações
              
            - Um laço @foreach($compras as $compra) para preencher as linhas (<tr>) da tabela:
              1. ID da Compra: {{ $compra->id }}
              2. Data formatada: {{ $compra->created_at->format('d/m/Y') }}
              3. Valor formatado: R$ {{ number_format($compra->valor_total, 2, ',', '.') }}
              4. Status do pedido: {{ $compra->status }}
              
              5. Coluna de Ações:
                 Um link para o usuário visualizar os detalhes daquela reserva específica (Ou para efetuar o cancelamento do item, caso a regra de negócio permita baseado no relacionamento com ProdutoReserva e o item ainda esteja em análise ou reservado, por exemplo, conferir os possíveis status que permitem cancelamento e criar uma rota específica para isso, como: route('perfil.minhasReservas.cancelar', $compra->id)):
                 
                 {{-- Exemplo de botão para ver detalhes ou acionar a rota de cancelamento se mapeado por item --}}
                 <a href="#" class="btn-table-details">Visualizar</a>
            @endforeach
        @endif
        Lembrar que tudo tem que ser reponsivo
    --}}
@endsection