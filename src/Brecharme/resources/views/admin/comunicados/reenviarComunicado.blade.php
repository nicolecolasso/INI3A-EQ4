@extends('layout.site')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/form.css') }}">
@endpush

@section('titulo', 'Reenviar Comunicado')

@section('conteudo')
<div class="form-container-admin">
    <h3 class="form-titulo">Reutilizar ou Reeditar Comunicado Antigo</h3>

    <div class="historico-dropdown-container">
        <label for="comunicado_historico" class="historico-label">Escolha um comunicado já enviado:</label>
        <select id="comunicado_historico" class="historico-select">
            <option value="">Selecione um comunicado</option>
            @foreach($comunicadosAntigos as $antigo)
                <option value="{{ $antigo->id_comunicado }}" 
                        data-assunto="{{ $antigo->assunto }}" 
                        data-mensagem="{{ $antigo->mensagem }}">
                    [{{ \Carbon\Carbon::parse($antigo->data_envio)->format('d/m/Y') }}] - {{ Str::limit($antigo->assunto, 60) }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="row-form">
        <form action="{{ route('admin.comunicados.salvar') }}" method="post">
            {{ csrf_field() }}
            
            @include('admin.comunicados._form')
            
            <div class="form-actions section-spacing">
                <button class="btn-submit dark-style">Disparar Mensagem Editada</button>
                <a href="{{ route('admin.gerenciar') }}" class="btn-cancel">Cancelar</a>
            </div>
        </form>
    </div>
</div>

@endsection