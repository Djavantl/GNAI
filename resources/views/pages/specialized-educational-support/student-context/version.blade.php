@extends('layouts.app')

@section('content')

<div class="mb-5">
    <x-breadcrumb :items="[
        'Home' => route('dashboard'),
        'Alunos' => route('specialized-educational-support.students.index'),
        $student->person->name => route('specialized-educational-support.students.show', $student),
        'Contextos' => route('specialized-educational-support.student-context.index', $student),
        'Novo Contexto v' . ($studentContext->version + 1) => null
    ]" />
</div>

<div class="d-flex justify-content-between mb-3 align-items-center">
    <div>
        <h2 class="text-title">Nova Versão do Contexto</h2>
        <p class="text-muted">
            Registre uma versão atualizada (v{{ $studentContext->version + 1 }}) preservando os registros anteriores de {{ $student->person->name }}.
        </p>
    </div>

    <x-buttons.link-button
        href="{{ route('specialized-educational-support.student-context.index', $student) }}"
        variant="secondary">
        <i class="fas fa-times"></i> Cancelar
    </x-buttons.link-button>
</div>

<x-forms.form-card
    action="{{ route('specialized-educational-support.student-context.store-new-version', $student) }}"
    method="POST">

    @csrf
    <input type="hidden" name="student_id" value="{{ $student->id }}">

    {{-- ================= IDENTIFICAÇÃO DO ALUNO E DEFICIÊNCIAS ================= --}}
    <x-forms.section title="Identificação do Aluno" />

    <div class="row g-2 px-4 pb-3">
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
                            Matrícula: {{ $student->registration ?? '—' }} | Status: 
                            @if($student->status === 'active')
                                <span class="text-success fw-semibold">ATIVO</span>
                            @else
                                <span class="text-danger fw-semibold">{{ strtoupper($student->status) }}</span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- DEFICIÊNCIAS --}}
        <div class="col-md-12 border-top pt-4">
            <div class="row g-2">
                @forelse($student->deficiencies as $def)
                    <div class="col-md-6">
                        <div class="card p-3 border-light bg-soft-info">
                            <strong class="d-block">{{ $def->deficiency->name ?? '—' }}</strong>
                            <span class="small text-muted">GRAU: {{ $def->severity ?? '—' }}</span>
                        </div>
                    </div>
                @empty
                    <div class="col-md-12">
                        <div class="card p-3 border-light bg-soft-info text-muted">
                            Nenhuma deficiência registrada para este aluno.
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
                label="Histórico do Aluno *"
                rows="3"
                required
                placeholder="Descreva o histórico educacional"
                :value="old('history', $studentContext->history)"
            />
        </div>

        <div class="col-md-12">
            <x-forms.textarea
                name="specific_educational_needs"
                label="Necessidades Educacionais Específicas *"
                rows="3"
                required
                placeholder="Descreva as necessidades educacionais específicas"
                :value="old('specific_educational_needs', $studentContext->specific_educational_needs)"
            />
        </div>
    </div>

    {{-- ================= APRENDIZAGEM ================= --}}
    <x-forms.section title="Aprendizagem e Cognição" />

    <div class="row g-2 px-4 pb-3">
        <div class="col-md-3">
            <x-forms.select name="learning_level" label="Nível de Aprendizagem"
                :options="['very_low'=>'Muito Baixo', 'low'=>'Baixo', 'adequate'=>'Adequado', 'good'=>'Bom', 'excellent'=>'Excelente']"
                :selected="old('learning_level', $studentContext->learning_level)"
                aria-label="Selecionar nível de aprendizagem" />
        </div>

        <div class="col-md-3">
            <x-forms.select name="attention_level" label="Nível de Atenção"
                :options="['very_low'=>'Muito Baixo', 'low'=>'Baixo', 'moderate'=>'Moderado', 'high'=>'Alto']"
                :selected="old('attention_level', $studentContext->attention_level)"
                aria-label="Selecionar nível de atenção" />
        </div>

        <div class="col-md-3">
            <x-forms.select name="memory_level" label="Nível de Memória"
                :options="['low'=>'Baixa', 'moderate'=>'Moderada', 'good'=>'Boa']"
                :selected="old('memory_level', $studentContext->memory_level)"
                aria-label="Selecionar nível de memória" />
        </div>

        <div class="col-md-3">
            <x-forms.select name="reasoning_level" label="Nível de Raciocínio"
                :options="['concrete'=>'Concreto', 'mixed'=>'Misto', 'abstract'=>'Abstrato']"
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
                :options="['verbal'=>'Verbal', 'non_verbal'=>'Não verbal', 'mixed'=>'Mista']"
                :selected="old('communication_type', $studentContext->communication_type)"
                aria-label="Selecionar tipo de comunicação" />
        </div>

        <div class="col-md-4">
            <x-forms.select name="interaction_level" label="Nível de Interação"
                :options="['very_low'=>'Muito Baixo', 'low'=>'Baixo', 'moderate'=>'Moderado', 'good'=>'Bom']"
                :selected="old('interaction_level', $studentContext->interaction_level)"
                aria-label="Selecionar nível de interação" />
        </div>

        <div class="col-md-4">
            <x-forms.select name="socialization_level" label="Nível de Socialização"
                :options="['isolated'=>'Isolado', 'selective'=>'Seletivo', 'participative'=>'Participativo']"
                :selected="old('socialization_level', $studentContext->socialization_level)"
                aria-label="Selecionar nível de socialização" />
        </div>

        <div class="col-md-6">
            <x-forms.select name="shows_aggressive_behavior" label="Comportamento Agressivo"
                :options="[1=>'Sim', 0=>'Não']"
                :selected="old('shows_aggressive_behavior', $studentContext->shows_aggressive_behavior)"
                aria-label="Apresenta comportamento agressivo" />
        </div>

        <div class="col-md-6">
            <x-forms.select name="shows_withdrawn_behavior" label="Comportamento Retraído"
                :options="[1=>'Sim', 0=>'Não']"
                :selected="old('shows_withdrawn_behavior', $studentContext->shows_withdrawn_behavior)"
                aria-label="Apresenta comportamento retraído" />
        </div>

        <div class="col-md-12">
            <x-forms.textarea name="behavior_notes" label="Notas Comportamentais" rows="3"
                :value="old('behavior_notes', $studentContext->behavior_notes)" />
        </div>
    </div>

    {{-- ================= AUTONOMIA ================= --}}
    <x-forms.section title="Autonomia e Apoios" />

    <div class="row g-2 px-4 pb-3">
        <div class="col-md-4">
            <x-forms.select name="autonomy_level" label="Nível de Autonomia"
                :options="['dependent'=>'Dependente', 'partial'=>'Parcial', 'independent'=>'Independente']"
                :selected="old('autonomy_level', $studentContext->autonomy_level)"
                aria-label="Selecionar nível de autonomia" />
        </div>

        <div class="col-md-4">
            <x-forms.select name="needs_mobility_support" label="Apoio de Mobilidade"
                :options="[1=>'Sim', 0=>'Não']"
                :selected="old('needs_mobility_support', $studentContext->needs_mobility_support)"
                aria-label="Necessita apoio de mobilidade" />
        </div>

        <div class="col-md-4">
            <x-forms.select name="needs_communication_support" label="Apoio de Comunicação"
                :options="[1=>'Sim', 0=>'Não']"
                :selected="old('needs_communication_support', $studentContext->needs_communication_support)"
                aria-label="Necessita apoio de comunicação" />
        </div>

        <div class="col-md-6">
            <x-forms.select name="needs_pedagogical_adaptation" label="Adaptação Pedagógica"
                :options="[1=>'Sim', 0=>'Não']"
                :selected="old('needs_pedagogical_adaptation', $studentContext->needs_pedagogical_adaptation)"
                aria-label="Necessita adaptação pedagógica" />
        </div>

        <div class="col-md-6">
            <x-forms.select name="uses_assistive_technology" label="Tecnologia Assistiva"
                :options="[1=>'Sim', 0=>'Não']"
                :selected="old('uses_assistive_technology', $studentContext->uses_assistive_technology)"
                aria-label="Utiliza tecnologia assistiva" />
        </div>
    </div>

    {{-- ================= SAÚDE ================= --}}
    <x-forms.section title="Saúde" />

    <div class="row g-2 px-4 pb-3">
        <div class="col-md-6">
            <x-forms.select name="has_medical_report" label="Possui Laudo Médico"
                :options="[1=>'Sim', 0=>'Não']"
                :selected="old('has_medical_report', $studentContext->has_medical_report)"
                aria-label="Possui laudo médico" />
        </div>

        <div class="col-md-6">
            <x-forms.select name="uses_medication" label="Usa Medicação"
                :options="[1=>'Sim', 0=>'Não']"
                :selected="old('uses_medication', $studentContext->uses_medication)"
                aria-label="Faz uso de medicação" />
        </div>

        <div class="col-md-12">
            <x-forms.textarea name="medical_notes" label="Observações Médicas" rows="3"
                :value="old('medical_notes', $studentContext->medical_notes)" />
        </div>
    </div>

    {{-- ================= SÍNTESE ================= --}}
    <x-forms.section title="Síntese Avaliativa" />

    <div class="row g-2 px-4 pb-3">
        <div class="col-md-6">
            <x-forms.textarea name="strengths" label="Pontos Fortes" rows="4"
                :value="old('strengths', $studentContext->strengths)" aria-label="Pontos fortes" />
        </div>

        <div class="col-md-6">
            <x-forms.textarea name="difficulties" label="Dificuldades" rows="4"
                :value="old('difficulties', $studentContext->difficulties)" aria-label="Dificuldades" />
        </div>

        <div class="col-md-6">
            <x-forms.textarea name="recommendations" label="Recomendações" rows="4"
                :value="old('recommendations', $studentContext->recommendations)" aria-label="Recomendações" />
        </div>

        <div class="col-md-6">
            <x-forms.textarea name="general_observation" label="Observação Geral" rows="4"
                :value="old('general_observation', $studentContext->general_observation)" aria-label="Observação geral" />
        </div>
    </div>

    {{-- ================= AÇÕES ================= --}}
    <div class="col-12 d-flex justify-content-end gap-3 border-top pt-4 px-4 pb-4">
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