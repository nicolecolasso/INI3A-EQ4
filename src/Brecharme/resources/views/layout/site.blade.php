<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Brecharme - @yield('titulo')</title>

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    
    <link rel="stylesheet" href="{{ asset('css/layout.css') }}">
    
    @stack('estilos')
</head>
<body>

    @include('layout._cabecalho')

    <main>
        @yield('conteudo')
    </main>

    @include('layout._rodape')

</body>
</html>