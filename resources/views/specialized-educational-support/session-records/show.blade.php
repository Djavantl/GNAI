<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Sessão - #{{ $sessionRecord->id }}</title>
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
            font-weight: 500;
        }
        .info-value {
            font-weight: 500;
            font-size: 1rem;
            color: #212529;
            padding: 8px 0;
        }
        .info-section {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #0d6efd;
        }
        .section-title {
            border-left: 5px solid #0d6efd;
            padding-left: 10px;
            margin-bottom: 15px;
            color: #0d6efd;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <!-- Cabeçalho -->
        <div class="card shadow border-0 mb-4">
            <div class="card-header text-white d-flex justify-content-between align-items-center p-3">
                <div>
                    <h4 class="mb-0"><i class="bi bi-journal-text me-2"></i>Registro de Sessão</h4>
                    <p class="mb-0 mt-2 opacity-75">
                        @if($sessionRecord->session && $sessionRecord->session->student)
                            Aluno: {{ $sessionRecord->session->student->person->name }} • 
                            Sessão #{{ $sessionRecord->attendance_sessions_id }}
                        @else
                            Registro #{{ $sessionRecord->id }}
                        @endif
                    </p>
                </div>
                <a href="{{ route('specialized-educational-support.sessions.show', $sessionRecord->attendance_sessions_id) }}" class="btn btn-light btn-sm">
                    <i class="bi bi-arrow-left"></i> Voltar
                </a>
            </div>
        </div>

        <!-- Informações Gerais -->
        <div class="card shadow border-0 mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0 text-dark"><i class="bi bi-info-circle me-2"></i>Informações Básicas</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <div class="info-label">Data do Registro</div>
                        <div class="info-value">
                            {{ $sessionRecord->record_date ? \Carbon\Carbon::parse($sessionRecord->record_date)->format('d/m/Y') : 'Não informado' }}
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="info-label">Duração</div>
                        <div class="info-value">
                            <span class="badge bg-info">{{ $sessionRecord->duration }}</span>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="info-label">Participação</div>
                        <div class="info-value">
                            {{ $sessionRecord->student_participation }}
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="info-label">Engajamento</div>
                        <div class="info-value">
                            {{ $sessionRecord->engagement_level ?? 'Não informado' }}
                        </div>
                    </div>
                </div>
                @if($sessionRecord->external_referral_needed)
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-warning mt-2">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Encaminhamento Externo Necessário</strong>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Atividades e Estratégias -->
        <div class="card shadow border-0 mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0 text-dark"><i class="bi bi-list-task me-2"></i>Atividades e Estratégias</h5>
            </div>
            <div class="card-body">
                <div class="info-section">
                    <h6 class="section-title">Atividades Realizadas</h6>
                    <div class="info-value">
                        {{ $sessionRecord->activities_performed }}
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-section">
                            <h6 class="section-title">Estratégias Utilizadas</h6>
                            <div class="info-value">
                                {{ $sessionRecord->strategies_used ?? 'Não informado' }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-section">
                            <h6 class="section-title">Recursos Utilizados</h6>
                            <div class="info-value">
                                {{ $sessionRecord->resources_used ?? 'Não informado' }}
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="info-section">
                    <h6 class="section-title">Adaptações Realizadas</h6>
                    <div class="info-value">
                        {{ $sessionRecord->adaptations_made ?? 'Não informado' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Comportamento e Observações -->
        <div class="card shadow border-0 mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0 text-dark"><i class="bi bi-eye me-2"></i>Comportamento e Observações</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-section">
                            <h6 class="section-title">Comportamento Observado</h6>
                            <div class="info-value">
                                {{ $sessionRecord->observed_behavior ?? 'Não informado' }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-section">
                            <h6 class="section-title">Resposta às Atividades</h6>
                            <div class="info-value">
                                {{ $sessionRecord->response_to_activities ?? 'Não informado' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Avaliação do Desenvolvimento -->
        <div class="card shadow border-0 mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0 text-dark"><i class="bi bi-graph-up me-2"></i>Avaliação do Desenvolvimento</h5>
            </div>
            <div class="card-body">
                <div class="info-section">
                    <h6 class="section-title">Avaliação do Desenvolvimento</h6>
                    <div class="info-value">
                        {{ $sessionRecord->development_evaluation }}
                    </div>
                </div>
                
                <div class="info-section">
                    <h6 class="section-title">Indicadores de Progresso</h6>
                    <div class="info-value">
                        {{ $sessionRecord->progress_indicators ?? 'Não informado' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Recomendações -->
        <div class="card shadow border-0 mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0 text-dark"><i class="bi bi-chat-left-text me-2"></i>Recomendações e Ajustes</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-section">
                            <h6 class="section-title">Recomendações</h6>
                            <div class="info-value">
                                {{ $sessionRecord->recommendations ?? 'Não informado' }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-section">
                            <h6 class="section-title">Ajustes para Próxima Sessão</h6>
                            <div class="info-value">
                                {{ $sessionRecord->next_session_adjustments ?? 'Não informado' }}
                            </div>
                        </div>
                    </div>
                </div>
                
                @if($sessionRecord->general_observations)
                <div class="info-section">
                    <h6 class="section-title">Observações Gerais</h6>
                    <div class="info-value">
                        {{ $sessionRecord->general_observations }}
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Ações -->
        <div class="card shadow border-0 mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted">
                            <i class="bi bi-clock-history me-1"></i>
                            Criado em: {{ $sessionRecord->created_at->format('d/m/Y H:i') }} | 
                            Atualizado em: {{ $sessionRecord->updated_at->format('d/m/Y H:i') }}
                        </small>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('specialized-educational-support.sessions.show', $sessionRecord->attendance_sessions_id) }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Voltar para Sessão
                        </a>
                        <a href="{{ route('specialized-educational-support.session-records.edit', $sessionRecord) }}" class="btn btn-warning">
                            <i class="bi bi-pencil me-1"></i> Editar
                        </a>
                        <form action="{{ route('specialized-educational-support.session-records.destroy', $sessionRecord) }}" 
                              method="POST" 
                              onsubmit="return confirm('Tem certeza que deseja excluir este registro?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger">
                                <i class="bi bi-trash me-1"></i> Excluir
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>