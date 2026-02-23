@extends('layouts.app')

@section('content')

<div class="mb-5">
    <x-breadcrumb :items="[
        'Home' => route('dashboard'),
        'Alunos' => route('specialized-educational-support.students.index'),
        $studentContext->student->person->name => route('specialized-educational-support.students.show', $studentContext->student),
        'Contextos' => route('specialized-educational-support.student-context.index', $studentContext->student),
        'Contexto #' . $studentContext->id => null
    ]" />
</div>

<div class="d-flex justify-content-between mb-3 align-items-center no-print">
    <div>
        <h2 class="text-title">Visualizar Contexto Educacional</h2>
        <p class="text-muted">
            Detalhes do registro de contexto para o aluno(a) **{{ $studentContext->student->person->name }}**.
        </p>
    </div>

    <div class="d-flex gap-2">
        @if($studentContext->is_current)
            <x-buttons.link-button 
                href="{{ route('specialized-educational-support.student-context.edit', $studentContext) }}" 
                variant="warning">
                <i class="fas fa-edit"></i> Editar
            </x-buttons.link-button>
        @endif

        <x-buttons.link-button 
            href="{{ route('specialized-educational-support.student-context.index', $studentContext->student) }}" 
            variant="secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </x-buttons.link-button>
    </div>
</div>

<div class="custom-table-card bg-white shadow-sm rounded">
    
    {{-- ================= IDENTIFICAÇÃO DO ALUNO (SOMENTE LEITURA) ================= --}}
    <x-forms.section title="Identificação do Aluno" />

    <div class="row g-2 px-4 pb-3">
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
                                <span class="text-danger fw-semibold">{{ strtoupper($student->status) }}</span>
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

    {{-- ================= INFORMAÇÕES DA AVALIAÇÃO ================= --}}
    <x-forms.section title="Identificação da Avaliação" />

    <div class="row g-3 px-4 pb-3">
        <x-show.info-item label="Semestre" column="col-md-3" isBox="true">
            {{ $studentContext->semester->label ?? $studentContext->semester->name ?? '—' }}
        </x-show.info-item>

        <x-show.info-item label="Tipo de Avaliação" column="col-md-3" isBox="true">
            @php
                $evalTypes = [
                    'initial' => 'Inicial',
                    'periodic_review' => 'Revisão Periódica',
                    'pei_review' => 'Revisão do PEI',
                    'specific_demand' => 'Demanda Específica'
                ];
            @endphp
            {{ $evalTypes[$studentContext->evaluation_type] ?? ucfirst(str_replace('_', ' ', $studentContext->evaluation_type)) }}
        </x-show.info-item>

        <x-show.info-item label="Versão" column="col-md-4" isBox="true">
            <strong>v{{ $studentContext->version }}</strong>
        </x-show.info-item>

        <x-show.info-item label="Contexto Atual?" column="col-md-2" isBox="true">
            @if($studentContext->is_current)
                <span class="text-success fw-bold">SIM</span>
            @else
                <span class="text-dark fw-bold">NÃO</span>
            @endif
        </x-show.info-item>
    </div>

    {{-- ================= HISTÓRICO EDUCACIONAL ================= --}}
    <x-forms.section title="Histórico e Necessidades Educacionais" />

    <div class="row px-4 pb-3">
        <x-show.info-textarea label="Histórico do Aluno" column="col-md-12" isBox="true">
            {!! nl2br(e($studentContext->history)) !!}
        </x-show.info-textarea>

        <x-show.info-textarea label="Necessidades Educacionais Específicas" column="col-md-12" isBox="true">
            {!! nl2br(e($studentContext->specific_educational_needs)) !!}
        </x-show.info-textarea>
    </div>

    {{-- ================= APRENDIZAGEM ================= --}}
    <x-forms.section title="Aprendizagem e Cognição" />

    <div class="row g-3 px-4 pb-3">
        <x-show.info-item label="Nível de Aprendizagem" column="col-md-3" isBox="true">
            @php $learnMap = ['very_low'=>'Muito Baixo', 'low'=>'Baixo', 'adequate'=>'Adequado', 'good'=>'Bom', 'excellent'=>'Excelente']; @endphp
            {{ $learnMap[$studentContext->learning_level] ?? $studentContext->learning_level }}
        </x-show.info-item>

        <x-show.info-item label="Nível de Atenção" column="col-md-3" isBox="true">
            @php $attMap = ['very_low'=>'Muito Baixo', 'low'=>'Baixo', 'moderate'=>'Moderado', 'high'=>'Alto']; @endphp
            {{ $attMap[$studentContext->attention_level] ?? $studentContext->attention_level }}
        </x-show.info-item>

        <x-show.info-item label="Nível de Memória" column="col-md-3" isBox="true">
            @php $memMap = ['low'=>'Baixa', 'moderate'=>'Moderada', 'good'=>'Boa']; @endphp
            {{ $memMap[$studentContext->memory_level] ?? $studentContext->memory_level }}
        </x-show.info-item>

        <x-show.info-item label="Nível de Raciocínio" column="col-md-3" isBox="true">
            @php $reasonMap = ['concrete'=>'Concreto', 'mixed'=>'Misto', 'abstract'=>'Abstrato']; @endphp
            {{ $reasonMap[$studentContext->reasoning_level] ?? $studentContext->reasoning_level }}
        </x-show.info-item>

        <x-show.info-textarea label="Observações de Aprendizagem" column="col-md-12" isBox="true">
            {{ $studentContext->learning_observations ?? 'Sem observações registradas.' }}
        </x-show.info-textarea>
    </div>

    {{-- ================= COMPORTAMENTO ================= --}}
    <x-forms.section title="Comunicação e Comportamento" />

    <div class="row g-3 px-4 pb-3">
        <x-show.info-item label="Tipo de Comunicação" column="col-md-4" isBox="true">
            @php $commMap = ['verbal'=>'Verbal', 'non_verbal'=>'Não verbal', 'mixed'=>'Mista']; @endphp
            {{ $commMap[$studentContext->communication_type] ?? $studentContext->communication_type }}
        </x-show.info-item>

        <x-show.info-item label="Nível de Interação" column="col-md-4" isBox="true">
            @php $intMap = ['very_low'=>'Muito Baixo', 'low'=>'Baixo', 'moderate'=>'Moderado', 'good'=>'Bom']; @endphp
            {{ $intMap[$studentContext->interaction_level] ?? $studentContext->interaction_level }}
        </x-show.info-item>

        <x-show.info-item label="Nível de Socialização" column="col-md-4" isBox="true">
            @php $socMap = ['isolated'=>'Isolado', 'selective'=>'Seletivo', 'participative'=>'Participativo']; @endphp
            {{ $socMap[$studentContext->socialization_level] ?? $studentContext->socialization_level }}
        </x-show.info-item>

        <x-show.info-item label="Comportamento Agressivo" column="col-md-3" isBox="true">
            {{ $studentContext->shows_aggressive_behavior ? 'Sim' : 'Não' }}
        </x-show.info-item>

        <x-show.info-item label="Comportamento Retraído" column="col-md-3" isBox="true">
            {{ $studentContext->shows_withdrawn_behavior ? 'Sim' : 'Não' }}
        </x-show.info-item>

        <x-show.info-textarea label="Notas Comportamentais" column="col-md-6" isBox="true">
            {{ $studentContext->behavior_notes ?? '—' }}
        </x-show.info-textarea>
    </div>

    {{-- ================= AUTONOMIA ================= --}}
    <x-forms.section title="Autonomia e Apoios" />

    <div class="row g-3 px-4 pb-3">
        <x-show.info-item label="Nível de Autonomia" column="col-md-4" isBox="true">
            @php $autMap = ['dependent'=>'Dependente', 'partial'=>'Parcial', 'independent'=>'Independente']; @endphp
            {{ $autMap[$studentContext->autonomy_level] ?? $studentContext->autonomy_level }}
        </x-show.info-item>

        <x-show.info-item label="Apoio de Mobilidade" column="col-md-4" isBox="true">
            {{ $studentContext->needs_mobility_support ? 'Sim' : 'Não' }}
        </x-show.info-item>

        <x-show.info-item label="Apoio de Comunicação" column="col-md-4" isBox="true">
            {{ $studentContext->needs_communication_support ? 'Sim' : 'Não' }}
        </x-show.info-item>

        <x-show.info-item label="Adaptação Pedagógica" column="col-md-6" isBox="true">
            {{ $studentContext->needs_pedagogical_adaptation ? 'Sim' : 'Não' }}
        </x-show.info-item>

        <x-show.info-item label="Tecnologia Assistiva" column="col-md-6" isBox="true">
            {{ $studentContext->uses_assistive_technology ? 'Sim' : 'Não' }}
        </x-show.info-item>
    </div>

    {{-- ================= SAÚDE ================= --}}
    <x-forms.section title="Saúde" />

    <div class="row g-3 px-4 pb-3">
        <x-show.info-item label="Possui Laudo Médico" column="col-md-3" isBox="true">
            {{ $studentContext->has_medical_report ? 'Sim' : 'Não' }}
        </x-show.info-item>

        <x-show.info-item label="Usa Medicação" column="col-md-3" isBox="true">
            {{ $studentContext->uses_medication ? 'Sim' : 'Não' }}
        </x-show.info-item>

        <x-show.info-textarea label="Observações Médicas" column="col-md-6" isBox="true">
            {{ $studentContext->medical_notes ?? '—' }}
        </x-show.info-textarea>
    </div>

    {{-- ================= SÍNTESE ================= --}}
    <x-forms.section title="Síntese Avaliativa" />

    <div class="row g-3 px-4 pb-3">
        <x-show.info-textarea label="Pontos Fortes" column="col-md-6" isBox="true">
            {!! nl2br(e($studentContext->strengths ?? '—')) !!}
        </x-show.info-textarea>

        <x-show.info-textarea label="Dificuldades" column="col-md-6" isBox="true">
            {!! nl2br(e($studentContext->difficulties ?? '—')) !!}
        </x-show.info-textarea>

        <x-show.info-textarea label="Recomendações" column="col-md-6" isBox="true">
            {!! nl2br(e($studentContext->recommendations ?? '—')) !!}
        </x-show.info-textarea>

        <x-show.info-textarea label="Observação Geral" column="col-md-6" isBox="true">
            {!! nl2br(e($studentContext->general_observation ?? '—')) !!}
        </x-show.info-textarea>
    </div>

    {{-- ================= INFORMAÇÕES DO SISTEMA ================= --}}
    <x-forms.section title="Informações do Sistema" />

    <div class="row g-3 px-4 pb-4">
        <x-show.info-item label="Profissional Avaliador" column="col-md-4" isBox="true">
            {{ $studentContext->evaluator->person->name ?? '—' }}
        </x-show.info-item>

        <x-show.info-item label="Criado em" column="col-md-4" isBox="true">
            {{ $studentContext->created_at->format('d/m/Y \à\s H:i') }}
        </x-show.info-item>

        <x-show.info-item label="Última Atualização" column="col-md-4" isBox="true">
            {{ $studentContext->updated_at->format('d/m/Y \à\s H:i') }}
        </x-show.info-item>
    </div>

    {{-- ================= RODAPÉ DE AÇÕES ================= --}}
    <div class="col-12 border-top p-4 d-flex justify-content-between align-items-center bg-light rounded-bottom no-print">
        <div class="text-muted small">
            <i class="fas fa-fingerprint me-1"></i> ID do Registro: #{{ $studentContext->id }}
        </div>

        <div class="d-flex gap-3">
            @if(!$studentContext->is_current)
                <form action="{{ route('specialized-educational-support.student-context.restore', $studentContext) }}"
                    method="POST" class="d-inline">
                    @csrf
                    <x-buttons.submit-button 
                        variant="info" 
                        onclick="return confirm('Tem certeza que deseja restaurar esta versão como sendo a atual?')">
                        <i class="fas fa-history"></i> Restaurar esta Versão
                    </x-buttons.submit-button>
                </form>
            @endif

            <form action="{{ route('specialized-educational-support.student-context.destroy', $studentContext) }}" 
                method="POST" class="d-inline"
                onsubmit="return confirm('ATENÇÃO: Deseja realmente excluir este registro de contexto?')">
                @csrf
                @method('DELETE')
                <x-buttons.submit-button variant="danger">
                    <i class="fas fa-trash-alt"></i> Excluir
                </x-buttons.submit-button>
            </form>
        </div>
    </div>
</div>

@endsection