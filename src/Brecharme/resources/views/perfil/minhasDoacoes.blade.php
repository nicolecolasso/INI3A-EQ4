@extends('layout.site')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/perfil.css') }}">
@endpush

@section('titulo', 'Minhas Doações')

@section('conteudo')
<div class="perfil-wrapper doacoes-container-main">
   
    {{-- Cabeçalho da Página com o botão rápido de voltar para o painel --}}
    <div class="doacoes-header-row">
        <h2 class="titulo-doacoes-seasons">Minhas doações:</h2>
        <a href="{{ route('perfil.meuPerfil') }}" class="link-voltar-perfil-dourado">
            <i class="material-icons">arrow_back</i> Voltar ao Perfil
        </a>
    </div>

    {{-- Alertas de Sucesso --}}
    @if (session('sucesso'))
        <div class="alert-success alert-doacoes-sucesso">
            <i class="material-icons">check_circle</i>
            <span>{{ session('sucesso') }}</span>
        </div>
    @endif

    {{-- Alertas de Erro --}}
    @if (session('erro'))
        <div class="alert-error alert-doacoes-erro">
            <i class="material-icons">error_outline</i>
            <span>{{ session('erro') }}</span>
        </div>
    @endif

    {{-- Listagem de Doações --}}
    <div class="doacoes-list-wrapper">
        @if($doacoes->isEmpty())
            <div class="doacoes-vazia-card">
                <i class="material-icons icon-vazio-doacao">volunteer_activism</i>
                <p class="texto-vazio-doacao">Você ainda não realizou nenhuma proposta de doação.</p>
            </div>
        @else
            @foreach($doacoes as $doacao)
                {{-- Card da Doação --}}
                <div class="doacao-card shadow-card-doacao">
                   
                    <div class="doacao-info-coluna">
                        <div class="doacao-item-meta">
                            <span class="label-meta-doacao">Categoria:</span>
                            <span class="badge-meta-dados">
                                {{ $doacao->categoria->nome ?? 'Geral' }}
                            </span>
                        </div>
                        <div class="doacao-item-meta">
                            <span class="label-meta-doacao">Data:</span>
                            <span class="badge-meta-dados">
                                {{ $doacao->created_at->format('d/m/Y') }}
                            </span>
                        </div>
                    </div>

                    <div class="doacao-status-coluna">
                        <div class="doacao-status-container">
                            <span class="label-meta-doacao">Status:</span>
                           
                            {{-- Classes dinâmicas injetadas com as cores originais do seu projeto --}}
                            @php
                                $statusClass = 'status-default';
                                if($doacao->status === 'Em Análise') {
                                    $statusClass = 'status-analise-custom';
                                } elseif($doacao->status === 'Aprovada' || $doacao->status === 'Integrada ao Estoque') {
                                    $statusClass = 'status-aprovado-custom';
                                } elseif($doacao->status === 'Recusada') {
                                    $statusClass = 'status-rejeitado-custom';
                                } elseif($doacao->status === 'Cancelada') { 
                                    $statusClass = 'status-cancelada-custom';
                                }
                            @endphp

                            <span class="badge-status-dinamico {{ $statusClass }}">
                                {{ $doacao->status }}
                            </span>
                        </div>

                        {{-- Botão de Cancelar se estiver em Análise --}}
                        @if($doacao->status === 'Em Análise')
                          <a href="{{ route('perfil.minhasDoacoes.cancelar', $doacao->id_doacao) }}" 
                            class="btn-cancelar-perfil-action" 
                            onclick="return confirm('Tem certeza que deseja cancelar esta proposta de doação?')">
                              <i class="material-icons">cancel</i> Cancelar Doação
                          </a>
                        @endif
                    </div>

                    <div class="doacao-imagem-coluna">
                      @if(!empty($doacao->caminho_img))
                        <img src="{{ asset($doacao->caminho_img) }}" 
                             alt="Imagem da doação" 
                             class="imagem-preview-doacao">
                      @else
                        {{-- Fallback caso o campo esteja vazio no banco --}}
                        <div class="fallback-preview-doacao">
                            <i class="material-icons">checkroom</i>
                        </div>
                      @endif
                    </div>

                </div>
            @endforeach
        @endif
    </div>

    {{-- Botão de Ação Inferior: Propor Nova Doação --}}
    <div class="doacoes-footer-actions">
        <a href="{{ route('produtos.novaDoacao') }}" class="btn-nova-doacao-clear btn-footer-dourado">
            Nova Doação
        </a>
    </div>

</div>
@endsection