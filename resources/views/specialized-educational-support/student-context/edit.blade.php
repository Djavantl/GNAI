<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Contexto - {{ $student_context->student->name ?? 'Aluno' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .section-title { 
            border-left: 5px solid #ffc107; 
            padding-left: 10px; 
            margin-bottom: 20px; 
            color: #856404;
            margin-top: 30px;
        }
        .card-header {
            background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
        }
        .form-check-label {
            font-weight: 500;
        }
        .checkbox-group {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="card shadow border-0">
            <div class="card-header text-dark p-3">
                <h4 class="mb-0">Editar Contexto: {{ $student_context->student->name ?? 'Aluno' }}</h4>
                <p class="mb-0 mt-2 opacity-75">Atualize os dados do contexto educacional do aluno</p>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('specialized-educational-support.student-context.update', $student_context->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Seção 1: Aprendizagem e Cognição -->
                    <h5 class="section-title">Aprendizagem e Cognição</h5>
                    <div class="row mb-4">
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold">Nível de Aprendizagem</label>
                            <select name="learning_level" class="form-select">
                                <option value="">Selecione...</option>
                                <option value="very_low" {{ $student_context->learning_level == 'very_low' ? 'selected' : '' }}>Muito Baixo</option>
                                <option value="low" {{ $student_context->learning_level == 'low' ? 'selected' : '' }}>Baixo</option>
                                <option value="adequate" {{ $student_context->learning_level == 'adequate' ? 'selected' : '' }}>Adequado</option>
                                <option value="good" {{ $student_context->learning_level == 'good' ? 'selected' : '' }}>Bom</option>
                                <option value="excellent" {{ $student_context->learning_level == 'excellent' ? 'selected' : '' }}>Excelente</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold">Nível de Atenção</label>
                            <select name="attention_level" class="form-select">
                                <option value="">Selecione...</option>
                                <option value="very_low" {{ $student_context->attention_level == 'very_low' ? 'selected' : '' }}>Muito Baixo</option>
                                <option value="low" {{ $student_context->attention_level == 'low' ? 'selected' : '' }}>Baixo</option>
                                <option value="moderate" {{ $student_context->attention_level == 'moderate' ? 'selected' : '' }}>Moderado</option>
                                <option value="high" {{ $student_context->attention_level == 'high' ? 'selected' : '' }}>Alto</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold">Nível de Memória</label>
                            <select name="memory_level" class="form-select">
                                <option value="">Selecione...</option>
                                <option value="low" {{ $student_context->memory_level == 'low' ? 'selected' : '' }}>Baixo</option>
                                <option value="moderate" {{ $student_context->memory_level == 'moderate' ? 'selected' : '' }}>Moderado</option>
                                <option value="good" {{ $student_context->memory_level == 'good' ? 'selected' : '' }}>Bom</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold">Raciocínio</label>
                            <select name="reasoning_level" class="form-select">
                                <option value="">Selecione...</option>
                                <option value="concrete" {{ $student_context->reasoning_level == 'concrete' ? 'selected' : '' }}>Concreto</option>
                                <option value="mixed" {{ $student_context->reasoning_level == 'mixed' ? 'selected' : '' }}>Misto</option>
                                <option value="abstract" {{ $student_context->reasoning_level == 'abstract' ? 'selected' : '' }}>Abstrato</option>
                            </select>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">Observações de Aprendizagem</label>
                            <textarea name="learning_observations" class="form-control" rows="3">{{ old('learning_observations', $student_context->learning_observations) }}</textarea>
                        </div>
                    </div>

                    <!-- Seção 2: Comunicação, Interação e Comportamento -->
                    <h5 class="section-title">Comunicação, Interação e Comportamento</h5>
                    <div class="row mb-4">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Tipo de Comunicação</label>
                            <select name="communication_type" class="form-select">
                                <option value="">Selecione...</option>
                                <option value="verbal" {{ $student_context->communication_type == 'verbal' ? 'selected' : '' }}>Verbal</option>
                                <option value="non_verbal" {{ $student_context->communication_type == 'non_verbal' ? 'selected' : '' }}>Não Verbal</option>
                                <option value="mixed" {{ $student_context->communication_type == 'mixed' ? 'selected' : '' }}>Mista</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Nível de Interação</label>
                            <select name="interaction_level" class="form-select">
                                <option value="">Selecione...</option>
                                <option value="very_low" {{ $student_context->interaction_level == 'very_low' ? 'selected' : '' }}>Muito Baixo</option>
                                <option value="low" {{ $student_context->interaction_level == 'low' ? 'selected' : '' }}>Baixo</option>
                                <option value="moderate" {{ $student_context->interaction_level == 'moderate' ? 'selected' : '' }}>Moderado</option>
                                <option value="good" {{ $student_context->interaction_level == 'good' ? 'selected' : '' }}>Bom</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Nível de Socialização</label>
                            <select name="socialization_level" class="form-select">
                                <option value="">Selecione...</option>
                                <option value="isolated" {{ $student_context->socialization_level == 'isolated' ? 'selected' : '' }}>Isolado</option>
                                <option value="selective" {{ $student_context->socialization_level == 'selective' ? 'selected' : '' }}>Seletivo</option>
                                <option value="participative" {{ $student_context->socialization_level == 'participative' ? 'selected' : '' }}>Participativo</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="checkbox-group">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="shows_aggressive_behavior" value="1" id="agressivo" {{ $student_context->shows_aggressive_behavior ? 'checked' : '' }}>
                                    <label class="form-check-label" for="agressivo">Apresenta Comportamento Agressivo</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="shows_withdrawn_behavior" value="1" id="retraido" {{ $student_context->shows_withdrawn_behavior ? 'checked' : '' }}>
                                    <label class="form-check-label" for="retraido">Apresenta Comportamento Retraído</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Observações de Comportamento</label>
                            <textarea name="behavior_notes" class="form-control" rows="3">{{ old('behavior_notes', $student_context->behavior_notes) }}</textarea>
                        </div>
                    </div>

                    <!-- Seção 3: Autonomia e Apoios -->
                    <h5 class="section-title">Autonomia e Apoios</h5>
                    <div class="row mb-4">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Nível de Autonomia</label>
                            <select name="autonomy_level" class="form-select">
                                <option value="">Selecione...</option>
                                <option value="dependent" {{ $student_context->autonomy_level == 'dependent' ? 'selected' : '' }}>Dependente</option>
                                <option value="partial" {{ $student_context->autonomy_level == 'partial' ? 'selected' : '' }}>Parcial</option>
                                <option value="independent" {{ $student_context->autonomy_level == 'independent' ? 'selected' : '' }}>Independente</option>
                            </select>
                        </div>
                        <div class="col-md-8 mb-3">
                            <div class="checkbox-group">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="needs_mobility_support" value="1" id="mobilidade" {{ $student_context->needs_mobility_support ? 'checked' : '' }}>
                                            <label class="form-check-label" for="mobilidade">Necessita Apoio à Mobilidade</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="needs_communication_support" value="1" id="comunicacao" {{ $student_context->needs_communication_support ? 'checked' : '' }}>
                                            <label class="form-check-label" for="comunicacao">Necessita Apoio à Comunicação</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="needs_pedagogical_adaptation" value="1" id="adaptacao" {{ $student_context->needs_pedagogical_adaptation ? 'checked' : '' }}>
                                            <label class="form-check-label" for="adaptacao">Necessita Adaptação Pedagógica</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="uses_assistive_technology" value="1" id="tecnologia" {{ $student_context->uses_assistive_technology ? 'checked' : '' }}>
                                            <label class="form-check-label" for="tecnologia">Usa Tecnologia Assistiva</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Seção 4: Saúde -->
                    <h5 class="section-title">Saúde</h5>
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <div class="checkbox-group">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="has_medical_report" value="1" id="laudo" {{ $student_context->has_medical_report ? 'checked' : '' }}>
                                    <label class="form-check-label" for="laudo">Possui Laudo Médico</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="uses_medication" value="1" id="medicacao" {{ $student_context->uses_medication ? 'checked' : '' }}>
                                    <label class="form-check-label" for="medicacao">Usa Medicação</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Observações de Saúde</label>
                            <textarea name="medical_notes" class="form-control" rows="3">{{ old('medical_notes', $student_context->medical_notes) }}</textarea>
                        </div>
                    </div>

                    <!-- Seção 5: Avaliação Geral -->
                    <h5 class="section-title">Avaliação Geral</h5>
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Pontos Fortes / Potencialidades</label>
                            <textarea name="strengths" class="form-control" rows="3">{{ old('strengths', $student_context->strengths) }}</textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Dificuldades</label>
                            <textarea name="difficulties" class="form-control" rows="3">{{ old('difficulties', $student_context->difficulties) }}</textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Recomendações</label>
                            <textarea name="recommendations" class="form-control" rows="3">{{ old('recommendations', $student_context->recommendations) }}</textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Observação Geral</label>
                            <textarea name="general_observation" class="form-control" rows="3">{{ old('general_observation', $student_context->general_observation) }}</textarea>
                        </div>
                    </div>

                    <!-- Botões -->
                    <div class="d-flex justify-content-between mt-5 pt-4 border-top">
                        <a href="{{ route('specialized-educational-support.student-context.show', $student_context->student_id) }}" class="btn btn-outline-secondary px-4">
                            <i class="bi bi-arrow-left"></i> Voltar
                        </a>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-danger px-4" onclick="confirmDelete()">
                                <i class="bi bi-trash"></i> Excluir
                            </button>
                            <button type="submit" class="btn btn-warning px-5 fw-bold">
                                <i class="bi bi-save"></i> Atualizar Contexto
                            </button>
                        </div>
                    </div>
                </form>
                
                <!-- Formulário de exclusão -->
                <form id="delete-form" action="{{ route('specialized-educational-support.student-context.destroy', $student_context->id) }}" method="POST" class="d-none">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
    </div>

    <!-- Adicionando ícones do Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function confirmDelete() {
            if (confirm('Tem certeza que deseja excluir este contexto? Esta ação não pode ser desfeita.')) {
                document.getElementById('delete-form').submit();
            }
        }
        
        // Adiciona validação básica antes do envio
        document.querySelector('form').addEventListener('submit', function(e) {
            // Validação opcional pode ser adicionada aqui
            // Exemplo: verificar se pelo menos um campo foi preenchido
            const hasMedicalReport = document.querySelector('input[name="has_medical_report"]').checked;
            const usesMedication = document.querySelector('input[name="uses_medication"]').checked;
            const medicalNotes = document.querySelector('textarea[name="medical_notes"]').value;
            
            if ((hasMedicalReport || usesMedication) && medicalNotes.trim() === '') {
                if (!confirm('Você marcou opções de saúde, mas não preencheu as observações médicas. Deseja continuar?')) {
                    e.preventDefault();
                }
            }
        });
    </script>
</body>
</html>