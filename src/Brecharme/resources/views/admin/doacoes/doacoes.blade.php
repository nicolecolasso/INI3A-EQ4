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
                    <th>Doador</th> 
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
                                <img src="{{ asset($linha->caminho_img) }}" alt="Doação">
                            </div>
                        </td>
                        <td data-label="Nome">
                            {{ $linha->nome }}
                            @if(in_array($linha->status, ['Rejeitada', 'Retirada']))
                                <span class="text-danger-bold">(Arquivado)</span>
                            @endif
                        </td>
                        <td data-label="Doador">{{ $linha->usuario->name ?? 'Doador Anônimo' }}</td>
                        <td data-label="Detalhes">{{ Str::limit($linha->descricao, 40) }}</td>
                        <td data-label="Status">
                            <span class="badge badge-{{ strtolower($linha->status) }}">{{ $linha->status }}</span>
                        </td>
                        <td data-label="Ações">
                            <div class="action-buttons-flex">
                                @if($linha->status == 'Analise')
                                    {{-- 🔥 CORREÇÃO: Enviando via PUT com o method field para alinhar com a rota --}}
                                    <form action="{{ route('admin.doacoes.atualizar', $linha->id_doacao) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="Aprovada">
                                        <button type="submit" class="btn-action check" title="Aceitar / Aprovar Doação" style="background-color: #2ec4b6; color: white;">
                                            <i class="material-icons">thumb_up</i>
                                        </button>
                                    </form>

                                    <form action="{{ route('admin.doacoes.rejeitar', $linha->id_doacao) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn-action delete" title="Recusar / Rejeitar Doação" style="background-color: #e71d36; color: white;">
                                            <i class="material-icons">thumb_down</i>
                                        </button>
                                    </form>
                                @endif

                                @if($linha->status == 'Aprovada')
                                    <button class="btn-action check btn-abrir-modal" 
                                            data-id="{{ $linha->id_doacao }}" 
                                            data-nome="{{ $linha->nome }}" 
                                            title="Confirmar Retirada e Precificar">
                                        <i class="material-icons">local_shipping</i>
                                    </button>
                                @endif

                                {{-- O link de edição só aparece se o status não for arquivado (Garante consistência) --}}
                                @if($linha->status !== 'Retirada')
                                    <a href="{{ route('admin.doacoes.editarDoacao', ['id' => $linha->id_doacao]) }}" class="btn-action edit" title="Editar / Ver Detalhes">
                                        <i class="material-icons">edit</i>
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="empty-table">Nenhuma doação cadastrada no sistema.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Estrutura do Modal de Precificação --}}
<div id="modalPreco" class="modal-preco">
    <div class="modal-conteudo">
        <h3>Confirmar Retirada e Definir Preço</h3>
        <p id="modalTextoItem" class="modal-text"></p>
        
        <form id="formRetirarPreco" action="" method="POST">
            @csrf
            <label for="preco_venda">Valor de Venda (R$):</label>
            <input type="number" name="preco" id="preco_venda" step="0.01" min="0" placeholder="0,00" required>
            
            <div class="modal-botoes">
                <button type="button" class="btn class-secondary btn-close-modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Confirmar e Cadastrar</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/script.js') }}"></script>
@endpush