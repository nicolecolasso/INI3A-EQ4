@extends('layout.site')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@endpush

@section('titulo', 'Gerenciar Banners')

@section('conteudo')
<div class="admin-container">
    
    <div class="admin-header-title">
        <h1>Gerenciar Carrossel</h1>
        <p>Suba as 3 imagens oficiais que vão rodar na página inicial da loja.</p>
    </div>

    @if(session('sucesso'))
        <div class="alert-success-custom">
            {{ session('sucesso') }}
        </div>
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

    <div class="banners-admin-grid">
        @for($i = 1; $i <= 3; $i++)
            @php $currentBanner = $banners->get($i); @endphp
            
            <div class="banner-admin-card">
                <div class="banner-badge">Posição {{ $i }}</div>
                
                <div class="banner-preview-box">
                    @if($currentBanner)
                        <img src="{{ asset($currentBanner->caminho_img) }}" alt="Banner {{ $i }}">
                    @else
                        <div class="banner-vazio-placeholder">
                            <i class="material-icons">cloud_upload</i>
                            <span>Sem imagem ativa</span>
                        </div>
                    @endif
                </div>

                <div class="banner-card-form-wrapper">
                    <form action="{{ route('admin.banners.update', $i) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <label class="btn-upload-customizado">
                            <i class="material-icons">add_a_photo</i>
                            <span>{{ $currentBanner ? 'Substituir Imagem' : 'Escolher Imagem' }}</span>
                            {{-- Nome alterado para caminho_img correspondendo ao validate --}}
                            <input type="file" name="caminho_img" accept="image/*" required onchange="this.form.submit()">
                        </label>
                    </form>
                </div>
            </div>
        @endfor
    </div>
</div>
@endsection