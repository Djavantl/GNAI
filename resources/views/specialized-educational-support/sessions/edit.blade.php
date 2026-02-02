<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Sessão</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-5">
    <div class="container" style="max-width: 800px;">
        <div class="card shadow border-warning">
            <div class="card-header bg-warning">
                <h4 class="mb-0">Editar Sessão #{{ $session->id }}</h4>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="card-body">
                <form action="{{ route('specialized-educational-support.sessions.update', $session) }}" method="POST">
                    @csrf @method('PUT')
                    
                    <div class="row g-3">
                        <div class="col-md-12 text-muted mb-2">
                            Atenção: Você está editando os dados da sessão.
                        </div>

                        <div class="col-md-4">
                            <input type="hidden" name="student_id" class="form-control" value="{{ $session->student_id }}">
                        </div>

                        <div class="col-md-4">
                            <input type="hidden" name="professional_id" class="form-control" value="{{ $session->professional_id }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Data</label>
                            <input type="date" name="session_date" class="form-control" value="{{ $session->session_date }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Início</label>
                            <input type="time" name="start_time" class="form-control" value="{{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Fim</label>
                            <input type="time" name="end_time" class="form-control" value="{{ $session->end_time ? \Carbon\Carbon::parse($session->end_time)->format('H:i') : '' }}">
                        </div>

                        <div class="col-12">
                            <label class="form-label">Objetivo</label>
                            <textarea name="session_objective" class="form-control" rows="3">{{ $session->session_objective }}</textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="Agendado" {{ $session->status == 'Agendado' ? 'selected' : '' }}>Agendado</option>
                                <option value="Realizado" {{ $session->status == 'Realizado' ? 'selected' : '' }}>Realizado</option>
                                <option value="Cancelado" {{ $session->status == 'Cancelado' ? 'selected' : '' }}>Cancelado</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Tipo de Atendimento</label>
                        <input type="text" name="type" class="form-control" value="{{ $session->type }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Local</label>
                        <input type="text" name="location" class="form-control" value="{{ $session->location }}">
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">Atualizar</button>
                        <a href="{{ route('specialized-educational-support.sessions.index') }}" class="btn btn-secondary">Voltar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>