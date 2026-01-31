<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Contexto - {{ $student->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .section-title { 
            border-left: 5px solid #0d6efd; 
            padding-left: 10px; 
            margin-bottom: 20px; 
            color: #0d6efd;
            margin-top: 30px;
        }
        .card-header {
            background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
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
            <div class="card-header text-white p-3">
                <h4 class="mb-0">Cadastrar Contexto: {{ $student->name }}</h4>
                <p class="mb-0 mt-2 opacity-75">Preencha os dados do contexto educacional do aluno</p>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('specialized-educational-support.student-context.store', $student->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="student_id" value="{{ $student->id }}">

                    <!-- Seção 1: Aprendizagem e Cognição -->
                    <h5 class="section-title">Aprendizagem e Cognição</h5>
                    <div class="row mb-4">
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold">Nível de Aprendizagem</label>
                            <select name="learning_level" class="form-select">
                                <option value="">Selecione...</option>
                                <option value="very_low">Muito Baixo</option>
                                <option value="low">Baixo</option>
                                <option value="adequate">Adequado</option>
                                <option value="good">Bom</option>
                                <option value="excellent">Excelente</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold">Nível de Atenção</label>
                            <select name="attention_level" class="form-select">
                                <option value="">Selecione...</option>
                                <option value="very_low">Muito Baixo</option>
                                <option value="low">Baixo</option>
                                <option value="moderate">Moderado</option>
                                <option value="high">Alto</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold">Nível de Memória</label>
                            <select name="memory_level" class="form-select">
                                <option value="">Selecione...</option>
                                <option value="low">Baixo</option>
                                <option value="moderate">Moderado</option>
                                <option value="good">Bom</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold">Raciocínio</label>
                            <select name="reasoning_level" class="form-select">
                                <option value="">Selecione...</option>
                                <option value="concrete">Concreto</option>
                                <option value="mixed">Misto</option>
                                <option value="abstract">Abstrato</option>
                            </select>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">Observações de Aprendizagem</label>
                            <textarea name="learning_observations" class="form-control" rows="3" placeholder="Observações específicas sobre aprendizagem..."></textarea>
                        </div>
                    </div>

                    <!-- Seção 2: Comunicação, Interação e Comportamento -->
                    <h5 class="section-title">Comunicação, Interação e Comportamento</h5>
                    <div class="row mb-4">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Tipo de Comunicação</label>
                            <select name="communication_type" class="form-select">
                                <option value="">Selecione...</option>
                                <option value="verbal">Verbal</option>
                                <option value="non_verbal">Não Verbal</option>
                                <option value="mixed">Mista</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Nível de Interação</label>
                            <select name="interaction_level" class="form-select">
                                <option value="">Selecione...</option>
                                <option value="very_low">Muito Baixo</option>
                                <option value="low">Baixo</option>
                                <option value="moderate">Moderado</option>
                                <option value="good">Bom</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Nível de Socialização</label>
                            <select name="socialization_level" class="form-select">
                                <option value="">Selecione...</option>
                                <option value="isolated">Isolado</option>
                                <option value="selective">Seletivo</option>
                                <option value="participative">Participativo</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="checkbox-group">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="shows_aggressive_behavior" value="1" id="agressivo">
                                    <label class="form-check-label" for="agressivo">Apresenta Comportamento Agressivo</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="shows_withdrawn_behavior" value="1" id="retraido">
                                    <label class="form-check-label" for="retraido">Apresenta Comportamento Retraído</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Observações de Comportamento</label>
                            <textarea name="behavior_notes" class="form-control" rows="3" placeholder="Observações sobre comportamento..."></textarea>
                        </div>
                    </div>

                    <!-- Seção 3: Autonomia e Apoios -->
                    <h5 class="section-title">Autonomia e Apoios</h5>
                    <div class="row mb-4">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Nível de Autonomia</label>
                            <select name="autonomy_level" class="form-select">
                                <option value="">Selecione...</option>
                                <option value="dependent">Dependente</option>
                                <option value="partial">Parcial</option>
                                <option value="independent">Independente</option>
                            </select>
                        </div>
                        <div class="col-md-8 mb-3">
                            <div class="checkbox-group">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="needs_mobility_support" value="1" id="mobilidade">
                                            <label class="form-check-label" for="mobilidade">Necessita Apoio à Mobilidade</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="needs_communication_support" value="1" id="comunicacao">
                                            <label class="form-check-label" for="comunicacao">Necessita Apoio à Comunicação</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="needs_pedagogical_adaptation" value="1" id="adaptacao">
                                            <label class="form-check-label" for="adaptacao">Necessita Adaptação Pedagógica</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="uses_assistive_technology" value="1" id="tecnologia">
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
                                    <input class="form-check-input" type="checkbox" name="has_medical_report" value="1" id="laudo">
                                    <label class="form-check-label" for="laudo">Possui Laudo Médico</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="uses_medication" value="1" id="medicacao">
                                    <label class="form-check-label" for="medicacao">Usa Medicação</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Observações de Saúde</label>
                            <textarea name="medical_notes" class="form-control" rows="3" placeholder="Observações sobre saúde..."></textarea>
                        </div>
                    </div>

                    <!-- Seção 5: Avaliação Geral -->
                    <h5 class="section-title">Avaliação Geral</h5>
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Pontos Fortes</label>
                            <textarea name="strengths" class="form-control" rows="3" placeholder="Principais pontos fortes do aluno..."></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Dificuldades</label>
                            <textarea name="difficulties" class="form-control" rows="3" placeholder="Principais dificuldades do aluno..."></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Recomendações</label>
                            <textarea name="recommendations" class="form-control" rows="3" placeholder="Recomendações para o apoio educacional..."></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Observação Geral</label>
                            <textarea name="general_observation" class="form-control" rows="3" placeholder="Observação geral sobre o contexto..."></textarea>
                        </div>
                    </div>

                    <!-- Botões -->
                    <div class="d-flex justify-content-between mt-5 pt-4 border-top">
                        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary px-4">
                            <i class="bi bi-arrow-left"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary px-5 fw-bold">
                            <i class="bi bi-save"></i> Salvar Contexto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Adicionando ícones do Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>