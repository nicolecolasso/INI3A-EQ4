<header class="brecharme-navbar">
    
    <a href="{{ route('institucional.index') }}" class="brand-logo-container">
        <img src="{{ asset('img/logo.png') }}" alt="Brecharme" class="logo-img">
        <span class="brand-logo-text">Brecharme</span>
    </a>
    
    <ul class="navbar-links">
        <li><a href="{{ route('institucional.quemSomos') }}">Quem Somos</a></li>
        <li><a href="{{ route('produtos.vitrine') }}">Vitrine</a></li>
        <li><a href="{{ route('produtos.novaDoacao') }}">Doe Já</a></li>

        @guest
            <li><a href="{{ route('login') }}">Doe Já</a></li>
            <li>
                <a href="{{ route('login') }}" class="icon-circle" title="Entrar">
                    <i class="material-icons">account_circle</i>
                </a>
            </li>
            <li>
                <a href="{{ route('login') }}" class="icon-circle" title="Carrinho">
                    <i class="material-icons">shopping_cart</i>
                </a>
            </li>
        @endguest

        @auth
            @if(Auth::user()->admin)
                <li><a href="{{ route('admin.gerenciar') }}" class="text-admin">Painel Admin</a></li>
                <li>
                    <a href="{{ route('perfil.meuPerfil') }}" class="icon-circle" title="Configurações Admin">
                        <i class="material-icons">admin_panel_settings</i>
                    </a>
                </li>
            @else
                <li>
                    <a href="{{ route('perfil.meuPerfil') }}" class="icon-circle" title="Meu Perfil">
                        <i class="material-icons">account_circle</i>
                    </a>
                </li>
                
            @endif
            <li>
                <a href="{{ route('carrinho') }}" class="icon-circle" title="Carrinho">
                    <i class="material-icons">shopping_cart</i>
                </a>
            </li>
            <li><a href="{{ route('login.sair') }}" class="btn-sair">Sair</a></li>
        @endauth
    </ul>

    <button class="sidenav-trigger" onclick="toggleMenu()">
        <i class="material-icons">menu</i>
    </button>
</header>

<div class="sidenav-overlay" id="overlay" onclick="toggleMenu()"></div>
<ul class="sidenav" id="mobile-menu">
    <li><a href="{{ route('institucional.index') }}">Home</a></li>
    <li><a href="{{ route('institucional.quemSomos') }}">Quem Somos</a></li>
    <li><a href="{{ route('produtos.vitrine') }}">Vitrine</a></li>
    <li><a href="{{ route('produtos.novaDoacao') }}">Doe Já</a></li>
    
    @guest
        <li><a href="{{ route('login') }}">Doe Já</a></li>
        <li><a href="{{ route('login') }}">Login</a></li>
    @endguest

    @auth
        @if(Auth::user()->admin)
            <li><a href="{{ route('admin.gerenciar') }}">Admin Panel</a></li>
        @else
            <li><a href="{{ route('perfil.meuPerfil') }}">Perfil</a></li>
        @endif
        <li><a href="{{ route('carrinho') }}">Carrinho</a></li>
        <li><a href="{{ route('login.sair') }}">Sair</a></li>
    @endauth
</ul>

<script>
    // Função em JS puro para abrir e fechar o menu mobile
    function toggleMenu() {
        const menu = document.getElementById('mobile-menu');
        const overlay = document.getElementById('overlay');
        menu.classList.toggle('active');
        overlay.classList.toggle('active');
    }
</script>