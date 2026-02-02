<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sessões de Atendimento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h2>Sessões de Atendimento</h2>
        <div class="d-flex justify-content-between align-items-end mb-4">
            
            <a href="{{ route('specialized-educational-support.sessions.create') }}" class="btn btn-primary">Nova Sessão</a>
            <a href="{{ route('specialized-educational-support.session-records.index') }}" class="btn btn-sm btn-outline-primary">Registros</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card shadow-sm">
            <div class="card-body">
                <table class="table table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Data</th>
                            <th>Aluno</th>
                            <th>Profissional</th>
                            <th>Tipo</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sessions as $session)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($session->session_date)->format('d/m/Y') }}</td>
                            <td>{{ $session->student->name }}</td>
                            <td>{{ $session->professional->name }}</td>
                            <td>{{ $session->type }}</td>
                            <td><span class="badge bg-info text-dark">{{ $session->status }}</span></td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('specialized-educational-support.sessions.show', $session) }}" class="btn btn-sm btn-outline-secondary">Ver</a>
                                    <a href="{{ route('specialized-educational-support.sessions.edit', $session) }}" class="btn btn-sm btn-outline-warning">Editar</a>
                                    @if($session->sessionRecord)
                                        <a href="{{ route('specialized-educational-support.session-records.show', $session->sessionRecord->id) }}" 
                                        class="btn btn-sm btn-outline-dark">
                                        <i class="bi bi-file-earmark-text"></i> Ver Registro
                                        </a>
                                    @else
                                        <a href="{{ route('specialized-educational-support.session-records.create', $session->id) }}" 
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-plus-circle"></i> Criar Registro
                                        </a>
                                    @endif
                                    <form action="{{ route('specialized-educational-support.sessions.destroy', $session) }}" method="POST" onsubmit="return confirm('Mover para lixeira?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Excluir</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>