@extends('layouts.app')

@section('content')

<div class="mb-5">
    <x-breadcrumb :items="[
        'Home' => route('dashboard'),
        'Prontuário do Aluno' => route('specialized-educational-support.students.show', $student),
        'Contexto do Aluno' => route('specialized-educational-support.student-context.index', $student),
        'Novo Contexto' => null
    ]" />
</div>

<div class="d-flex justify-content-between mb-3 align-items-center">
    <div>
        <h2 class="text-title">Novo Contexto Educacional</h2>
        <p class="text-muted">
            Registre um novo contexto com observações atuais sobre o comportamento do aluno(a) {{ $student->person->name }}.
        </p>
    </div>

    <x-buttons.link-button
        href="{{ route('specialized-educational-support.student-context.index', $student) }}"
        variant="secondary">
        <i class="fas fa-times"></i> Cancelar
    </x-buttons.link-button>
</div>


<x-forms.form-card
    action="{{ route('specialized-educational-support.student-context.store', $student) }}"
    method="POST">

    @csrf

    {{-- ================= IDENTIFICAÇÃO DO ALUNO ================= --}}
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
                            Matrícula: {{ $student->registration ?? '—' }}
                        </span>
                        <span class="small text-muted">
                            Status:
                            @if($student->status === 'active')
                                <span class="text-success fw-semibold">ATIVO</span>
                            @else
                                <span class="text-danger fw-semibold">
                                    {{ strtoupper($student->status) }}
                                </span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- DEFICIÊNCIAS --}}
        <div class="col-md-12 border-top pt-4 ">

            <div class="row g-2">
                @forelse($student->deficiencies as $def)
                    <div class="col-md-6">
                        <div class="card p-3 border-light bg-soft-info">
                            <strong class="d-block">
                                {{ $def->name ?? '—' }}
                            </strong>
                            <span class="small text-muted">
                                 GRAU: {{ $def->pivot->severity ?? '—' }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="col-md-6">
                        <div class="card p-3 border-light bg-soft-info text-muted">
                            Nenhuma deficiência registrada para
                            {{ $student->person->name ?? 'este aluno' }}.
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

    </div>

    {{-- ================= HISTÓRICO EDUCACIONAL ================= --}}
    <x-forms.section title="Histórico e Necessidades Educacionais" />

    <div class="row px-4 pb-3">

        <div class="col-md-12">
            <x-forms.textarea
                name="history"
                label="Histórico do Aluno"
                rows="3"
                required
                placeholder="Descreva o histórico educacional do aluno"
                :value="old('history')"
            />
        </div>

        <div class="col-md-12">
            <x-forms.textarea
                name="specific_educational_needs"
                label="Necessidades Educacionais Específicas"
                rows="3"
                required
                placeholder="Descreva as necessidades educacionais específicas"
                :value="old('specific_educational_needs')"
            />
        </div>

    </div>

    {{-- ================= APRENDIZAGEM ================= --}}
    <x-forms.section title="Aprendizagem e Cognição" />

    <div class="row g-2 px-4 pb-3">

        <div class="col-md-3">
            <x-forms.select name="learning_level" label="Nível de Aprendizagem"
            :options="[
            'very_low'=>'Muito Baixo',
            'low'=>'Baixo',
            'adequate'=>'Adequado',
            'good'=>'Bom',
            'excellent'=>'Excelente'
            ]" :value="old('learning_level')" />
        </div>

        <div class="col-md-3">
            <x-forms.select name="attention_level" label="Nível de Atenção"
            :options="[
            'very_low'=>'Muito Baixo',
            'low'=>'Baixo',
            'moderate'=>'Moderado',
            'high'=>'Alto'
            ]" :value="old('attention_level')" />
        </div>

        <div class="col-md-3">
            <x-forms.select name="memory_level" label="Nível de Memória"
            :options="[
            'low'=>'Baixa',
            'moderate'=>'Moderada',
            'good'=>'Boa'
            ]" :value="old('memory_level')" />
        </div>

        <div class="col-md-3">
            <x-forms.select name="reasoning_level" label="Nível de Raciocínio"
            :options="[
            'concrete'=>'Concreto',
            'mixed'=>'Misto',
            'abstract'=>'Abstrato'
            ]" :value="old('reasoning_level')" />
        </div>

        <div class="col-md-12">
            <x-forms.textarea
            name="learning_observations"
            label="Observações de Aprendizagem"
            rows="3"
            placeholder="Observações pedagógicas relevantes"
            :value="old('learning_observations')" />
        </div>

    </div>

    {{-- ================= COMPORTAMENTO ================= --}}
    <x-forms.section title="Comunicação e Comportamento" />

    <div class="row g-2 px-4 pb-3">

        <div class="col-md-4">
            <x-forms.select name="communication_type" label="Tipo de Comunicação"
            :options="[
            'verbal'=>'Verbal',
            'non_verbal'=>'Não verbal',
            'mixed'=>'Mista'
            ]" :value="old('communication_type')" />
        </div>

        <div class="col-md-4">
            <x-forms.select name="interaction_level" label="Nível de Interação"
            :options="[
            'very_low'=>'Muito Baixo',
            'low'=>'Baixo',
            'moderate'=>'Moderado',
            'good'=>'Bom'
            ]" :value="old('interaction_level')" />
        </div>

        <div class="col-md-4">
            <x-forms.select name="socialization_level" label="Nível de Socialização"
            :options="[
            'isolated'=>'Isolado',
            'selective'=>'Seletivo',
            'participative'=>'Participativo'
            ]" :value="old('socialization_level')" />
        </div>

        <div class="col-md-6">
            <x-forms.select name="shows_aggressive_behavior" label="Comportamento Agressivo"
            :options="[1=>'Sim',0=>'Não']"
            :value="old('shows_aggressive_behavior')" />
        </div>

        <div class="col-md-6">
            <x-forms.select name="shows_withdrawn_behavior" label="Comportamento Retraído"
            :options="[1=>'Sim',0=>'Não']"
            :value="old('shows_withdrawn_behavior')" />
        </div>

        <div class="col-md-12">
            <x-forms.textarea name="behavior_notes" label="Notas Comportamentais"
            rows="3"
            placeholder="Observações comportamentais"
            :value="old('behavior_notes')" />
        </div>

    </div>

    {{-- ================= AUTONOMIA ================= --}}
    <x-forms.section title="Autonomia e Apoios" />

    <div class="row g-2 px-4 pb-3">

        <div class="col-md-4">
            <x-forms.select name="autonomy_level" label="Nível de Autonomia"
            :options="[
            'dependent'=>'Dependente',
            'partial'=>'Parcial',
            'independent'=>'Independente'
            ]" :value="old('autonomy_level')" />
        </div>

        <div class="col-md-4">
            <x-forms.select name="needs_mobility_support" label="Apoio de Mobilidade"
            :options="[1=>'Sim',0=>'Não']"
            :value="old('needs_mobility_support')" />
        </div>

        <div class="col-md-4">
            <x-forms.select name="needs_communication_support" label="Apoio de Comunicação"
            :options="[1=>'Sim',0=>'Não']"
            :value="old('needs_communication_support')" />
        </div>

        <div class="col-md-6">
            <x-forms.select name="needs_pedagogical_adaptation" label="Adaptação Pedagógica"
            :options="[1=>'Sim',0=>'Não']"
            :value="old('needs_pedagogical_adaptation')" />
        </div>

        <div class="col-md-6">
            <x-forms.select name="uses_assistive_technology" label="Tecnologia Assistiva"
            :options="[1=>'Sim',0=>'Não']"
            :value="old('uses_assistive_technology')" />
        </div>

    </div>

    {{-- ================= SAÚDE ================= --}}
    <x-forms.section title="Saúde" />

    <div class="row g-2 px-4 pb-3">

        <div class="col-md-6">
            <x-forms.select name="has_medical_report" label="Possui Laudo Médico"
            :options="[1=>'Sim',0=>'Não']"
            :value="old('has_medical_report')" />
        </div>

        <div class="col-md-6">
            <x-forms.select name="uses_medication" label="Usa Medicação"
            :options="[1=>'Sim',0=>'Não']"
            :value="old('uses_medication')" />
        </div>

        <div class="col-md-12">
            <x-forms.textarea name="medical_notes" label="Observações Médicas"
            rows="3"
            placeholder="Informações médicas relevantes"
            :value="old('medical_notes')" />
        </div>

    </div>

    {{-- ================= FINAL ================= --}}
    <x-forms.section title="Síntese Avaliativa" />

    <div class="row g-2 px-4 pb-3">

        <div class="col-md-6">
            <x-forms.textarea name="strengths" label="Pontos Fortes"
            rows="4"
            placeholder="Potencialidades do aluno"
            :value="old('strengths')" />
        </div>

        <div class="col-md-6">
            <x-forms.textarea name="difficulties" label="Dificuldades"
            rows="4"
            placeholder="Principais dificuldades observadas"
            :value="old('difficulties')" />
        </div>

        <div class="col-md-6">
            <x-forms.textarea name="recommendations" label="Recomendações"
            rows="4"
            placeholder="Encaminhamentos pedagógicos"
            :value="old('recommendations')" />
        </div>

        <div class="col-md-6">
            <x-forms.textarea name="general_observation" label="Observação Geral"
            rows="4"
            placeholder="Síntese final do contexto"
            :value="old('general_observation')" />
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