<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Página não encontrada | AEE</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/css/pages/errors.css'])
    <style>
        :root { --primary-purple: #4D44B5; --text-dark: #303972; --text-gray: #5a5c6f; }
        body { margin: 0; padding: 0; background-color: #f8f9fa; }
    </style>
</head>
<body>
    <div class="error-page-container">
        <div class="icon-wrapper">
            <svg viewBox="0 0 200 200" width="180" height="180">
                <path d="M140,80 C155,80 155,60 140,60 L140,40 Q140,30 130,30 L110,30 C110,15 90,15 90,30 L70,30 Q60,30 60,40 L60,60 C45,60 45,80 60,80 L60,100 Q60,110 70,110 L130,110 Q140,110 140,100 Z" 
                      fill="none" stroke="#4D44B5" stroke-width="5" />
                <circle cx="100" cy="70" r="15" fill="#8a7fd4" opacity="0.3" />
                <text x="88" y="82" font-family="Arial" font-size="30" fill="#4D44B5" font-weight="bold">?</text>
            </svg>
        </div>

        <h1 class="error-code">404</h1>
        <h2 class="error-title">Página não encontrada</h2>
        <p class="error-description">
            Parece que esta peça do sistema sumiu. <br>
            O link que você acessou pode estar quebrado ou a página foi movida.
        </p>

        <a href="{{ route('dashboard') }}" class="btn-back">
            Voltar para o Dashboard
        </a>
    </div>
</body>
</html>