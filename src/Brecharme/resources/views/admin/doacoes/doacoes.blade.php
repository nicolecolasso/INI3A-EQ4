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
                        <td>#{{ $linha->id_doacao }}</td>
                        <td>
                            <div class="product-img-thumbnail">
                                <img src="{{ asset($linha->caminho_img ?? 'img/produto-placeholder.png') }}" alt="Doação">
                            </div>
                        </td>

                        <td>
                            <div style="font-size: 14px; color: #333; font-weight: 600;">
                                {{ $linha->nome ?? 'Doação sem nome' }} {{-- 🎯 Corrigido de $linha->doacao->nome para $linha->nome --}}
                            </div>
                        </td>
                        
                        <td>
                            <strong>{{ $linha->usuario->name ?? 'Doador Anônimo' }}</strong>
                            <div style="font-size: 12px; color: #555; margin-top: 2px;">
                                <i class="material-icons" style="font-size: 12px; vertical-align: middle;">phone</i> 
                                {{ $linha->usuario->telefone ?? 'Sem telefone' }}
                            </div>
                            <div style="font-size: 12px; color: #2e7d32; font-weight: 600; margin-top: 4px; background: #e8f5e9; padding: 2px 6px; border-radius: 4px; display: inline-block;">
                                <i class="material-icons" style="font-size: 14px; vertical-align: middle;">location_on</i> 
                                Retirada em: {{ $linha->localizacao ?? 'Não informada' }}
                            </div>
                        </td>
                        
                        <td>
                            <span style="font-size: 11px; background: #EAEAEA; padding: 3px 8px; border-radius: 4px; font-weight: 600; text-transform: uppercase;">
                                {{ $linha->categoria }}
                            </span>
                            <div style="font-size: 13px; color: #444; margin-top: 6px;">
                                {{ Str::limit($linha->descricao, 50, '...') }}
                            </div>
                        </td>
                        <td>
                            @if($linha->status == 'Analise')
                                <span class="badge" style="background-color: #FFF3E0; color: #E65100; font-weight: 700; border: 1px solid #FFE0B2;">
                                    Em Análise
                                </span>
                            @elseif($linha->status == 'Aprovada')
                                <span class="badge badge-user">Aprovada</span>
                            @elseif($linha->status == 'Retirada')
                                <span class="badge" style="background-color: #E0F7FA; color: #006064;">
                                    Retirada
                                </span>
                            @else
                                <span class="badge badge-inactive">Rejeitada</span>
                            @endif
                        </td>
                        <td>
                            <div class="action-buttons-flex">
                                @if($linha->status == 'Analise')
                                    <a href="{{ route('admin.doacoes.aprovar', $linha->id_doacao) }}" 
                                       class="btn-action" 
                                       style="background-color: #E8F5E9; color: #2E7D32;" 
                                       title="Aprovar Doação"
                                       onclick="return confirm('Deseja aprovar esta doação? Ela ficará aguardando retirada.');">
                                        <i class="material-icons">check</i>
                                    </a>
                                    
                                    <a href="{{ route('admin.doacoes.rejeitar', $linha->id_doacao) }}" 
                                       class="btn-action delete" 
                                       title="Rejeitar Doação"
                                       onclick="return confirm('Deseja rejeitar esta doação?');">
                                        <i class="material-icons">close</i>
                                    </a>
                                @endif

                                @if($linha->status == 'Aprovada')
                                    <button type="button" class="btn-action" 
                                            style="background-color: #E0F7FA; color: #006064; border: none; cursor: pointer;" 
                                            title="Confirmar Retirada e Virar Produto"
                                            onclick="abrirModalRetirada('{{ $linha->id_doacao }}', '{{ addslashes($linha->nome) }}')">
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
        <p id="modalTextoItem" style="font-size: 14px; color: #666;"></p>
        
        <form id="formRetirarPreco" action="" method="POST">
            @csrf
            <label for="preco_venda">Valor de Venda (R$):</label>
            <input type="number" name="preco" id="preco_venda" step="0.01" min="0" placeholder="0,00" required autofocus>
            
            <div class="modal-botoes">
                <button type="button" class="btn" style="background: #ccc; color: #333;" onclick="fecharModalPreco()">Cancelar</button>
                <button type="submit" class="btn" style="background: #006064; color: white;">Confirmar e Cadastrar</button>
            </div>
        </form>
    </div>
</div>

<script>
    function abrirModalRetirada(id, nome) {
        const form = document.getElementById('formRetirarPreco');
        form.action = `/admin/doacoes/retirar/${id}`;
        
        document.getElementById('modalTextoItem').innerText = "O item (" + nome + ") foi retirado. Insira o preço de venda para o estoque:";
        document.getElementById('modalPreco').style.display = 'flex';
    }

    function fecharModalPreco() {
        document.getElementById('modalPreco').style.display = 'none';
    }
</script>
@endsection