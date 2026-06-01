<html>
    <head>
        <title>Brecharme - @yield('titulo')</title>

        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        
        <link rel="stylesheet" href="{{ asset('css/cabecalho.css') }}">
        
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    </head>

    <body>

        <nav class="brecharme-navbar">
            <div class="nav-wrapper">
                <a href="{{ route('produtos.vitrine') }}" class="brand-logo">Brecharme</a>
                
                <a href="#" data-target="mobile" class="sidenav-trigger">
                    <i class="material-icons">menu</i>
                </a>
                
                <ul class="right hide-on-med-and-down">
                    <li><a href="#">Sobre Nós</a></li>
                    <li><a href="{{ route('produtos.vitrine') }}">Vitrine</a></li>

                    @guest
                        <li><a href="{{ route('login') }}">Doe Já</a></li>
                        <li><a href="{{ route('login') }}"><i class="material-icons">account_circle</i></a></li>
                        <li><a href="{{ route('login') }}"><i class="material-icons">shopping_cart</i></a></li>
                    @endguest

                    @auth
                        @if(Auth::user()->admin)
                            <li><a href="{{ route('admin.gerenciar') }}" class="text-admin">Painel Admin</a></li>
                            <li><a href="{{ route('admin.gerenciar') }}"><i class="material-icons">admin_panel_settings</i></a></li>
                        @else
                            <li><a href="{{ route('produtos.novaDoacao') }}">Doe Já</a></li>
                            <li><a href="{{ route('perfil.meuPerfil') }}"><i class="material-icons">account_circle</i></a></li>
                            <li><a href="{{ route('carrinho') }}"><i class="material-icons">shopping_cart</i></a></li>
                        @endif
                        
                        <li><a href="{{ route('login.sair') }}">Sair</a></li>
                    @endauth
                </ul>
            </div>
        </nav>

        <ul class="sidenav" id="mobile">
            <li><a href="{{ route('produtos.vitrine') }}">Home</a></li>
            <li><a href="#">Sobre Nós</a></li>
            <li><a href="{{ route('produtos.vitrine') }}">Vitrine</a></li>
            
            @guest
                <li><a href="{{ route('login') }}">Doe Já</a></li>
                <li><a href="{{ route('login') }}">Login</a></li>
            @endguest

            @auth
                @if(Auth::user()->admin)
                    <li><a href="{{ route('admin.gerenciar') }}">Admin Panel</a></li>
                @else
                    <li><a href="{{ route('produtos.novaDoacao') }}">Doe Já</a></li>
                    <li><a href="{{ route('perfil.meuPerfil') }}">Perfil</a></li>
                    <li><a href="{{ route('carrinho') }}">Carrinho</a></li>
                @endif
                
                <li><a href="{{ route('login.sair') }}">Sair</a></li>
            @endauth
        </ul>

        <script src="{{ asset('js/cabecalho.js') }}"></script>
    </body>
</html>