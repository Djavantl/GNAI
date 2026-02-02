<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Registro de Sessão - #{{ $sessionRecord->id }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; }
        .card-header {
            background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
        }
        .section-title { 
            border-left: 5px solid #ffc107; 
            padding-left: 10px; 
            margin-bottom: 20px; 
            color: #856404;
            margin-top: 30px;
        }
        .form-check-label {
            font-weight: 500;
        }
        .form-section {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            border-left: 4px solid #ffc107;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="card shadow border-0">
            <div class="card-header text-dark p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0"><i class="bi bi-pencil me-2"></i>Editar Registro de Sessão</h4>
                        <p class="mb-0 mt-2 opacity-75">
                            @if($sessionRecord->session && $sessionRecord->session->student)
                                Aluno: {{ $sessionRecord->session->student->person->name }} • 
                                Sessão #{{ $sessionRecord->attendance_sessions_id }} • Registro #{{ $sessionRecord->id }}
                            @else
                                Registro #{{ $sessionRecord->id }}
                            @endif
                        </p>
                    </div>
                    <a href="{{ route('specialized-educational-support.session-records.show', $sessionRecord) }}" class="btn btn-dark btn-sm">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>
            <div class="card-body p-4">
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <h5 class="alert-heading"><i class="bi bi-exclamation-triangle me-2"></i>Erros no formulário</h5>
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form action="{{ route('specialized-educational-support.session-records.update', $sessionRecord) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="attendance_sessions_id" value="{{ $sessionRecord->attendance_sessions_id }}">

                    <!-- Informações Básicas -->
                    <div class="form-section">
                        <h5 class="section-title">Informações da Sessão</h5>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Data do Registro</label>
                                <input type="date" name="record_date" class="form-control" 
                                       value="{{ old('record_date', $sessionRecord->record_date ? \Carbon\Carbon::parse($sessionRecord->record_date)->format('Y-m-d') : '') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Duração <span class="text-danger">*</span></label>
                                <input type="text" name="duration" class="form-control" 
                                       value="{{ old('duration', $sessionRecord->duration) }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Participação do Aluno <span class="text-danger">*</span></label>
                                <input type="text" name="student_participation" class="form-control" 
                                       value="{{ old('student_participation', $sessionRecord->student_participation) }}" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Nível de Engajamento</label>
                                <input type="text" name="engagement_level" class="form-control" 
                                       value="{{ old('engagement_level', $sessionRecord->engagement_level) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" name="external_referral_needed" value="1" 
                                           id="external_referral" {{ old('external_referral_needed', $sessionRecord->external_referral_needed) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="external_referral">
                                        Encaminhamento Externo Necessário?
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Atividades e Estratégias -->
                    <div class="form-section">
                        <h5 class="section-title">Atividades e Estratégias</h5>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-bold">Atividades Realizadas <span class="text-danger">*</span></label>
                                <textarea name="activities_performed" class="form-control" rows="3" required>{{ old('activities_performed', $sessionRecord->activities_performed) }}</textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Estratégias Utilizadas</label>
                                <textarea name="strategies_used" class="form-control" rows="2">{{ old('strategies_used', $sessionRecord->strategies_used) }}</textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Recursos Utilizados</label>
                                <textarea name="resources_used" class="form-control" rows="2">{{ old('resources_used', $sessionRecord->resources_used) }}</textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-bold">Adaptações Realizadas</label>
                                <textarea name="adaptations_made" class="form-control" rows="2">{{ old('adaptations_made', $sessionRecord->adaptations_made) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Comportamento e Observações -->
                    <div class="form-section">
                        <h5 class="section-title">Comportamento e Observações</h5>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-bold">Comportamento Observado</label>
                                <textarea name="observed_behavior" class="form-control" rows="3">{{ old('observed_behavior', $sessionRecord->observed_behavior) }}</textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-bold">Resposta às Atividades</label>
                                <textarea name="response_to_activities" class="form-control" rows="2">{{ old('response_to_activities', $sessionRecord->response_to_activities) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Avaliação do Desenvolvimento -->
                    <div class="form-section">
                        <h5 class="section-title">Avaliação do Desenvolvimento</h5>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-bold">Avaliação do Desenvolvimento <span class="text-danger">*</span></label>
                                <textarea name="development_evaluation" class="form-control" rows="3" required>{{ old('development_evaluation', $sessionRecord->development_evaluation) }}</textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-bold">Indicadores de Progresso</label>
                                <textarea name="progress_indicators" class="form-control" rows="2">{{ old('progress_indicators', $sessionRecord->progress_indicators) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Recomendações -->
                    <div class="form-section">
                        <h5 class="section-title">Recomendações e Ajustes</h5>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-bold">Recomendações</label>
                                <textarea name="recommendations" class="form-control" rows="2">{{ old('recommendations', $sessionRecord->recommendations) }}</textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-bold">Ajustes para Próxima Sessão</label>
                                <textarea name="next_session_adjustments" class="form-control" rows="2">{{ old('next_session_adjustments', $sessionRecord->next_session_adjustments) }}</textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-bold">Observações Gerais</label>
                                <textarea name="general_observations" class="form-control" rows="3">{{ old('general_observations', $sessionRecord->general_observations) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Botões -->
                    <div class="d-flex justify-content-between mt-5 pt-4 border-top">
                        <a href="{{ route('specialized-educational-support.session-records.show', $sessionRecord) }}" class="btn btn-outline-secondary px-4">
                            <i class="bi bi-x-circle me-1"></i> Cancelar
                        </a>
                        <div class="d-flex gap-2">
                            
                            <button type="submit" class="btn btn-warning px-5 fw-bold">
                                <i class="bi bi-check-circle me-1"></i> Atualizar Registro
                            </button>
                        </div>
                    </div>
                </form>
                <form action="{{ route('specialized-educational-support.session-records.destroy', $sessionRecord) }}" 
                        method="POST" 
                        class="d-inline"
                        onsubmit="return confirm('Tem certeza que deseja excluir permanentemente este registro?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger px-4">
                        <i class="bi bi-trash me-1"></i> Excluir
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>