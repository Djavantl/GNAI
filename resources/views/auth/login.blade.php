<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - GNAI</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    @vite(['resources/css/app.css'])
</head>
<body>

<div class="login-wrapper">
    <div class="login-split-card">
        <div class="login-brand-side">
            <div class="brand-content">
                <div class="brand-icon mb-4">
                    <i class="fas fa-layer-group"></i>
                </div>
                <h1 class="welcome-title">Bem-vindo(a) ao GNAI</h1>
                <p class="brand-tagline">
                    A plataforma definitiva para a <strong>gestão estratégica dos NAIs</strong>.
                    Unindo tecnologia e inclusão para transformar o suporte educacional.
                </p>
                <div class="brand-decoration">
                    <span class="dot"></span>
                    <span class="dot"></span>
                    <span class="dot"></span>
                </div>
            </div>
        </div>

        <div class="login-form-side">
            <div class="form-content">
                <div class="mb-4">
                    <h2 class="form-title">Acesse sua conta</h2>
                    <p class="text-muted small">Insira suas credenciais para continuar.</p>
                </div>

                @if(session('error'))
                    <div class="alert alert-danger border-0 small mb-4">
                        <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-bold">E-mail</label>
                        <input type="email" name="email" class="form-control custom-input" placeholder="exemplo@ifbaiano.edu.br" required autofocus>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-bold">Senha</label>
                        <input type="password" name="password" class="form-control custom-input" placeholder="••••••••" required>
                    </div>

                    <div class="text-end mb-4">
                        <a href="{{ route('password.request') }}" class="forgot-password-link">
                            Esqueceu sua senha?
                        </a>
                    </div>

                    <button type="submit" class="btn btn-login-primary w-100">
                        Entrar no Sistema <i class="fas fa-arrow-right ms-2"></i>
                    </button>
                </form>

                <div class="mt-5 text-center">
                    <p class="footer-copy">GNAI &copy; {{ date('Y') }} | Gestão NAIs</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
