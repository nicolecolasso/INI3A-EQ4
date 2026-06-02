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
                    <th>Doador</th>
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
                            <strong>{{ $linha->usuario->name ?? 'Doador Anônimo' }}</strong>
                            <div style="font-size: 12px; color: #888;">{{ $linha->localizacao }}</div>
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
                                       onclick="return confirm('Deseja aprovar esta doação?');">
                                        <i class="material-icons">check</i>
                                    </a>
                                    
                                    <a href="{{ route('admin.doacoes.rejeitar', $linha->id_doacao) }}" 
                                       class="btn-action delete" 
                                       title="Rejeitar Doação"
                                       onclick="return confirm('Deseja rejeitar esta doação?');">
                                        <i class="material-icons">close</i>
                                    </a>
                                @endif

                                <a href="{{ route('admin.doacoes.editarDoacao', $linha->id_doacao) }}" class="btn-action edit" title="Editar / Ver Detalhes">
                                    <i class="material-icons">edit</i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="empty-table">Nenhuma doação cadastrada no sistema.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection