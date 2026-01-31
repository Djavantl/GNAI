<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - NAPNE</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-primary">
    <div class="container-fluid">
        <span class="navbar-brand">NAPNE</span>

        <form action="{{ route('auth.logout') }}" method="POST">
            @csrf
            <button class="btn btn-outline-light btn-sm">
                Sair
            </button>
        </form>
    </div>
</nav>

<div class="container mt-4">

    <div class="alert alert-success">
        Bem-vindo, <strong>{{ auth()->user()->email }}</strong>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h5>Profissionais</h5>
                    <p>Gerenciar profissionais do NAPNE</p>
                    <a href="#" class="btn btn-primary btn-sm">Acessar</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h5>Alunos</h5>
                    <p>Gerenciar alunos</p>
                    <a href="#" class="btn btn-primary btn-sm">Acessar</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h5>Relatórios</h5>
                    <p>Visualizar relatórios</p>
                    <a href="#" class="btn btn-primary btn-sm">Acessar</a>
                </div>
            </div>
        </div>
    </div>

</div>

</body>
</html>
