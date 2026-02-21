@extends('layouts.master')

@section('title', 'Alunos')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Alunos' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3 align-items-center">
        <div>
            <h2 class="text-title mb-0">Alunos</h2>
            <p class="text-muted">Gerencie os estudantes e seus documentos de apoio especializado.</p>
        </div>
        <x-buttons.link-button
            :href="route('specialized-educational-support.students.create')"
            variant="new"
        >
            <i class="fas fa-plus"></i>Adicionar
        </x-buttons.link-button>
    </div>

    <x-table.filters.form
        data-dynamic-filter
        data-target="#students-table"
        :fields="[
            [
                'name' => 'name',
                'placeholder' => 'Nome do aluno...'
            ],
            [
                'name' => 'email',
                'placeholder' => 'Email...'
            ],
            [
                'name' => 'registration',
                'placeholder' => 'Matrícula...'
            ],
            [
                'name' => 'status',
                'type' => 'select',
                'options' => [
                    '' => 'Status (Todos)',
                    'active' => 'Ativo',
                    'locked' => 'Trancado',
                    'completed' => 'Concluído',
                    'dropped' => 'Evadido',
                ]
            ],
            [
                'name' => 'semester',
                'type' => 'select',
                'options' => ['' => 'Semestre (Todos)'] +
                    collect($semesters)
                        ->mapWithKeys(fn($semester) => [
                            $semester->id => $semester->label
                        ])
                        ->toArray()
            ],
        ]"
    />

    <div id="students-table">
        @include('pages.specialized-educational-support.students.partials.table')
    </div>
    @push('scripts')
        @vite('resources/js/components/dynamicFilters.js')
    @endpush
@endsection
