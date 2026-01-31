<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deficiências do Aluno - {{ $student->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f4f7f6; }
        .header-bg {
            background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
        }
        .deficiency-card {
            border-left: 5px solid #0d6efd;
            border-radius: 8px;
        }
        .severity-badge {
            font-size: 0.8rem;
            padding: 5px 10px;
            border-radius: 20px;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="card shadow border-0 mb-4">
            <div class="card-header header-bg text-white d-flex justify-content-between align-items-center p-3">
                <div>
                    <h4 class="mb-0"><i class="bi bi-person-badge me-2"></i>Deficiências do Aluno</h4>
                    <p class="mb-0 mt-2 opacity-75">{{ $student->name }} • Matrícula: {{ $student->enrollment ?? 'N/A' }}</p>
                </div>
                <a href="{{ route('specialized-educational-support.students.index') }}" class="btn btn-light btn-sm">
                    <i class="bi bi-arrow-left"></i> Voltar
                </a>
            </div>
            <div class="card-body p-4">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0">Lista de Deficiências</h5>
                    <a href="{{ route('specialized-educational-support.student-deficiencies.create', $student) }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-circle"></i> Nova Deficiência
                    </a>
                </div>

                @if($deficiencies->isEmpty())
                    <div class="text-center py-5">
                        <i class="bi bi-clipboard-x" style="font-size: 3rem; color: #dee2e6;"></i>
                        <h5 class="text-secondary mt-3">Nenhuma deficiência cadastrada</h5>
                        <p class="text-muted">Este aluno ainda não possui deficiências cadastradas.</p>
                        <a href="{{ route('specialized-educational-support.student-deficiencies.create', $student) }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Cadastrar Deficiência
                        </a>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Deficiência</th>
                                    <th>Severidade</th>
                                    <th>Recursos de Apoio</th>
                                    <th>Observações</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($deficiencies as $deficiency)
                                <tr>
                                    <td>{{ $deficiency->deficiency->name }}</td>
                                    <td>
                                        @php
                                            $severityLabels = [
                                                'mild' => 'Leve',
                                                'moderate' => 'Moderada',
                                                'severe' => 'Severa'
                                            ];
                                            $severityColors = [
                                                'mild' => 'success',
                                                'moderate' => 'warning',
                                                'severe' => 'danger'
                                            ];
                                        @endphp
                                        @if($deficiency->severity)
                                            <span class="badge bg-{{ $severityColors[$deficiency->severity] }} severity-badge">
                                                {{ $severityLabels[$deficiency->severity] }}
                                            </span>
                                        @else
                                            <span class="text-muted">Não informada</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($deficiency->uses_support_resources)
                                            <span class="badge bg-success">Sim</span>
                                        @else
                                            <span class="badge bg-secondary">Não</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($deficiency->notes)
                                            <span class="d-inline-block text-truncate" style="max-width: 200px;" title="{{ $deficiency->notes }}">
                                                {{ $deficiency->notes }}
                                            </span>
                                        @else
                                            <span class="text-muted">Sem observações</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('specialized-educational-support.student-deficiencies.edit', $deficiency) }}" class="btn btn-sm btn-outline-warning">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('specialized-educational-support.student-deficiencies.destroy', $deficiency) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja remover esta deficiência?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>