<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Acesso Negado | AEE</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/css/pages/403.css'])
    <style>
        :root { --primary-purple: #4D44B5; --text-dark: #303972; --text-gray: #5a5c6f; }
        body { margin: 0; padding: 0; background-color: #f8f9fa; }
    </style>
</head>
<body>
    <div class="error-page-container">

        <div class="icon-wrapper">
            <svg viewBox="0 0 200 200" width="180" height="180">
                <rect x="60" y="80" width="80" height="60" rx="10" fill="none" stroke="#4D44B5" stroke-width="5" />
                <path d="M75,80 L75,60 Q75,30 100,30 Q125,30 125,60 L125,80" fill="none" stroke="#4D44B5" stroke-width="5" />
                <circle cx="100" cy="110" r="8" fill="#4D44B5" />
                <line x1="100" y1="118" x2="100" y2="128" stroke="#4D44B5" stroke-width="4" stroke-linecap="round" />
            </svg>
        </div>

        <h1 class="error-code">403</h1>
        <h2 class="error-title">Acesso Restrito</h2>
        <p class="error-description">
            Você não tem as permissões necessárias para acessar este recurso.<br>
            Consulte o administrador caso acredite que isso é um erro.
        </p>

        <a href="{{ route('dashboard') }}" class="btn-back">
            Voltar para o Dashboard
        </a>
    </div>
</body>
</html>