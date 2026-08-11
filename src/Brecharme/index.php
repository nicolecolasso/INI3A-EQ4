<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brecharme - Em Breve</title>

    <!-- Fonte Google: Montserrat -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* ==============================================
           IDENTIDADE VISUAL OFICIAL DO BRECHARME
           ============================================== */
        @font-face {
            font-family: 'The Season';
            src: url('../fonts/The Seasons.ttf') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        :root {
            --amarelo-brecho: #EFB810;
            --branco-fundo:    #F0F0F0;
            --cinza-detalhe:   #B3B3B3;
            --preto-puro:      #000000;
            --fonte-titulo:    'The Season', serif;
            --fonte-texto:     'Montserrat', sans-serif;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box !important;
        }

        body {
            background-color: var(--branco-fundo);
            color: var(--preto-puro);
            font-family: var(--fonte-texto);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        /* Efeito visual suave de fundo com a cor amarela da marca */
        body::before {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, var(--amarelo-brecho) 0%, transparent 70%);
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.15;
            z-index: 0;
        }

        .container {
            position: relative;
            z-index: 1;
            max-width: 580px;
            width: 100%;
            background: #ffffff;
            padding: 45px 35px;
            border-radius: 20px;
            border: 2px solid var(--cinza-detalhe);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.05);
        }

        /* Logo do Brecharme */
        .logo-box {
            margin-bottom: 20px;
        }

        .logo-box img {
            max-width: 160px;
            height: auto;
            transition: transform 0.3s ease;
        }

        .logo-box img:hover {
            transform: scale(1.03);
        }

        /* Tag Em Breve */
        .badge {
            display: inline-block;
            padding: 6px 20px;
            background-color: var(--amarelo-brecho);
            border-radius: 50px;
            font-size: 0.8rem;
            color: #ffffff;
            font-weight: 600;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-bottom: 20px;
        }

        /* Título Principal com a fonte 'The Season' */
        h1 {
            font-family: var(--fonte-titulo), serif;
            font-size: 3rem;
            font-weight: normal;
            color: var(--preto-puro);
            line-height: 1.1;
            margin-bottom: 15px;
        }

        p.descricao {
            font-size: 0.95rem;
            color: #555555;
            margin-bottom: 30px;
            line-height: 1.6;
            font-weight: 400;
        }

        /* Botão do Instagram estilo Padrão do Brecharme */
        .social-box {
            display: flex;
            justify-content: center;
        }

        .btn-contato {
            display: inline-block;
            padding: 12px 28px;
            background-color: var(--amarelo-brecho);
            color: #ffffff;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            border-radius: 12px;
            border: 1.5px solid var(--amarelo-brecho);
            transition: all 0.3s ease;
        }

        .btn-contato:hover {
            background-color: var(--branco-fundo);
            color: var(--amarelo-brecho);
            border-color: var(--amarelo-brecho);
        }

        footer {
            margin-top: 25px;
            font-size: 0.75rem;
            color: var(--cinza-detalhe);
        }
    </style>
</head>
<body>

    <div class="container">
        <!-- Logo do Projeto -->
        <div class="logo-box">
            <img src="{{ asset('img/logo.png') }}" alt="Logo Brecharme" onerror="this.style.display='none'">
        </div>

        <span class="badge">Em Breve</span>

        <h1>Brecharme</h1>

        <p class="descricao">
            Estamos ajustando os últimos detalhes do nosso TCC para oferecer a melhor experiência para você. Voltaremos logo!
        </p>

        <div class="social-box">
            <a href="https://www.instagram.com/brecharme.tcc/" target="_blank" class="btn-contato">
                Acompanhe no Instagram
            </a>
        </div>

    </div>

</body>
</html>