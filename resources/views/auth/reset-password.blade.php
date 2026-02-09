<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nova Senha - GNAI</title>

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
                    <i class="fas fa-lock"></i>
                </div>
                <h1 class="welcome-title">Definir nova senha</h1>
                <p class="brand-tagline">
                    Escolha uma senha segura para proteger sua conta no GNAI.
                </p>
            </div>
        </div>

        <div class="login-form-side">
            <div class="form-content">

                <h2 class="form-title mb-3">Redefinir senha</h2>

                <form method="POST" action="{{ route('password.update') }}">
                    @csrf

                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="mb-3">
                        <label class="form-label fw-bold">E-mail</label>
                        <input type="email"
                               name="email"
                               value="{{ $email }}"
                               class="form-control custom-input"
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Nova senha</label>
                        <input type="password"
                               name="password"
                               class="form-control custom-input"
                               required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Confirmar senha</label>
                        <input type="password"
                               name="password_confirmation"
                               class="form-control custom-input"
                               required>
                    </div>

                    <button class="btn btn-login-primary w-100">
                        Alterar senha <i class="fas fa-check ms-2"></i>
                    </button>

                </form>
            </div>
        </div>
    </div>
</div>

</body>
</html>
