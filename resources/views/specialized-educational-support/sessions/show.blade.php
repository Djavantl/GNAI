<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Detalhes da Sessão</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-5">
    <div class="container" style="max-width: 700px;">
        <div class="card shadow">
            <div class="card-header bg-dark text-white d-flex justify-content-between">
                <h4 class="mb-0">Detalhes da Sessão</h4>
                <span class="badge bg-primary">{{ $session->status }}</span>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-sm-4 fw-bold">Aluno:</div>
                    <div class="col-sm-8">{{ $session->student->name }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 fw-bold">Profissional:</div>
                    <div class="col-sm-8">{{ $session->professional->name }}</div>
                </div>
                <hr>
                <div class="row mb-3">
                    <div class="col-sm-4 fw-bold">Data:</div>
                    <div class="col-sm-8">{{ \Carbon\Carbon::parse($session->session_date)->format('d/m/Y') }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 fw-bold">Horário:</div>
                    <div class="col-sm-8">
                        {{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }} às 
                        {{ $session->end_time ? \Carbon\Carbon::parse($session->end_time)->format('H:i') : '--:--' }}
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 fw-bold">Local:</div>
                    <div class="col-sm-8">{{ $session->location }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 fw-bold">Tipo:</div>
                    <div class="col-sm-8">{{ $session->type }}</div>
                </div>
                <hr>
                <div class="row mb-3">
                    <div class="col-12 fw-bold mb-2">Objetivo:</div>
                    <div class="col-12 p-3 bg-light border rounded">{{ $session->session_objective }}</div>
                </div>

                <div class="mt-4">
                    <a href="{{ route('specialized-educational-support.sessions.edit', $session) }}" class="btn btn-warning">Editar</a>
                    <a href="{{ route('specialized-educational-support.sessions.index') }}" class="btn btn-secondary">Voltar para Lista</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>