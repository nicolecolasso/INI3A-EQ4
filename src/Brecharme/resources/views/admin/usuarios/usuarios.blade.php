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
                        <td>#{{ $linha->id }}</td>
                        <td><strong>{{ $linha->name }}</strong></td>
                        <td>{{ $linha->email }}</td>
                        <td>{{ $linha->telefone ?? 'Não informado' }}</td>
                        <td>
                            @if($linha->excluido)
                                <span class="badge badge-inactive">Inativo</span>
                            @else
                                <span class="badge {{ $linha->admin ? 'badge-admin' : 'badge-user' }}">
                                    {{ $linha->admin ? 'Admin' : 'Cliente' }}
                                </span>
                            @endif
                        </td>
                        <td>
                            <div class="action-buttons-flex">
                                <a href="{{ route('admin.usuarios.editarUsuario', $linha->id) }}" class="btn-action edit" title="Editar">
                                    <i class="material-icons">edit</i>
                                </a>
                                
                                @if(!$linha->excluido)
                                    <a href="{{ route('admin.usuarios.excluir', $linha->id) }}" 
                                       class="btn-action delete" 
                                       onclick="return confirm('Tem certeza que deseja desativar este usuário?');" 
                                       title="Desativar">
                                        <i class="material-icons">block</i>
                                    </a>
                                @else
                                    <button class="btn-action delete disabled" title="Já desativado" disabled style="opacity: 0.4; cursor: not-allowed;">
                                        <i class="material-icons">clear</i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="empty-table">Nenhum usuário cadastrado no sistema.</td>
                    </tr>
                @endforelse </tbody>
        </table>
    </div>
</div>
@endsection