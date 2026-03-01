@extends('layouts.app')

@section('content')

<div class="mb-5">
    <x-breadcrumb :items="[
        'Home' => route('dashboard'),
        'Alunos' => route('specialized-educational-support.students.index'),
        $student->person->name => route('specialized-educational-support.students.show', $student),
        'PEI' => null
    ]" />
</div>

<div class="d-flex justify-content-between mb-3 align-items-center no-print">
    <div>
        <h2 class="text-title">Plano Educacional Individualizado (PEI)</h2>
        <p class="text-muted">
            Documento de acompanhamento para o aluno(a) **{{ $student->person->name }}**.
        </p>
    </div>

    <div class="d-flex gap-2">
        @if(!$pei->is_finished)
            <x-buttons.link-button 
                href="{{ route('specialized-educational-support.pei-discipline.create', $pei) }}" 
                variant="primary">
                <i class="fas fa-plus"></i> Adicionar Disciplina
            </x-buttons.link-button>

            <form action="{{ route('specialized-educational-support.pei.finish', $pei) }}" method="POST" class="d-inline">
                @csrf
                @method('PATCH')
                <x-buttons.submit-button variant="success" onclick="return confirm('Deseja finalizar este PEI? Após finalizado, nenhuma alteração poderá ser feita.')">
                    <i class="fas fa-check-circle"></i> Finalizar PEI
                </x-buttons.submit-button>
            </form>
        @else
            <x-buttons.link-button 
                href="{{ route('specialized-educational-support.pei.pdf', $pei) }}" 
                variant="dark" target="_blank">
                <i class="fas fa-file-pdf"></i> Gerar PDF
            </x-buttons.link-button>

            <form action="{{ route('specialized-educational-support.pei.version.newVersion', $pei) }}" method="POST" class="d-inline">
                @csrf
                <x-buttons.submit-button variant="info">
                    <i class="fas fa-copy"></i> Criar Nova Versão
                </x-buttons.submit-button>
            </form>
        @endif

        <x-buttons.link-button 
            href="{{ route('specialized-educational-support.pei.index', $student) }}" 
            variant="secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </x-buttons.link-button>
    </div>
</div>

<div class="custom-table-card bg-white shadow-sm rounded">
    
    {{-- ================= IDENTIFICAÇÃO DO ALUNO ================= --}}
    <x-forms.section title="Identificação do Aluno e Contexto" />

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
                            Curso: {{ $pei->course->name ?? '—' }} | Semestre: {{ $pei->semester->label ?? '—' }}
                        </span>
                        <span class="small text-muted">
                            Status do Documento: 
                            @if($pei->is_finished)
                                <span class="badge bg-success">FINALIZADO</span>
                            @else
                                <span class="badge bg-warning text-dark">EM EDIÇÃO</span>
                            @endif
                            | Versão: <strong>v{{ $pei->version }}</strong>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ================= ADAPTAÇÕES POR DISIPLINA ================= --}}
    <x-forms.section title="Adaptações Curriculares por Disciplina" />

    <div class="px-4 pb-4">
        {{-- ================= DETALHES DA ADAPTAÇÃO ================= --}}
    <x-forms.section title="Planejamento da Disciplina: {{ $peiDiscipline->discipline->name }}" />

    <div class="row g-3 px-4 pb-3">
        <x-show.info-textarea label="Objetivos Específicos" column="col-md-12" isBox="true">
            {!! nl2br(e($peiDiscipline->specific_objectives)) !!}
        </x-show.info-textarea>

        <x-show.info-textarea label="Conteúdo Programático" column="col-md-12" isBox="true">
            {!! nl2br(e($peiDiscipline->content_programmatic)) !!}
        </x-show.info-textarea>

        <x-show.info-textarea label="Metodologias e Estratégias" column="col-md-12" isBox="true">
            {!! nl2br(e($peiDiscipline->methodologies)) !!}
        </x-show.info-textarea>

        <x-show.info-textarea label="Processo de Avaliação" column="col-md-12" isBox="true">
            {!! nl2br(e($peiDiscipline->evaluations)) !!}
        </x-show.info-textarea>
    </div>
    </div>

    {{-- ================= RODAPÉ ================= --}}
    <div class="col-12 border-top p-4 d-flex justify-content-between align-items-center bg-light rounded-bottom no-print">
        <div class="text-muted small">
            <i class="fas fa-fingerprint me-1"></i> ID do PEI: #{{ $pei->id }}
        </div>

        <div class="d-flex gap-2">
            @if(!$pei->is_finished)
                <form action="{{ route('specialized-educational-support.pei.destroy', $pei) }}" 
                    method="POST" class="d-inline"
                    onsubmit="return confirm('Deseja realmente excluir este PEI por completo?')">
                    @csrf
                    @method('DELETE')
                    <x-buttons.submit-button variant="danger">
                        <i class="fas fa-trash-alt"></i> Excluir PEI
                    </x-buttons.submit-button>
                </form>
            @endif
        </div>
    </div>
</div>

@endsection