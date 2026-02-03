<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes da Deficiência - {{ $student->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f4f7f6; }
        .card-header {
            background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
        }
        .info-label {
            color: #6c757d;
            font-size: 0.875rem;
            margin-bottom: 5px;
        }
        .info-value {
            font-weight: 500;
            font-size: 1.1rem;
            color: #212529;
        }
        .severity-badge {
            font-size: 0.85rem;
            padding: 5px 10px;
            border-radius: 20px;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <!-- Cabeçalho -->
        <div class="card shadow border-0 mb-4">
            <div class="card-header text-white d-flex justify-content-between align-items-center p-3">
                <div>
                    <h4 class="mb-0"><i class="bi bi-eye me-2"></i>Detalhes da Deficiência</h4>
                    <p class="mb-0 mt-2 opacity-75">{{ $student->name }} • Matrícula: {{ $student->enrollment ?? 'N/A' }}</p>
                </div>
                <a href="{{ route('specialized-educational-support.student-deficiencies.index', $student) }}" class="btn btn-light btn-sm">
                    <i class="bi bi-arrow-left"></i> Voltar
                </a>
            </div>
        </div>

        <!-- Detalhes da Deficiência -->
        <div class="card shadow border-0 mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0 text-dark"><i class="bi bi-heart-pulse me-2"></i>{{ $student_deficiency->deficiency->name }}</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="info-label">Deficiência</div>
                        <div class="info-value">
                            <strong>{{ $student_deficiency->deficiency->name }}</strong>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-4">
                        <div class="info-label">Severidade</div>
                        <div class="info-value">
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
                            @if($student_deficiency->severity)
                                <span class="badge bg-{{ $severityColors[$student_deficiency->severity] }} severity-badge">
                                    <i class="bi bi-thermometer-{{ $student_deficiency->severity == 'mild' ? 'low' : ($student_deficiency->severity == 'moderate' ? 'half' : 'high') }} me-1"></i>
                                    {{ $severityLabels[$student_deficiency->severity] }}
                                </span>
                            @else
                                <span class="badge bg-secondary severity-badge">Não informada</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-4">
                        <div class="info-label">Recursos de Apoio</div>
                        <div class="info-value">
                            @if($student_deficiency->uses_support_resources)
                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> Sim</span>
                            @else
                                <span class="badge bg-secondary"><i class="bi bi-x-circle me-1"></i> Não</span>
                            @endif
                        </div>
                    </div>
                </div>
                
                @if($student_deficiency->notes)
                    <div class="row">
                        <div class="col-md-12">
                            <div class="info-label">Observações</div>
                            <div class="info-value bg-light p-3 rounded border">
                                {{ $student_deficiency->notes }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            <div class="card-footer bg-light">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted">
                            <i class="bi bi-clock-history me-1"></i>
                            Criado em: {{ $student_deficiency->created_at->format('d/m/Y H:i') }} | 
                            Atualizado em: {{ $student_deficiency->updated_at->format('d/m/Y H:i') }}
                        </small>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('specialized-educational-support.student-deficiencies.edit', $student_deficiency) }}" class="btn btn-warning btn-sm">
                            <i class="bi bi-pencil me-1"></i> Editar
                        </a>
                        <a href="{{ route('specialized-educational-support.student-deficiencies.index', $student) }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-list me-1"></i> Ver Todas
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ações -->
        <div class="card shadow border-0 mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('specialized-educational-support.student-deficiencies.index', $student) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Voltar para Lista
                    </a>
                    <div class="d-flex gap-2">
                        <form action="{{ route('specialized-educational-support.student-deficiencies.destroy', $student_deficiency) }}" 
                              method="POST" 
                              onsubmit="return confirm('Tem certeza que deseja excluir permanentemente esta deficiência?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger">
                                <i class="bi bi-trash me-1"></i> Excluir Deficiência
                            </button>
                        </form>
                        <a href="{{ route('specialized-educational-support.student-deficiencies.edit', $student_deficiency) }}" class="btn btn-primary">
                            <i class="bi bi-pencil me-1"></i> Editar Deficiência
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>