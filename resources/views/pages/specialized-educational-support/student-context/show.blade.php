@extends('layouts.app')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Alunos' => route('specialized-educational-support.students.index'),
            $student->person->name => route('specialized-educational-support.students.show', $student),
            'Contextos' => route('specialized-educational-support.student-context.index', $student),
            'Contexto #' . $context->id => null
        ]" />
    </div>
    {{-- Cabeçalho da Página --}}
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <div>
            <h2 class="text-title">Contexto Educacional</h2>
            <p class="text-muted">
                Aluno: {{ $student->person->name }} 
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('specialized-educational-support.student-context.pdf', $context->id) }}" target="_blank" class="btn-action primary" style="text-decoration: none; display: inline-flex; align-items: center; gap: 5px;">
                <i class="fas fa-file-pdf"></i> Gerar PDF
            </a>
            
            @if(isset($context))
                <x-buttons.link-button :href="route('specialized-educational-support.student-context.edit', $context->id)" variant="warning">
                    <i class="fas fa-edit"></i> Editar
                </x-buttons.link-button>
            @endif

            <x-buttons.link-button :href="route('specialized-educational-support.student-context.index', $student->id)" variant="secondary">
                Voltar
            </x-buttons.link-button>
        </div>
    </div>

    <div class="custom-table-card bg-white shadow-sm">
        <div class="row g-0">
            
            {{-- SEÇÃO: RESUMO E IDENTIFICAÇÃO --}}
            <x-forms.section title="Resumo do Contexto" />
            
            <x-show.info-item label="Tipo de Avaliação" column="col-md-4" isBox="true">
                @php
                    $evaluationTypes = [
                        'initial' => 'Avaliação Inicial',
                        'periodic_review' => 'Revisão Periódica',
                        'pei_review' => 'Revisão PEI',
                        'specific_demand' => 'Demanda Específica'
                    ];
                @endphp
                {{ $evaluationTypes[$context->evaluation_type] ?? $context->evaluation_type }}
            </x-show.info-item>

            <x-show.info-item label="Status do Registro" column="col-md-4" isBox="true">
                @if($context->is_current)
                    <span class="text-success fw-bold"><i class="fas fa-check-circle"></i> CONTEXTO ATUAL</span>
                @else
                    <span class="text-muted">HISTÓRICO</span>
                @endif
            </x-show.info-item>

            <x-show.info-item label="Última Atualização" column="col-md-4" isBox="true">
                {{ $context->updated_at->format('d/m/Y H:i') }}
            </x-show.info-item>

            <x-show.info-item label="Nível de Aprendizagem" column="col-md-3" isBox="true">
                @php
                    $learningLevels = ['very_low' => 'Muito Baixo', 'low' => 'Baixo', 'adequate' => 'Adequado', 'good' => 'Bom', 'excellent' => 'Excelente'];
                @endphp
                {{ $learningLevels[$context->learning_level] ?? 'Não informado' }}
            </x-show.info-item>

            <x-show.info-item label="Comunicação" column="col-md-3" isBox="true">
                @php
                    $communicationTypes = ['verbal' => 'Verbal', 'non_verbal' => 'Não Verbal', 'mixed' => 'Mista'];
                @endphp
                {{ $communicationTypes[$context->communication_type] ?? 'Não informado' }}
            </x-show.info-item>

            <x-show.info-item label="Autonomia" column="col-md-3" isBox="true">
                @php
                    $autonomyLevels = ['dependent' => 'Dependente', 'partial' => 'Parcial', 'independent' => 'Independente'];
                @endphp
                {{ $autonomyLevels[$context->autonomy_level] ?? 'Não informado' }}
            </x-show.info-item>

            <x-show.info-item label="Saúde / Laudo" column="col-md-3" isBox="true">
                {!! $context->has_medical_report ? '<span class="text-success">Com Laudo</span>' : 'Sem Laudo' !!}
                {!! $context->uses_medication ? ' | <span class="text-warning">Usa Medicação</span>' : '' !!}
            </x-show.info-item>

            {{-- SEÇÃO: APRENDIZAGEM E COGNIÇÃO --}}
            <x-forms.section title="Aprendizagem e Cognição" />

            <x-show.info-item label="Nível de Atenção" column="col-md-4" isBox="true">
                {{ ['very_low' => 'Muito Baixo', 'low' => 'Baixo', 'moderate' => 'Moderado', 'high' => 'Alto'][$context->attention_level] ?? '---' }}
            </x-show.info-item>

            <x-show.info-item label="Nível de Memória" column="col-md-4" isBox="true">
                {{ ['low' => 'Baixo', 'moderate' => 'Moderado', 'good' => 'Bom'][$context->memory_level] ?? '---' }}
            </x-show.info-item>

            <x-show.info-item label="Tipo de Raciocínio" column="col-md-4" isBox="true">
                {{ ['concrete' => 'Concreto', 'mixed' => 'Misto', 'abstract' => 'Abstrato'][$context->reasoning_level] ?? '---' }}
            </x-show.info-item>

            <x-show.info-textarea label="Observações de Aprendizagem" column="col-md-12" isBox="true">{{ $context->learning_observations ?? 'Nenhuma observação registrada.' }}</x-show.info-textarea>

            {{-- SEÇÃO: COMPORTAMENTO E SOCIALIZAÇÃO --}}
            <x-forms.section title="Comunicação, Interação e Comportamento" />

            <x-show.info-item label="Nível de Interação" column="col-md-4" isBox="true">
                {{ ['very_low' => 'Muito Baixo', 'low' => 'Baixo', 'moderate' => 'Moderado', 'good' => 'Bom'][$context->interaction_level] ?? '---' }}
            </x-show.info-item>

            <x-show.info-item label="Nível de Socialização" column="col-md-4" isBox="true">
                {{ ['isolated' => 'Isolado', 'selective' => 'Seletivo', 'participative' => 'Participativo'][$context->socialization_level] ?? '---' }}
            </x-show.info-item>

            <x-show.info-item label="Alertas de Comportamento" column="col-md-4" isBox="true">
                @if($context->shows_aggressive_behavior) <span class="badge bg-danger me-1">Agressividade</span> @endif
                @if($context->shows_withdrawn_behavior) <span class="badge bg-warning text-dark me-1">Retraimento</span> @endif
                @if(!$context->shows_aggressive_behavior && !$context->shows_withdrawn_behavior) <span class="text-success">Sem intercorrências</span> @endif
            </x-show.info-item>

            <x-show.info-textarea label="Observações de Comportamento" column="col-md-12" isBox="true">{{ $context->behavior_notes ?? 'Sem notas de comportamento.' }}</x-show.info-textarea>

            {{-- SEÇÃO: APOIOS --}}
            <x-forms.section title="Autonomia e Apoios" />

            <x-show.info-item label="Recursos e Apoios Necessários" column="col-md-12" isBox="true">
                <div class="d-flex flex-wrap gap-2">
                    @if($context->needs_mobility_support) <span class="badge bg-primary px-3">Apoio Mobilidade</span> @endif
                    @if($context->needs_communication_support) <span class="badge bg-info px-3">Apoio Comunicação</span> @endif
                    @if($context->needs_pedagogical_adaptation) <span class="badge bg-warning text-dark px-3">Adaptação Pedagógica</span> @endif
                    @if($context->uses_assistive_technology) <span class="badge bg-success px-3">Tecnologia Assistiva</span> @endif
                    @if(!$context->needs_mobility_support && !$context->needs_communication_support && !$context->needs_pedagogical_adaptation && !$context->uses_assistive_technology)
                        <span class="text-muted">Nenhum apoio específico registrado.</span>
                    @endif
                </div>
            </x-show.info-item>

            {{-- SEÇÃO: SAÚDE --}}
            <x-forms.section title="Saúde" />

            <x-show.info-item label="Possui Laudo Médico" column="col-md-6" isBox="true">
                @if($context->has_medical_report)
                    <span class="text-success fw-bold"><i class="fas fa-check-circle me-1"></i> SIM</span>
                @else
                    <span class="text-muted"><i class="fas fa-times-circle me-1"></i> NÃO</span>
                @endif
            </x-show.info-item>

            <x-show.info-item label="Usa Medicação" column="col-md-6" isBox="true">
                @if($context->uses_medication)
                    <span class="text-warning fw-bold"><i class="fas fa-pills me-1"></i> SIM</span>
                @else
                    <span class="text-muted"><i class="fas fa-times-circle me-1"></i> NÃO</span>
                @endif
            </x-show.info-item>

            <x-show.info-textarea label="Observações de Saúde" column="col-md-12" isBox="true">
                {{ $context->medical_notes ?? 'Nenhuma observação médica registrada.' }}
            </x-show.info-textarea>

            {{-- SEÇÃO: TEXTOS LONGOS --}}
            <x-forms.section title="Histórico e Necessidades" />

            <x-show.info-textarea label="Histórico" column="col-md-12" isBox="true">{{ $context->history }}</x-show.info-textarea>

            <x-show.info-textarea label="Necessidades Educacionais Específicas" column="col-md-12" isBox="true">{{ $context->specific_educational_needs }}</x-show.info-textarea>

            {{-- SEÇÃO: AVALIAÇÃO --}}
            <x-forms.section title="Avaliação Geral" />

            <x-show.info-textarea label="Pontos Fortes / Potencialidades" column="col-md-6" isBox="true">{{ $context->strengths ?? 'Não informado' }}</x-show.info-textarea>

            <x-show.info-textarea label="Dificuldades" column="col-md-6" isBox="true">{{ $context->difficulties ?? 'Não informado' }}</x-show.info-textarea>

            <x-show.info-textarea label="Recomendações" column="col-md-6" isBox="true">{{ $context->recommendations ?? 'Não informado' }}</x-show.info-textarea>

            <x-show.info-textarea label="Observação Geral" column="col-md-6" isBox="true">{{ $context->general_observation ?? 'Sem observações adicionais.' }}</x-show.info-textarea>

            {{-- Rodapé / Ações Finais --}}
            <div class="col-12 border-top p-4 d-flex justify-content-between align-items-center bg-light no-print">
                <small class="text-muted italic">
                    Criado em: {{ $context->created_at->format('d/m/Y H:i') }}
                </small>
                <div class="d-flex gap-3">
                    <form action="{{ route('specialized-educational-support.student-context.destroy', $context->id) }}" method="POST" onsubmit="return confirm('Excluir permanentemente?')">
                        @csrf 
                        @method('DELETE')
                        <x-buttons.submit-button variant="danger">
                            <i class="fas fa-trash"></i> Excluir Contexto
                        </x-buttons.submit-button>
                    </form>

                    <x-buttons.link-button :href="route('specialized-educational-support.student-context.edit', $context->id)" variant="warning">
                        <i class="fas fa-edit"></i> Editar Contexto
                    </x-buttons.link-button>
                </div>
            </div>
        </div>
    </div>
@endsection