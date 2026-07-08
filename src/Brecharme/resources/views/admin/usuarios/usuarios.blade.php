@extends('layout.site')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@endpush

@section('titulo', 'Usuários')

@section('conteudo')
<div class="admin-table-container">
    
    <header class="table-header-box">
        <h2>Gerenciamento de Usuários</h2>
        <a href="{{ route('admin.usuarios.novoUsuario') }}" class="btn-add-table">
            <i class="material-icons">person_add</i> Novo Usuário
        </a>
    </header>

    <div class="admin-filters-wrapper">
        <form action="{{ route('admin.usuarios.buscar') }}" method="GET" class="filters-form">
    
            <div class="filter-field">
                <label for="filter-nome">Nome</label>
                <div class="filter-input-icon">
                    <i class="material-icons">person</i>
                    <input type="text" name="nome" id="filter-nome" value="{{ request('nome') }}" placeholder="Buscar por nome...">
                </div>
            </div>

            <div class="filter-field">
                <label for="filter-email">E-mail</label>
                <div class="filter-input-icon">
                    <i class="material-icons">email</i>
                    <input type="text" name="email" id="filter-email" value="{{ request('email') }}" placeholder="Buscar por e-mail...">
                </div>
            </div>

            <div class="filter-field">
                <label for="filter-status">Status</label>
                <div class="filter-input-icon">
                    <i class="material-icons">toggle_on</i>
                    <select name="status" id="filter-status">
                        <option value="">Todos os usuários</option>
                        <option value="ativo" {{ request('status') === 'ativo' ? 'selected' : '' }}>Apenas Ativos</option>
                        <option value="inativo" {{ request('status') === 'inativo' ? 'selected' : '' }}>Apenas Inativos</option>
                    </select>
                </div>
            </div>

            <div class="filter-field">
                <label for="filter-admin">Tipo</label>
                <div class="filter-input-icon">
                    <i class="material-icons">admin_panel_settings</i>
                    <select name="admin" id="filter-admin">
                        <option value="">Todos os tipos</option>
                        <option value="sim" {{ request('admin') === 'sim' ? 'selected' : '' }}>Apenas Admins</option>
                        <option value="nao" {{ request('admin') === 'nao' ? 'selected' : '' }}>Apenas Clientes</option>
                    </select>
                </div>
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn-filter-submit" title="Aplicar filtros">
                    <i class="material-icons">search</i> Filtrar
                </button>
                @if(request()->filled('nome') || request()->filled('email') || request()->filled('status'))
                    <a href="{{ route('admin.usuarios') }}" class="btn-filter-clear" title="Limpar Filtros">
                        <i class="material-icons">clear</i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>Telefone</th>
                    <th>Tipo / Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($linhas as $linha)
                    <tr class="{{ $linha->excluido ? 'row-user-inactive' : '' }}">
                        <td data-label="ID">#{{ $linha->id }}</td>
                        <td data-label="Nome">
                            <strong>{{ $linha->name }}</strong>
                            @if($linha->excluido)
                                <br><span class="text-muted-bold">(Inativo / Bloqueado)</span>
                            @endif
                        </td>
                        <td data-label="E-mail">{{ $linha->email }}</td>
                        <td data-label="Telefone">{{ $linha->telefone ?? 'Não informado' }}</td>
                        <td data-label="Tipo / Status">
                            @if($linha->excluido)
                                <span class="badge-user-arquivado">Inativo</span>
                            @elseif($linha->admin)
                                <span class="badge-user-admin">Admin</span>
                            @else
                                <span class="badge-user-cliente">Cliente</span>
                            @endif
                        </td>
                        <td data-label="Ações">
                            <div class="action-buttons-flex">
                                @if(!$linha->excluido)
                                    {{-- Usuário Ativo: Exibe Editar e Desativar --}}
                                    <a href="{{ route('admin.usuarios.editarUsuario', $linha->id) }}" class="btn-action edit" title="Editar Usuário">
                                        <i class="material-icons">edit</i>
                                    </a>
                                    
                                    <a href="{{ route('admin.usuarios.excluir', $linha->id) }}" 
                                       class="btn-action delete" 
                                       onclick="return confirm('Tem certeza que deseja desativar este usuário? Todos os carrinhos ativos e reservas dele serão cancelados automaticamente.');" 
                                       title="Desativar Usuário">
                                        <i class="material-icons">block</i>
                                    </a>
                                @else
                                    {{-- Usuário Inativo: Esconde Editar e Exibe Reintegrar/Ativar --}}
                                    <a href="{{ route('admin.usuarios.ativar', $linha->id) }}" 
                                       class="btn-action check" 
                                       onclick="return confirm('Deseja reintegrar este usuário ao sistema? Ele voltará a ter permissão de acesso.');" 
                                       title="Reintegrar Usuário">
                                        <i class="material-icons">settings_backup_restore</i>
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="empty-table">Nenhum usuário cadastrado no sistema.</td>
                    </tr>
                @endforelse 
            </tbody>
        </table>
    </div>
</div>
@endsection