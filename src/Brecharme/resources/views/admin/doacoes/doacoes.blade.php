@extends('layout.site')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@endpush

@section('titulo', 'Doações')

@section('conteudo')
<div class="admin-table-container">
    
    <header class="table-header-box">
        <h2>Gerenciamento de Doações</h2>
        <a href="{{ route('admin.doacoes.novaDoacao') }}" class="btn-add-table">
            <i class="material-icons">add_box</i> Nova Doação
        </a>
    </header>

    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Foto</th>
                    <th>Nome do Item</th>
                    <th>Doador e Retirada</th> 
                    <th>Detalhes</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($linhas as $linha)
                    <tr class="{{ in_array($linha->status, ['Rejeitada', 'Retirada']) ? 'row-user-inactive' : '' }}">
                        <td data-label="ID">#{{ $linha->id_doacao }}</td>
                        <td data-label="Foto">
                            <div class="product-img-thumbnail">
                                <img src="{{ asset($linha->caminho_img ) }}" alt="Doação">
                            </div>
                        </td>

                        <td data-label="Nome do Item">
                            <div class="admin-strong-text">
                                {{ $linha->nome ?? 'Doação sem nome' }} 
                            </div>
                        </td>
                        
                        <td data-label="Doador e Retirada">
                            <strong>{{ $linha->usuario->name ?? 'Doador Anônimo' }}</strong>
                            <div class="admin-subtext">
                                <i class="material-icons icon-inline">phone</i> 
                                {{ $linha->usuario->telefone ?? 'Sem telefone' }}
                            </div>
                            <div class="admin-location-tag">
                                <i class="material-icons icon-inline">location_on</i> 
                                Retirada em: {{ $linha->localizacao ?? 'Não informada' }}
                            </div>
                        </td>
                        
                        <td data-label="Detalhes">
                            <span class="admin-category-tag">
                                {{ $linha->categoria }}
                            </span>
                            <div class="admin-description-text">
                                {{ Str::limit($linha->descricao, 50, '...') }}
                            </div>
                        </td>
                        <td>
                            @if($linha->status == 'Analise')
                                <span class="badge badge-analise">
                                    Em Análise
                                </span>
                            @elseif($linha->status == 'Aprovada')
                                <span class="badge badge-user">Aprovada</span>
                            @elseif($linha->status == 'Retirada')
                                <span class="badge badge-retirada">
                                    Retirada
                                </span>
                            @else
                                <span class="badge badge-inactive">Rejeitada</span>
                            @endif
                        </td>
                        <td data-label="Ações">
                            <div class="action-buttons-flex">
                                @if($linha->status == 'Analise')
                                    <a href="{{ route('admin.doacoes.aprovar', $linha->id_doacao) }}" 
                                       class="btn-action btn-action-approve confirm-action" 
                                       data-confirm="Deseja aprovar esta doação? Ela ficará aguardando retirada."
                                       title="Aprovar Doação">
                                        <i class="material-icons">check</i>
                                    </a>
                                    
                                    <a href="{{ route('admin.doacoes.rejeitar', $linha->id_doacao) }}" 
                                       class="btn-action delete confirm-action" 
                                       data-confirm="Deseja rejeitar esta doação?"
                                       title="Rejeitar Doação">
                                        <i class="material-icons">close</i>
                                    </a>
                                @endif

                                @if($linha->status == 'Aprovada')
                                    <button type="button" class="btn-action btn-action-ship btn-open-modal-ship" 
                                            title="Confirmar Retirada e Virar Produto"
                                            data-doacao-id="{{ $linha->id_doacao }}"
                                            data-doacao-nome="{{ addslashes($linha->nome) }}">
                                        <i class="material-icons">local_shipping</i>
                                    </button>
                                @endif

                                <a href="{{ route('admin.doacoes.editarDoacao', $linha->id_doacao) }}" class="btn-action edit" title="Editar / Ver Detalhes">
                                    <i class="material-icons">edit</i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="empty-table">Nenhuma doação cadastrada no sistema.</td> {{-- 🎯 Reajustado para colspan="7" --}}
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div id="modalPreco" class="modal-preco">
    <div class="modal-conteudo">
        <h3>Confirmar Retirada e Definir Preço</h3>
        <p id="modalTextoItem" class="modal-text"></p>
        
        <form id="formRetirarPreco" action="" method="POST">
            @csrf
            <label for="preco_venda">Valor de Venda (R$):</label>
            <input type="number" name="preco" id="preco_venda" step="0.01" min="0" placeholder="0,00" required autofocus>
            
            <div class="modal-botoes">
                <button type="button" class="btn btn-secondary btn-close-modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Confirmar e Cadastrar</button>
            </div>
        </form>
    </div>
</div>
@endsection