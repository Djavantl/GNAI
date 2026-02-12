@extends('layouts.app')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Alunos' => route('specialized-educational-support.students.index'),
            $student->person->name => route('specialized-educational-support.students.show', $student),
            'Contextos' => route('specialized-educational-support.student-context.index', $student),
            'Contexto #' . $student_context->id => route('specialized-educational-support.student-context.show', $student_context),
            'Editar' => null
        ]" />
    </div>
    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Editar Contexto: {{ $student_context->student->name ?? 'Aluno' }}</h2>
            <p class="text-muted">Atualize as informações do contexto educacional e funcional.</p>
        </div>
    </div>

    <div class="mt-3">
        <x-forms.form-card action="{{ route('specialized-educational-support.student-context.update', $student_context->id) }}" method="POST">
            @method('PUT')

            <x-forms.section title="Tipo de Avaliação" />

            <div class="col-md-6">
                <x-forms.select
                    name="evaluation_type"
                    label="Tipo de Avaliação *"
                    required
                    :options="[
                        'initial' => 'Avaliação Inicial',
                        'periodic_review' => 'Revisão Periódica',
                        'pei_review' => 'Revisão PEI',
                        'specific_demand' => 'Demanda Específica'
                    ]"
                    :value="old('evaluation_type', $student_context->evaluation_type)"
                />
            </div>

            <div class="col-md-6 mt-4">
                <x-forms.checkbox 
                    name="is_current" 
                    label="Contexto atual" 
                    :checked="old('is_current', $student_context->is_current)" 
                />
            </div>

            <x-forms.section title="Aprendizagem e Cognição" />

            <div class="col-md-6">
                <x-forms.select
                    name="learning_level"
                    label="Nível de Aprendizagem"
                    :options="['very_low' => 'Muito Baixo', 'low' => 'Baixo', 'adequate' => 'Adequado', 'good' => 'Bom', 'excellent' => 'Excelente']"
                    :value="old('learning_level', $student_context->learning_level)"
                />
            </div>

            <div class="col-md-6">
                <x-forms.select
                    name="attention_level"
                    label="Nível de Atenção"
                    :options="['very_low' => 'Muito Baixo', 'low' => 'Baixo', 'moderate' => 'Moderado', 'high' => 'Alto']"
                    :value="old('attention_level', $student_context->attention_level)"
                />
            </div>

            <div class="col-md-6">
                <x-forms.select
                    name="memory_level"
                    label="Nível de Memória"
                    :options="['low' => 'Baixo', 'moderate' => 'Moderado', 'good' => 'Bom']"
                    :value="old('memory_level', $student_context->memory_level)"
                />
            </div>

            <div class="col-md-6">
                <x-forms.select
                    name="reasoning_level"
                    label="Raciocínio"
                    :options="['concrete' => 'Concreto', 'mixed' => 'Misto', 'abstract' => 'Abstrato']"
                    :value="old('reasoning_level', $student_context->reasoning_level)"
                />
            </div>

            <div class="col-md-12">
                <x-forms.textarea name="learning_observations" label="Observações de Aprendizagem" rows="3" :value="old('learning_observations', $student_context->learning_observations)" />
            </div>

            <x-forms.section title="Comunicação, Interação e Comportamento" />

            <div class="col-md-6">
                <x-forms.select
                    name="communication_type"
                    label="Tipo de Comunicação"
                    :options="['verbal' => 'Verbal', 'non_verbal' => 'Não Verbal', 'mixed' => 'Mista']"
                    :value="old('communication_type', $student_context->communication_type)"
                />
            </div>

            <div class="col-md-6">
                <x-forms.select
                    name="interaction_level"
                    label="Nível de Interação"
                    :options="['very_low' => 'Muito Baixo', 'low' => 'Baixo', 'moderate' => 'Moderado', 'good' => 'Bom']"
                    :value="old('interaction_level', $student_context->interaction_level)"
                />
            </div>

            <div class="col-md-6">
                <x-forms.select
                    name="socialization_level"
                    label="Nível de Socialização"
                    :options="['isolated' => 'Isolado', 'selective' => 'Seletivo', 'participative' => 'Participativo']"
                    :value="old('socialization_level', $student_context->socialization_level)"
                />
            </div>

            <div class="col-md-6 mt-4">
                <div class="d-flex gap-4">
                    <x-forms.checkbox name="shows_aggressive_behavior" label="Agressivo" :checked="old('shows_aggressive_behavior', $student_context->shows_aggressive_behavior)" />
                    <x-forms.checkbox name="shows_withdrawn_behavior" label="Retraído" :checked="old('shows_withdrawn_behavior', $student_context->shows_withdrawn_behavior)" />
                </div>
            </div>

            <div class="col-md-12">
                <x-forms.textarea name="behavior_notes" label="Observações de Comportamento" rows="3" :value="old('behavior_notes', $student_context->behavior_notes)" />
            </div>

            <x-forms.section title="Autonomia e Apoios" />

            <div class="col-md-6">
                <x-forms.select
                    name="autonomy_level"
                    label="Nível de Autonomia"
                    :options="['dependent' => 'Dependente', 'partial' => 'Parcial', 'independent' => 'Independente']"
                    :value="old('autonomy_level', $student_context->autonomy_level)"
                    :selected="old('autonomy_level', $student_context->autonomy_level)"
                />
            </div>

            <div class="col-md-6 mt-4">
                <div class="row">
                    <div class="col-6"><x-forms.checkbox name="needs_mobility_support" label="Apoio Mobilidade" :checked="old('needs_mobility_support', $student_context->needs_mobility_support)" /></div>
                    <div class="col-6"><x-forms.checkbox name="needs_communication_support" label="Apoio Comunicação" :checked="old('needs_communication_support', $student_context->needs_communication_support)" /></div>
                </div>
            </div>

            <div class="col-md-12 mt-2">
                <div class="row">
                    <div class="col-6"><x-forms.checkbox name="needs_pedagogical_adaptation" label="Adaptação Pedagógica" :checked="old('needs_pedagogical_adaptation', $student_context->needs_pedagogical_adaptation)" /></div>
                    <div class="col-6"><x-forms.checkbox name="uses_assistive_technology" label="Usa Tecnologia Assistiva" :checked="old('uses_assistive_technology', $student_context->uses_assistive_technology)" /></div>
                </div>
            </div>

            <x-forms.section title="Saúde" />

            <div class="col-md-6">
                <x-forms.checkbox name="has_medical_report" label="Possui Laudo Médico" :checked="old('has_medical_report', $student_context->has_medical_report)" />
            </div>

            <div class="col-md-6">
                <x-forms.checkbox name="uses_medication" label="Usa Medicação" :checked="old('uses_medication', $student_context->uses_medication)" />
            </div>

            <div class="col-md-12">
                <x-forms.textarea name="medical_notes" label="Observações de Saúde" rows="3" :value="old('medical_notes', $student_context->medical_notes)" />
            </div>

            <x-forms.section title="Necessidades Educacionais" />

            <div class="col-md-12">
                <x-forms.textarea name="history" label="Histórico" rows="4" required :value="old('history', $student_context->history)" />
            </div>

            <div class="col-md-12">
                <x-forms.textarea name="specific_educational_needs" label="Necessidades Educacionais Específicas " rows="4" required :value="old('specific_educational_needs', $student_context->specific_educational_needs)" />
            </div>

            <x-forms.section title="Avaliação Geral" />

            <div class="col-md-6">
                <x-forms.textarea name="strengths" label="Pontos Fortes" rows="3" :value="old('strengths', $student_context->strengths)" />
            </div>

            <div class="col-md-6">
                <x-forms.textarea name="difficulties" label="Dificuldades" rows="3" :value="old('difficulties', $student_context->difficulties)" />
            </div>

            <div class="col-md-6">
                <x-forms.textarea name="recommendations" label="Recomendações" rows="3" :value="old('recommendations', $student_context->recommendations)" />
            </div>

            <div class="col-md-6">
                <x-forms.textarea name="general_observation" label="Observação Geral" rows="3" :value="old('general_observation', $student_context->general_observation)" />
            </div>

            <div class="col-12 d-flex justify-content-between border-t pt-4 px-4 pb-4">
                <div>
                    <x-buttons.link-button href="{{ route('specialized-educational-support.student-context.index', $student_context->student_id) }}" variant="secondary">
                        Voltar
                    </x-buttons.link-button>
                </div>

                <div class="d-flex gap-3">
                    <x-buttons.submit-button type="submit" class="btn-action new submit px-5">
                        <i class="fas fa-sync mr-2"></i> Atualizar Contexto
                    </x-buttons.submit-button>
                </div>
            </div>
        </x-forms.form-card>
        
        {{-- Formulário de exclusão separado para manter a integridade do layout --}}
        <div class="mt-3 px-4 pb-4">
            <form action="{{ route('specialized-educational-support.student-context.destroy', $student_context->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir permanentemente este contexto?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger btn-sm">
                    <i class="fas fa-trash mr-1"></i> Excluir Contexto
                </button>
            </form>
        </div>
    </div>
@endsection