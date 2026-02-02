<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contextos do Aluno - {{ $student->person->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f4f7f6; }
        .card-header {
            background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
        }
        .badge-evaluation {
            font-size: 0.8rem;
            padding: 4px 10px;
            border-radius: 12px;
        }
        .current-context {
            border-left: 5px solid #198754 !important;
            background-color: #f8fff9 !important;
        }
        .table-hover tbody tr:hover {
            background-color: rgba(13, 110, 253, 0.05);
        }
        .action-buttons .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        .empty-state { 
            padding: 60px 20px; 
            text-align: center; 
            color: #6c757d;
        }
        .empty-state-icon { 
            font-size: 3.5rem; 
            color: #dee2e6; 
            margin-bottom: 15px; 
        }
        .student-info-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            border: 1px solid #e9ecef;
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <!-- Cabeçalho -->
        <div class="card shadow border-0 mb-4">
            <div class="card-header text-white d-flex justify-content-between align-items-center p-3">
                <div>
                    <h4 class="mb-0"><i class="bi bi-clock-history me-2"></i>Histórico de Contextos</h4>
                    <p class="mb-0 mt-2 opacity-75">{{ $student->person->name}} • Matrícula: {{ $student->registration ?? 'N/A' }}</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('specialized-educational-support.student-context.create', $student->id) }}" 
                       class="btn btn-light btn-sm fw-bold">
                        <i class="bi bi-plus-circle"></i> Novo Contexto
                    </a>
                </div>
            </div>
        </div>

        <!-- Informações do Aluno -->
        <div class="student-info-card mb-4">
            <div class="row">
                <div class="col-md-8">
                    <h5 class="mb-3"><i class="bi bi-person-badge me-2"></i>Informações do Aluno</h5>
                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <small class="text-muted">Nome:</small>
                            <div class="fw-bold">{{ $student->person->name }}</div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <small class="text-muted">Matrícula:</small>
                            <div class="fw-bold">{{ $student->registration ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <small class="text-muted">Status:</small>
                            <div>
                                @if($student->status === 'active')
                                    <span class="badge bg-success">Ativo</span>
                                @elseif($student->status === 'inactive')
                                    <span class="badge bg-secondary">Inativo</span>
                                @else
                                    <span class="badge bg-warning">Transferido</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-md-end">
                    @if($contexts->where('is_current', true)->first())
                        <div class="alert alert-success mb-0">
                            <i class="bi bi-check-circle me-2"></i>
                            <strong>Contexto Atual Disponível</strong>
                            <div class="small">Última atualização: 
                                {{ $contexts->where('is_current', true)->first()->updated_at->format('d/m/Y') }}
                            </div>
                        </div>
                    @else
                        <div class="alert alert-warning mb-0">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Nenhum Contexto Atual</strong>
                            <div class="small">Defina um contexto como atual</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Lista de Contextos -->
        <div class="card shadow border-0">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center p-3">
                <h5 class="mb-0 text-dark"><i class="bi bi-list-check me-2"></i>Contextos Registrados</h5>
                <span class="badge bg-primary">{{ $contexts->count() }} registro(s)</span>
            </div>
            
            @if($contexts->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="50">#</th>
                                <th width="120">Data</th>
                                <th>Tipo de Avaliação</th>
                                <th width="150">Nível Aprendizagem</th>
                                <th width="150">Nível Autonomia</th>
                                <th width="120">Status</th>
                                <th width="180" class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($contexts as $context)
                                <tr class="{{ $context->is_current ? 'current-context' : '' }}">
                                    <td class="fw-bold">{{ $context->id }}</td>
                                    <td>
                                        <div class="small text-muted">{{ $context->created_at->format('d/m/Y') }}</div>
                                        <div class="extra-small">{{ $context->created_at->format('H:i') }}</div>
                                    </td>
                                    <td>
                                        @php
                                            $evaluationTypes = [
                                                'initial' => 'Avaliação Inicial',
                                                'periodic_review' => 'Revisão Periódica',
                                                'pei_review' => 'Revisão PEI',
                                                'specific_demand' => 'Demanda Específica'
                                            ];
                                            $typeColors = [
                                                'initial' => 'primary',
                                                'periodic_review' => 'info',
                                                'pei_review' => 'warning',
                                                'specific_demand' => 'secondary'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $typeColors[$context->evaluation_type] ?? 'secondary' }} badge-evaluation">
                                            {{ $evaluationTypes[$context->evaluation_type] ?? $context->evaluation_type }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $learningLevels = [
                                                'very_low' => ['Muito Baixo', 'danger'],
                                                'low' => ['Baixo', 'warning'],
                                                'adequate' => ['Adequado', 'info'],
                                                'good' => ['Bom', 'success'],
                                                'excellent' => ['Excelente', 'success']
                                            ];
                                        @endphp
                                        @if($context->learning_level)
                                            <span class="badge bg-{{ $learningLevels[$context->learning_level][1] ?? 'secondary' }}">
                                                {{ $learningLevels[$context->learning_level][0] ?? $context->learning_level }}
                                            </span>
                                        @else
                                            <span class="text-muted small">Não informado</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $autonomyLevels = [
                                                'dependent' => ['Dependente', 'danger'],
                                                'partial' => ['Parcial', 'warning'],
                                                'independent' => ['Independente', 'success']
                                            ];
                                        @endphp
                                        @if($context->autonomy_level)
                                            <span class="badge bg-{{ $autonomyLevels[$context->autonomy_level][1] ?? 'secondary' }}">
                                                {{ $autonomyLevels[$context->autonomy_level][0] ?? $context->autonomy_level }}
                                            </span>
                                        @else
                                            <span class="text-muted small">Não informado</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($context->is_current)
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle me-1"></i> Atual
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">Histórico</span>
                                        @endif
                                    </td>
                                    <td class="text-center action-buttons">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('specialized-educational-support.student-context.show', $context->id) }}" 
                                               class="btn btn-outline-primary" 
                                               title="Visualizar">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('specialized-educational-support.student-context.edit', $context->id) }}" 
                                               class="btn btn-outline-warning" 
                                               title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            @if(!$context->is_current)
                                                <form action="{{ route('specialized-educational-support.student-context.set-current', $context->id) }}" 
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" 
                                                            class="btn btn-outline-success" 
                                                            title="Definir como atual"
                                                            onclick="return confirm('Definir este contexto como atual?')">
                                                        <i class="bi bi-check-lg"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            <form action="{{ route('specialized-educational-support.student-context.destroy', $context->id) }}" 
                                                  method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-outline-danger" 
                                                        title="Excluir"
                                                        onclick="return confirm('Tem certeza que deseja excluir este contexto?')">
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
                
                <!-- Rodapé da tabela -->
                <div class="card-footer bg-white d-flex justify-content-between align-items-center p-3">
                    <div class="text-muted small">
                        <i class="bi bi-info-circle me-1"></i>
                        Contextos marcados com <span class="badge bg-success">Atual</span> estão em uso no momento.
                    </div>
                    <div>
                        <a href="{{ route('specialized-educational-support.student-context.create', $student->id) }}" 
                           class="btn btn-primary btn-sm">
                            <i class="bi bi-plus-circle me-1"></i> Adicionar Novo Contexto
                        </a>
                    </div>
                </div>
            @else
                <!-- Estado vazio -->
                <div class="card-body empty-state">
                    <div class="empty-state-icon">
                        <i class="bi bi-file-earmark-text"></i>
                    </div>
                    <h4 class="text-secondary mb-3">Nenhum contexto cadastrado</h4>
                    <p class="text-muted mb-4">Este aluno ainda não possui registros de contexto educacional.</p>
                    
                    <a href="{{ route('specialized-educational-support.student-context.create', $student->id) }}" 
                       class="btn btn-primary px-4">
                        <i class="bi bi-plus-circle me-2"></i> Criar Primeiro Contexto
                    </a>
                    
                    <div class="mt-4 small text-muted">
                        <i class="bi bi-lightbulb me-1"></i>
                        O contexto educacional ajuda a acompanhar a evolução do aluno ao longo do tempo.
                    </div>
                </div>
            @endif
        </div>

        <!-- Links de navegação -->
        <div class="mt-4 d-flex justify-content-between">
            <a href="{{ route('specialized-educational-support.students.index') }}" 
               class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Voltar para Alunos
            </a>
            
            @if($contexts->count() > 0)
                <div class="text-muted small">
                    Mostrando {{ $contexts->count() }} contexto(s)
                </div>
            @endif
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Confirmação para exclusão
        document.addEventListener('DOMContentLoaded', function() {
            const deleteForms = document.querySelectorAll('form[action*="destroy"]');
            deleteForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    if (!confirm('Tem certeza que deseja excluir este contexto?\nEsta ação não pode ser desfeita.')) {
                        e.preventDefault();
                    }
                });
            });
        });
    </script>
</body>
</html>