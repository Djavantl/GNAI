@extends('layouts.app')

@section('content')

<div class="mb-5">
    <x-breadcrumb :items="[
        'Home' => route('dashboard'),
        'Alunos' => route('specialized-educational-support.students.index'),
        $student->person->name => route('specialized-educational-support.students.show', $student),
        'Contextos' => route('specialized-educational-support.student-context.index', $student),
        'Novo Contexto v' . ($studentContext->version) => null
    ]" />
</div>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
    <div>
        <h2 class="text-title">Nova Versão do Contexto</h2>
        <p class="text-muted">
            Registre uma versão atualizada (v{{ $studentContext->version }}) preservando os registros anteriores de {{ $student->person->name }}.
        </p>
    </div>

    <div class="ms-md-auto">
        <x-buttons.link-button
            href="{{ route('specialized-educational-support.student-context.index', $student) }}"
            variant="secondary">
            <i class="fas fa-times"></i> Cancelar
        </x-buttons.link-button>
    </div>
</div>

<x-forms.form-card
    action="{{ route('specialized-educational-support.student-context.store-new-version', $student) }}"
    method="POST">

    @csrf
    <input type="hidden" name="student_id" value="{{ $student->id }}">

    {{-- ================= IDENTIFICAÇÃO DO ALUNO E PERFIS DE ATENDIMENTO ================= --}}
    <x-forms.section title="Identificação do Aluno" />

    <div class="row g-2 px-4 pb-3">
        {{-- MINI PERFIL --}}
        {{-- MINI PERFIL --}}
        <div class="col-md-12">
            <div class="card p-3 border-light bg-soft-info">
                <div class="d-flex align-items-center gap-3">
                    <img src="{{ $student->person->photo_url }}"
                        class="rounded-circle shadow-sm"
                        style="width:60px;height:60px;object-fit:cover;">

                    <div>
                        <strong class="d-block">{{ $student->person->name }}</strong>
                        <span class="small text-muted d-block">
                            Matrícula: {{ $student->registration ?? '—' }}
                        </span>
                        <span class="small">
                            <span class="text-{{ $student->status->color() }} text-uppercase fw-bold">
                                {{ $student->status->label() ?? '—' }}
                            </span>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- PERFIS DE ATENDIMENTO --}}
        <div class="col-md-12 border-top pt-4">
            <div class="row g-2">
                @forelse($student->deficiencies as $def)
                    <div class="col-md-6">
                        <div class="card p-3 border-light bg-soft-info">
                            <strong class="d-block">{{ $def->name ?? '—' }}</strong>
                            <span class="small text-muted">GRAU: {{ $def->pivot->severity ?? '—' }}</span>
                        </div>
                    </div>
                @empty
                    <div class="col-md-12">
                        <div class="card p-3 border-light bg-soft-info text-muted">
                            Nenhum perfil de atendimento registrado para este aluno.
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ================= HISTÓRICO EDUCACIONAL ================= --}}
    <x-forms.section title="Histórico e Necessidades" />

    <div class="row px-4 pb-3">
        <div class="col-md-12 mb-3">
            <x-forms.textarea
                name="history"
                label="Histórico do Aluno "
                rows="3"
                required
                placeholder="Descreva o histórico educacional"
                :value="old('history', $studentContext->history)"
            />
        </div>

        <div class="col-md-12">
            <x-forms.textarea
                name="specific_educational_needs"
                label="Necessidades Educacionais Específicas "
                rows="3"
                required
                placeholder="Descreva as necessidades educacionais específicas"
                :value="old('specific_educational_needs', $studentContext->specific_educational_needs)"
            />
        </div>
    </div>

    {{-- ================= SÍNTESE ================= --}}
    <x-forms.section title="Síntese Avaliativa" />

    <div class="row g-2 px-4 pb-3">
        <div class="col-md-12">
            <x-forms.textarea name="knowledge" label="Conhecimentos e Interesses" rows="4"
                :value="old('knowledge', $studentContext->knowledge)" aria-label="O que sabe? Do que gosta/afinidades?" required />
        </div>

        <div class="col-md-12">
            <x-forms.textarea name="difficulties" label="Dificuldades" rows="4"
                :value="old('difficulties', $studentContext->difficulties)" aria-label="Dificuldades" required />
        </div>

    </div>

    {{-- ================= APRENDIZAGEM ================= --}}
    <x-forms.section title="Aprendizagem e Cognição" />

    <div class="row g-2 px-4 pb-3">
        <div class="col-md-3">
            <x-forms.select name="learning_level" label="Nível de Aprendizagem"
                :options="$studentContextOptions['learningLevels']"
                :selected="old('learning_level', $studentContext->learning_level)"
                aria-label="Selecionar nível de aprendizagem" />
        </div>

        <div class="col-md-3">
            <x-forms.select name="attention_level" label="Nível de Atenção"
                :options="$studentContextOptions['attentionLevels']"
                :selected="old('attention_level', $studentContext->attention_level)"
                aria-label="Selecionar nível de atenção" />
        </div>

        <div class="col-md-3">
            <x-forms.select name="memory_level" label="Nível de Memória"
                :options="$studentContextOptions['memoryLevels']"
                :selected="old('memory_level', $studentContext->memory_level)"
                aria-label="Selecionar nível de memória" />
        </div>

        <div class="col-md-3">
            <x-forms.select name="reasoning_level" label="Nível de Raciocínio"
                :options="$studentContextOptions['reasoningLevels']"
                :selected="old('reasoning_level', $studentContext->reasoning_level)"
                aria-label="Selecionar nível de raciocínio" />
        </div>

        <div class="col-md-12">
            <x-forms.textarea name="learning_observations" label="Observações de Aprendizagem" rows="3"
                :value="old('learning_observations', $studentContext->learning_observations)" />
        </div>
    </div>

    {{-- ================= COMPORTAMENTO ================= --}}
    <x-forms.section title="Comunicação e Comportamento" />

    <div class="row g-2 px-4 pb-3">
        <div class="col-md-4">
            <x-forms.select name="communication_type" label="Tipo de Comunicação"
                :options="$studentContextOptions['communicationTypes']"
                :selected="old('communication_type', $studentContext->communication_type)"
                aria-label="Selecionar tipo de comunicação" />
        </div>

        <div class="col-md-4">
            <x-forms.select name="interaction_level" label="Nível de Interação"
                :options="$studentContextOptions['interactionLevels']"
                :selected="old('interaction_level', $studentContext->interaction_level)"
                aria-label="Selecionar nível de interação" />
        </div>

        <div class="col-md-4">
            <x-forms.select name="socialization_level" label="Nível de Socialização"
                :options="$studentContextOptions['socializationLevels']"
                :selected="old('socialization_level', $studentContext->socialization_level)"
                aria-label="Selecionar nível de socialização" />
        </div>

        <div class="col-md-6">
            <input type="hidden" name="shows_aggressive_behavior" value="0">
            <x-forms.checkbox
                name="shows_aggressive_behavior"
                label="Comportamento agressivo"
                description="Marque se o aluno apresenta comportamento agressivo."
                :checked="filter_var(old('shows_aggressive_behavior', $studentContext->shows_aggressive_behavior), FILTER_VALIDATE_BOOLEAN)"
                class="p-3 border rounded bg-light"
            />
        </div>

        <div class="col-md-6">
            <input type="hidden" name="shows_withdrawn_behavior" value="0">
            <x-forms.checkbox
                name="shows_withdrawn_behavior"
                label="Comportamento retraído"
                description="Marque se o aluno apresenta comportamento retraído."
                :checked="filter_var(old('shows_withdrawn_behavior', $studentContext->shows_withdrawn_behavior), FILTER_VALIDATE_BOOLEAN)"
                class="p-3 border rounded bg-light"
            />
        </div>

        <div class="col-md-12">
            <x-forms.textarea name="behavior_notes" label="Notas Comportamentais" rows="3"
                :value="old('behavior_notes', $studentContext->behavior_notes)" />
        </div>
    </div>

    {{-- ================= AUTONOMIA ================= --}}
    <x-forms.section title="Autonomia e Apoios" />

    <div class="row g-2 px-4 pb-3">
        <div class="col-md-12">
            <x-forms.select name="autonomy_level" label="Nível de Autonomia"
                :options="$studentContextOptions['autonomyLevels']"
                :selected="old('autonomy_level', $studentContext->autonomy_level)"
                aria-label="Selecionar nível de autonomia" />
        </div>

        <div class="col-md-6">
            <x-forms.textarea
                name="needs_mobility_support"
                label="Apoio de Mobilidade"
                rows="3"
                placeholder="Descreva os apoios de mobilidade necessários"
                :value="old('needs_mobility_support', $studentContext->needs_mobility_support)"
            />
        </div>

        <div class="col-md-6">
            <x-forms.textarea
                name="needs_communication_support"
                label="Apoio de Comunicação"
                rows="3"
                placeholder="Descreva os apoios de comunicação necessários"
                :value="old('needs_communication_support', $studentContext->needs_communication_support)"
            />
        </div>

        <div class="col-md-6">
            <x-forms.textarea
                name="needs_pedagogical_adaptation"
                label="Adaptação Pedagógica"
                rows="3"
                placeholder="Descreva as adaptações pedagógicas necessárias"
                :value="old('needs_pedagogical_adaptation', $studentContext->needs_pedagogical_adaptation)"
            />
        </div>

        <div class="col-md-6">
            <x-forms.textarea
                name="uses_assistive_technology"
                label="Tecnologia Assistiva"
                rows="3"
                placeholder="Descreva as tecnologias assistivas utilizadas"
                :value="old('uses_assistive_technology', $studentContext->uses_assistive_technology)"
            />
        </div>
    </div>

    {{-- ================= SAÚDE ================= --}}
    <x-forms.section title="Saúde" />

    <div class="row g-2 px-4 pb-3">
        <div class="col-md-6">
            <input type="hidden" name="has_medical_report" value="0">
            <x-forms.checkbox
                name="has_medical_report"
                label="Possui laudo médico"
                description="Marque se o aluno possui laudo médico."
                :checked="filter_var(old('has_medical_report', $studentContext->has_medical_report), FILTER_VALIDATE_BOOLEAN)"
                class="p-3 border rounded bg-light"
            />
        </div>

        <div class="col-md-6">
            <input type="hidden" name="uses_medication" value="0">
            <x-forms.checkbox
                name="uses_medication"
                label="Usa medicação"
                description="Marque se o aluno faz uso de medicação."
                :checked="filter_var(old('uses_medication', $studentContext->uses_medication), FILTER_VALIDATE_BOOLEAN)"
                class="p-3 border rounded bg-light"
            />
        </div>

        <div class="col-md-12">
            <x-forms.textarea name="medical_notes" label="Observações Médicas" rows="3"
                :value="old('medical_notes', $studentContext->medical_notes)" />
        </div>
    </div>

    

    {{-- ================= AÇÕES ================= --}}
    <div class="col-12 d-flex flex-wrap justify-content-end gap-2 border-top pt-4 px-4 pb-4">
        <x-buttons.link-button
            href="{{ route('specialized-educational-support.student-context.index', $student) }}"
            variant="secondary">
            <i class="fas fa-times"></i> Cancelar
        </x-buttons.link-button>

        <x-buttons.submit-button type="submit" class="btn-action new submit">
            <i class="fas fa-save"></i> Salvar
        </x-buttons.submit-button>
    </div>

</x-forms.form-card>

@endsection
