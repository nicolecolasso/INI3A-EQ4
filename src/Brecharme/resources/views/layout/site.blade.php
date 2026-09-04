<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Brecharme - @yield('titulo')</title>
    <link rel="icon" type="image/png" href="{{ asset('img/favicon.png') }}">

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout.css') }}">
    
    @stack('estilos')
</head>
<body>

    @include('layout._cabecalho')

    <main>
        @yield('conteudo')
    </main>

    @include('layout._rodape')
    <script src="{{ asset('js/script.js') }}"></script>
    @stack('scripts')

</body>
</html>