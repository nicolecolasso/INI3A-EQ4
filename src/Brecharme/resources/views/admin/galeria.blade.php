@extends('layout.site')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@endpush

@section('titulo', 'Gerenciar Bazar e Galeria')

@section('conteudo')
<div class="admin-bazar-container">
    <div class="admin-header">
        <h1>Painel de Fotos do Bazar & Eventos</h1>
        <p>Gerencie os posts em destaque do Instagram e as fotos da galeria local.</p>
    </div>

    @if(session('sucesso'))
        <div class="alert-sucesso">{{ session('sucesso') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert-danger" style="background-color: #f8d7da; color: #721c24; padding: 12px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- INSTAGRAM --}}
    <section class="admin-secao-box">
        <h2><i class="material-icons">star</i> Adicionar Post do Instagram</h2>
        <p class="instrucao">Copie a URL do post (ex: https://www.instagram.com/p/xxxxxx/) e cole abaixo.</p>
        
        <form action="{{ route('admin.galeria.salvarInsta') }}" method="POST" class="form-alinhado">
            @csrf
            <div class="input-grupo">
                <input type="url" name="link_post" placeholder="Cole o link do post aqui..." required>
                <button type="submit" class="btn-pilula-preta">Vincular Post</button>
            </div>
        </form>

        <h3 class="subtitulo-lista">Posts Vinculados Atualmente</h3>
        <div class="lista-itens-admin">
            @forelse($postsInstagram as $post)
                <div class="item-linha">
                    <span class="link-truncado">{{ $post->link_post }}</span>
                    <form action="{{ route('admin.galeria.excluirInsta', $post->id_destaque) }}" method="POST" onsubmit="return confirm('Deseja remover este destaque do Instagram?');">                        @csrf 
                        <button type="submit" class="btn-deletar-icone"><i class="material-icons">delete</i></button>
                    </form>
                </div>
            @empty
                <p class="sem-dados">Nenhum post vinculado ainda.</p>
            @endforelse
        </div>
    </section>

    {{-- GALERIA LOCAL --}}
    <section class="admin-secao-box">
        <h2><i class="material-icons">collections</i> Enviar Foto para a Galeria</h2>
        <form action="{{ route('admin.galeria.salvarFoto') }}" method="POST" enctype="multipart/form-data" class="form-vertical" >
            @csrf
            <div class="form-campo">
                <label for="titulo_evento">Título do Evento (Opcional)</label>
                <input type="text" id="titulo_evento" name="titulo_evento" placeholder="Ex: Bazar de Inverno 2026">
            </div>
            
            <div class="form-campo upload-area">
                <div class="upload-preview" style="margin-top: 15px; max-height: 200px; width: 100%; overflow: hidden; border-radius: 8px;"></div>

                <label class="btn-upload-local">
                    <input type="file" name="caminho_img" accept="image/*" required onchange="document.getElementById('nome-arquivo-selecionado').textContent = this.files[0].name">
                    <i class="material-icons">cloud_upload</i>
                    <span id="nome-arquivo-selecionado">Selecionar Imagem do Evento</span>                    
                </label>
            </div>
            
            <button type="submit" class="btn-pilula-preta btn-bloco">Salvar na Galeria</button>
        </form>

        <h3 class="subtitulo-lista">Fotos na Galeria</h3>
        <div class="galeria-admin-grid">
            @forelse($fotosGaleria as $foto)
                <div class="galeria-admin-card">
                    <img src="{{ asset($foto->caminho_img) }}" alt="Preview">
                    <div class="galeria-card-rodape">
                        <span>{{ $foto->titulo_evento ?? 'Sem título' }}</span>
                        <form action="{{ route('admin.galeria.excluirFoto', $foto->id_galeria) }}" method="POST" onsubmit="return confirm('Deseja realmente excluir esta foto da galeria?');">
                            @csrf
                            <button type="submit" class="btn-deletar-card"><i class="material-icons">delete</i></button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="sem-dados">Nenhuma foto adicionada.</p>
            @endforelse
        </div>
    </section>
</div>
@endsection