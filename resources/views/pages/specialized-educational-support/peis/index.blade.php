@extends('layouts.master')

@section('title', 'Planos Educacionais Individualizados (PEI)')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Alunos' => route('specialized-educational-support.students.index'),
            $student->person->name => route('specialized-educational-support.students.show', $student),
            'PEIs' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Histórico de PEIs</h2>
            <p class="text-muted">Aluno: {{ $student->person->name }}</p>
        </div>
        <div>
            <x-buttons.link-button
                :href="route('specialized-educational-support.students.show', $student)"
                variant="secondary"
            >
                <i class="fas fa-arrow-left"></i> Voltar para Aluno
            </x-buttons.link-button>
            <x-buttons.link-button class="ms-3"
                :href="route('specialized-educational-support.pei.create', $student->id)"
                variant="new"
            >
                <i class="fas fa-plus" aria-hidden="true"></i> Adicionar
            </x-buttons.link-button>
        </div>
    </div>

    <x-table.filters.form
        data-dynamic-filter
        data-target="#peis-index-table"
        :fields="[
            [
                'name' => 'semester_id',
                'type' => 'select',
                'options' => ['' => 'Semestre (Todos)'] +
                    collect($semesters)->mapWithKeys(fn($s) => [
                        $s->id => $s->label
                    ])->toArray()
            ],
            [
                'name' => 'version',
                'placeholder' => 'Versão...'
            ],
            [
                'name' => 'is_finished',
                'type' => 'select',
                'options' => [
                    '' => 'Status (Todos)',
                    '0' => 'Em andamento',
                    '1' => 'Finalizado',
                ]
            ],
        ]"
    />

    <div id="peis-index-table">
        @include('pages.specialized-educational-support.peis.partials.table')
    </div>

    @push('scripts')
        @vite('resources/js/components/dynamicFilters.js')
    @endpush

@endsection