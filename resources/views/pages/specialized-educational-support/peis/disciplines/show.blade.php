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

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3 no-print">
    <div>
        <h2 class="text-title">Planejamento da Disciplina {{ $peiDiscipline->discipline->name }}</h2>
        <p class="text-muted">
            Edite a adaptação para a disciplina {{ $peiDiscipline->discipline->name }}
        </p>
    </div>

    <div class="d-flex gap-2 flex-wrap justify-content-end ms-md-auto">

        <x-buttons.pdf-button 
                :href="route('specialized-educational-support.pei.discipline.pdf', [$pei, $peiDiscipline])" 
                target="_blank" 
            />

        @can('pei-discipline.update')
        <x-buttons.link-button 
            href="{{ route('specialized-educational-support.pei-discipline.edit', [$pei, $peiDiscipline]) }}"
            variant="warning">
            <i class="fas fa-edit"></i> Editar
        </x-buttons.link-button>
        @endcan
        <x-buttons.link-button 
            href="{{ route('specialized-educational-support.pei.show', $pei) }}" 
            variant="secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </x-buttons.link-button>
    </div>
</div>

<div class="custom-table-card bg-white shadow-sm rounded">

        {{-- ================= DETALHES DA ADAPTAÇÃO ================= --}}
    <x-forms.section title="Adaptações Razoáveis e/ou Acessibilidades Curriculares" />

    <div class="row g-3 px-4 pb-3">
        <x-show.info-textarea label="Objetivos Específicos" column="col-md-12" isBox="true">
            {!! $peiDiscipline->specific_objectives !!}
        </x-show.info-textarea>

        <x-show.info-textarea label="Conteúdo Programático" column="col-md-12" isBox="true">
            {!! $peiDiscipline->content_programmatic !!}
        </x-show.info-textarea>

        <x-show.info-textarea label="Metodologias e Estratégias" column="col-md-12" isBox="true">
            {!! $peiDiscipline->methodologies !!}
        </x-show.info-textarea>

        <x-show.info-textarea label="Processo de Avaliação" column="col-md-12" isBox="true">
            {!! $peiDiscipline->evaluations !!}
        </x-show.info-textarea>

        <x-show.info-textarea label="Registros Complementares" column="col-md-12" isBox="true">
            {!! $peiDiscipline->complementary_records ?? 'Nenhum registro complementar informado.' !!}
        </x-show.info-textarea>

        <x-show.info-textarea label="Parecer" column="col-md-12" isBox="true">
            {!! $peiDiscipline->opinion !!}
        </x-show.info-textarea>

        
    </div>


    {{-- ================= RODAPÉ ================= --}}
    <div class="col-12 border-top p-4 d-flex flex-wrap justify-content-end gap-2 bg-light rounded-bottom no-print">
        <div class="d-flex flex-wrap gap-2">
            @if(!$pei->is_finished)
            @can('pei-discipline.delete')
                <x-buttons.submit-button
                    type="button"
                    variant="danger"
                    data-bs-toggle="modal"
                    data-bs-target="#globalConfirmActionModal"
                    data-confirm-title="Excluir Adaptacao"
                    data-confirm-message="Deseja realmente excluir esta adaptacao?"
                    data-confirm-action="{{ route('specialized-educational-support.pei-discipline.destroy', [$pei, $peiDiscipline]) }}"
                    data-confirm-method="DELETE"
                    data-confirm-submit-text="Confirmar Exclusao"
                    data-confirm-variant="danger"
                >
                        <i class="fas fa-trash-alt"></i> Excluir Adaptação
                    </x-buttons.submit-button>
            @endcan
            @endif
        </div>
    </div>
</div>

@endsection
