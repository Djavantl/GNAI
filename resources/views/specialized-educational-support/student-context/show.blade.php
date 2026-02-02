<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contexto Educacional - {{ $student->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f4f7f6; }
        .section-title { 
            border-left: 5px solid #0d6efd; 
            padding-left: 10px; 
            margin-bottom: 20px; 
            color: #0d6efd;
            margin-top: 30px;
        }
        .info-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            border: 1px solid #e9ecef;
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
        .badge-custom {
            font-size: 0.85rem;
            padding: 5px 10px;
            border-radius: 20px;
        }
        .tag-list {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
        }
        .tag-item {
            background-color: #e9ecef;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.85rem;
        }
        .empty-state { 
            padding: 60px 20px; 
            text-align: center; 
        }
        .empty-state-icon { 
            font-size: 4rem; 
            color: #dee2e6; 
            margin-bottom: 20px; 
        }
        .card-header {
            background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
        }
        .print-only {
            display: none;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            .print-only {
                display: block !important;
            }
            .info-card {
                box-shadow: none;
                border: 1px solid #dee2e6;
                page-break-inside: avoid;
            }
            body {
                background-color: white !important;
                font-size: 12pt;
            }
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <!-- Cabeçalho -->
        <div class="card shadow border-0 mb-4">
            <div class="card-header text-white d-flex justify-content-between align-items-center p-3">
                <div>
                    <h4 class="mb-0"><i class="bi bi-person-badge me-2"></i>Contexto Educacional</h4>
                    <p class="mb-0 mt-2 opacity-75">{{ $student->name }} • Matrícula: {{ $student->registration ?? 'N/A' }}</p>
                </div>
                <div class="d-flex gap-2">
                    <button onclick="window.print()" class="btn btn-light btn-sm no-print">
                        <i class="bi bi-printer"></i> Imprimir
                    </button>
                    @if(isset($context) && $context)
                        <a href="{{ route('specialized-educational-support.student-context.edit', $context->id) }}" class="btn btn-warning btn-sm fw-bold">
                            <i class="bi bi-pencil"></i> Editar
                        </a>
                    @endif
                </div>
            </div>
        </div>

        @if(isset($context) && $context)
            <!-- Informações Gerais -->
            <div class="card shadow border-0 mb-4">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-dark"><i class="bi bi-info-circle me-2"></i>Resumo do Contexto</h5>
                    <span class="badge bg-primary">Criado em: {{ $context->created_at->format('d/m/Y') }}</span>
                </div>
                <div class="card-body">
                    <div class="row mt-3">
                        <div class="col-md-4">
                            <div class="info-label">Tipo de Avaliação</div>
                            <div class="info-value">
                                @php
                                    $evaluationTypes = [
                                        'initial' => 'Avaliação Inicial',
                                        'periodic_review' => 'Revisão Periódica',
                                        'pei_review' => 'Revisão PEI',
                                        'specific_demand' => 'Demanda Específica'
                                    ];
                                @endphp
                                <span class="badge bg-info">
                                    {{ $evaluationTypes[$context->evaluation_type] ?? $context->evaluation_type }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-label">Status</div>
                            <div class="info-value">
                                @if($context->is_current)
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle"></i> Contexto Atual
                                    </span>
                                @else
                                    <span class="badge bg-secondary">Contexto Histórico</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-label">Última Atualização</div>
                            <div class="info-value">
                                {{ $context->updated_at->format('d/m/Y H:i') }}
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-3">
                            <div class="info-card">
                                <div class="info-label">Nível de Aprendizagem</div>
                                <div class="info-value">
                                    @php
                                        $learningLevels = [
                                            'very_low' => 'Muito Baixo',
                                            'low' => 'Baixo',
                                            'adequate' => 'Adequado',
                                            'good' => 'Bom',
                                            'excellent' => 'Excelente'
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $context->learning_level == 'very_low' || $context->learning_level == 'low' ? 'warning' : 'success' }} badge-custom">
                                        {{ $learningLevels[$context->learning_level] ?? 'Não informado' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-card">
                                <div class="info-label">Tipo de Comunicação</div>
                                <div class="info-value">
                                    @php
                                        $communicationTypes = [
                                            'verbal' => 'Verbal',
                                            'non_verbal' => 'Não Verbal',
                                            'mixed' => 'Mista'
                                        ];
                                    @endphp
                                    <span class="badge bg-info badge-custom">
                                        {{ $communicationTypes[$context->communication_type] ?? 'Não informado' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-card">
                                <div class="info-label">Nível de Autonomia</div>
                                <div class="info-value">
                                    @php
                                        $autonomyLevels = [
                                            'dependent' => 'Dependente',
                                            'partial' => 'Parcial',
                                            'independent' => 'Independente'
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $context->autonomy_level == 'dependent' ? 'danger' : ($context->autonomy_level == 'partial' ? 'warning' : 'success') }} badge-custom">
                                        {{ $autonomyLevels[$context->autonomy_level] ?? 'Não informado' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-card">
                                <div class="info-label">Status Médico</div>
                                <div class="info-value">
                                    @if($context->has_medical_report)
                                        <span class="badge bg-success badge-custom"><i class="bi bi-file-medical"></i> Com Laudo</span>
                                    @else
                                        <span class="badge bg-secondary badge-custom">Sem Laudo</span>
                                    @endif
                                    @if($context->uses_medication)
                                        <span class="badge bg-warning badge-custom mt-1 d-block"><i class="bi bi-capsule"></i> Usa Medicação</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Aprendizagem e Cognição -->
            <div class="card shadow border-0 mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0 text-dark"><i class="bi bi-brain me-2"></i>Aprendizagem e Cognição</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="info-label">Nível de Atenção</div>
                            <div class="info-value">
                                @php
                                    $attentionLevels = [
                                        'very_low' => 'Muito Baixo',
                                        'low' => 'Baixo',
                                        'moderate' => 'Moderado',
                                        'high' => 'Alto'
                                    ];
                                @endphp
                                {{ $attentionLevels[$context->attention_level] ?? 'Não informado' }}
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="info-label">Nível de Memória</div>
                            <div class="info-value">
                                @php
                                    $memoryLevels = [
                                        'low' => 'Baixo',
                                        'moderate' => 'Moderado',
                                        'good' => 'Bom'
                                    ];
                                @endphp
                                {{ $memoryLevels[$context->memory_level] ?? 'Não informado' }}
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="info-label">Tipo de Raciocínio</div>
                            <div class="info-value">
                                @php
                                    $reasoningLevels = [
                                        'concrete' => 'Concreto',
                                        'mixed' => 'Misto',
                                        'abstract' => 'Abstrato'
                                    ];
                                @endphp
                                {{ $reasoningLevels[$context->reasoning_level] ?? 'Não informado' }}
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="info-label">Data da Avaliação</div>
                            <div class="info-value">
                                {{ $context->created_at->format('d/m/Y') }}
                            </div>
                        </div>
                    </div>
                    
                    @if($context->learning_observations)
                        <div class="mt-3">
                            <div class="info-label">Observações de Aprendizagem</div>
                            <div class="info-value bg-light p-3 rounded">
                                {{ $context->learning_observations }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Comunicação, Interação e Comportamento -->
            <div class="card shadow border-0 mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0 text-dark"><i class="bi bi-chat-dots me-2"></i>Comunicação, Interação e Comportamento</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="info-label">Nível de Interação</div>
                            <div class="info-value">
                                @php
                                    $interactionLevels = [
                                        'very_low' => 'Muito Baixo',
                                        'low' => 'Baixo',
                                        'moderate' => 'Moderado',
                                        'good' => 'Bom'
                                    ];
                                @endphp
                                {{ $interactionLevels[$context->interaction_level] ?? 'Não informado' }}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-label">Nível de Socialização</div>
                            <div class="info-value">
                                @php
                                    $socializationLevels = [
                                        'isolated' => 'Isolado',
                                        'selective' => 'Seletivo',
                                        'participative' => 'Participativo'
                                    ];
                                @endphp
                                {{ $socializationLevels[$context->socialization_level] ?? 'Não informado' }}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-label">Comportamentos</div>
                            <div class="info-value">
                                <div class="tag-list">
                                    @if($context->shows_aggressive_behavior)
                                        <span class="tag-item bg-danger text-white">
                                            <i class="bi bi-exclamation-triangle"></i> Agressivo
                                        </span>
                                    @endif
                                    @if($context->shows_withdrawn_behavior)
                                        <span class="tag-item bg-warning text-dark">
                                            <i class="bi bi-person-x"></i> Retraído
                                        </span>
                                    @endif
                                    @if(!$context->shows_aggressive_behavior && !$context->shows_withdrawn_behavior)
                                        <span class="tag-item bg-success text-white">
                                            <i class="bi bi-check-circle"></i> Estável
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    @if($context->behavior_notes)
                        <div class="mt-3">
                            <div class="info-label">Observações de Comportamento</div>
                            <div class="info-value bg-light p-3 rounded">
                                {{ $context->behavior_notes }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Autonomia e Apoios -->
            <div class="card shadow border-0 mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0 text-dark"><i class="bi bi-universal-access me-2"></i>Autonomia e Apoios</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="info-label mb-2">Recursos e Apoios Necessários</div>
                            <div class="tag-list">
                                @if($context->needs_mobility_support)
                                    <span class="tag-item bg-primary text-white">
                                        <i class="bi bi-wheelchair"></i> Apoio à Mobilidade
                                    </span>
                                @endif
                                @if($context->needs_communication_support)
                                    <span class="tag-item bg-info text-white">
                                        <i class="bi bi-megaphone"></i> Apoio à Comunicação
                                    </span>
                                @endif
                                @if($context->needs_pedagogical_adaptation)
                                    <span class="tag-item bg-warning text-dark">
                                        <i class="bi bi-journal-text"></i> Adaptação Pedagógica
                                    </span>
                                @endif
                                @if($context->uses_assistive_technology)
                                    <span class="tag-item bg-success text-white">
                                        <i class="bi bi-laptop"></i> Tecnologia Assistiva
                                    </span>
                                @endif
                                @if(!$context->needs_mobility_support && !$context->needs_communication_support && !$context->needs_pedagogical_adaptation && !$context->uses_assistive_technology)
                                    <span class="tag-item bg-secondary text-white">
                                        Nenhum apoio específico necessário
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Saúde -->
            <div class="card shadow border-0 mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0 text-dark"><i class="bi bi-heart-pulse me-2"></i>Saúde</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="info-label">Informações Médicas</div>
                            <div class="info-value">
                                <div class="d-flex flex-column gap-2">
                                    <div>
                                        <i class="bi bi-file-medical"></i>
                                        <strong>Laudo Médico:</strong>
                                        <span class="{{ $context->has_medical_report ? 'text-success' : 'text-secondary' }}">
                                            {{ $context->has_medical_report ? 'Sim' : 'Não' }}
                                        </span>
                                    </div>
                                    <div>
                                        <i class="bi bi-capsule"></i>
                                        <strong>Medicação:</strong>
                                        <span class="{{ $context->uses_medication ? 'text-warning' : 'text-secondary' }}">
                                            {{ $context->uses_medication ? 'Sim' : 'Não' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if($context->medical_notes)
                            <div class="col-md-6">
                                <div class="info-label">Observações de Saúde</div>
                                <div class="info-value bg-light p-3 rounded">
                                    {{ $context->medical_notes }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Avaliação Geral -->
            <div class="card shadow border-0 mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0 text-dark"><i class="bi bi-clipboard-data me-2"></i>Avaliação Geral</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="info-label">Pontos Fortes / Potencialidades</div>
                            <div class="info-value bg-success bg-opacity-10 p-3 rounded border-start border-success border-3">
                                @if($context->strengths)
                                    {{ $context->strengths }}
                                @else
                                    <span class="text-muted">Não informado</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="info-label">Dificuldades</div>
                            <div class="info-value bg-warning bg-opacity-10 p-3 rounded border-start border-warning border-3">
                                @if($context->difficulties)
                                    {{ $context->difficulties }}
                                @else
                                    <span class="text-muted">Não informado</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="info-label">Recomendações</div>
                            <div class="info-value bg-info bg-opacity-10 p-3 rounded border-start border-info border-3">
                                @if($context->recommendations)
                                    {{ $context->recommendations }}
                                @else
                                    <span class="text-muted">Não informado</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="info-label">Observação Geral</div>
                            <div class="info-value bg-light p-3 rounded border">
                                @if($context->general_observation)
                                    {{ $context->general_observation }}
                                @else
                                    <span class="text-muted">Sem observações adicionais</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ações -->
            <div class="card shadow border-0 mb-4 no-print">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">
                                <i class="bi bi-clock-history"></i>
                                Criado em: {{ $context->created_at->format('d/m/Y H:i') }} | 
                                Atualizado em: {{ $context->updated_at->format('d/m/Y H:i') }}
                            </small>
                        </div>
                        <div class="d-flex gap-2">
                            <form action="{{ route('specialized-educational-support.student-context.destroy', $context->id) }}" method="POST" 
                                  onsubmit="return confirm('Tem certeza que deseja excluir permanentemente este contexto? Esta ação não pode ser desfeita.')">
                                @csrf 
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                    <i class="bi bi-trash"></i> Excluir Contexto
                                </button>
                            </form>
                            <a href="{{ route('specialized-educational-support.student-context.edit', $context->id) }}" class="btn btn-warning btn-sm">
                                <i class="bi bi-pencil"></i> Editar Contexto
                            </a>
                            <a href="{{ route('specialized-educational-support.student-context.index', $student->id) }}" class="btn btn-info btn-sm">
                                <i class="bi bi-list"></i> Ver Histórico
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        @else
            <!-- Estado vazio -->
            <div class="card shadow border-0">
                <div class="card-body empty-state">
                    <div class="empty-state-icon">
                        <i class="bi bi-file-earmark-text" style="font-size: 4rem;"></i>
                    </div>
                    <h3 class="text-secondary">Nenhum contexto cadastrado</h3>
                    <p class="text-muted mb-4">Este aluno ainda não possui uma ficha de contexto educacional especializada.</p>
                    
                    <a href="{{ route('specialized-educational-support.student-context.create', $student->id) }}" class="btn btn-primary btn-lg px-5 shadow-sm">
                        <i class="bi bi-plus-circle me-2"></i> Adicionar Contexto
                    </a>
                    <div class="mt-3">
                        <a href="{{ route('specialized-educational-support.student-context.index', $student->id) }}" class="btn btn-outline-secondary">
                            <i class="bi bi-clock-history me-1"></i> Ver Histórico de Contextos
                        </a>
                    </div>
                </div>
            </div>
        @endif

        <!-- Rodapé -->
        <div class="mt-4 d-flex justify-content-between no-print">
            <a href="{{ route('specialized-educational-support.students.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Voltar para Lista de Alunos
            </a>
            
            @if(isset($context) && $context)
                <div class="text-muted">
                    <small>ID do Contexto: {{ $context->id }}</small>
                </div>
            @endif
        </div>

        <!-- Cabeçalho para impressão -->
        <div class="print-only">
            <h1>Contexto Educacional - {{ $student->name }}</h1>
            <p>Data: {{ date('d/m/Y H:i') }}</p>
            <hr>
        </div>
    </div>

    <script>
        // Adiciona confirmação antes de excluir
        document.addEventListener('DOMContentLoaded', function() {
            const deleteForms = document.querySelectorAll('form[onsubmit*="confirm"]');
            deleteForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    if (!confirm('Tem certeza que deseja excluir permanentemente este contexto? Esta ação não pode ser desfeita.')) {
                        e.preventDefault();
                    }
                });
            });
        });
    </script>
</body>
</html>