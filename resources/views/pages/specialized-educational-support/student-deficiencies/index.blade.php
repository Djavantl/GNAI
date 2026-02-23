@extends('layouts.master')

@section('title', 'Deficiências do Aluno')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Alunos' => route('specialized-educational-support.students.index'),
            $student->person->name => route('specialized-educational-support.students.show', $student),
            'Deficiências' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Deficiências do Aluno</h2>
            <p class="text-muted">Aluno: {{ $student->person->name }} </p>
        </div>
        <div class="d-flex gap-2">
            <x-buttons.link-button
                :href="route('specialized-educational-support.students.show', $student)"
                variant="secondary"
            >
                <i class="fas fa-arrow-left" aria-hidden="true"></i> Voltar
            </x-buttons.link-button>

            <x-buttons.link-button
                :href="route('specialized-educational-support.student-deficiencies.create', $student)"
                variant="new"
            >
                <i class="fas fa-plus" aria-hidden="true"></i> Adicionar
            </x-buttons.link-button>
        </div>
    </div>

    <x-table.filters.form
        data-dynamic-filter
        data-target="#student-deficiencies-table"
        :fields="[
            [
                'name' => 'deficiency_id',
                'type' => 'select',
                'options' => ['' => 'Deficiência (Todas)'] + $filterDeficiencies
            ],
            [
                'name' => 'severity',
                'type' => 'select',
                'options' => [
                    '' => 'Severidade (Todas)',
                    'mild' => 'Leve',
                    'moderate' => 'Moderada',
                    'severe' => 'Severa',
                ]
            ],
            [
                'name' => 'uses_support_resources',
                'type' => 'select',
                'options' => [
                    '' => 'Recurso de Apoio (Todos)',
                    '1' => 'Usa Recurso',
                    '0' => 'Não usa',
                ]
            ],
        ]"
    />

    <div id="student-deficiencies-table">
        @include('pages.specialized-educational-support.student-deficiencies.partials.table')
    </div>

    @push('scripts')
        @vite('resources/js/components/dynamicFilters.js')
    @endpush
@endsection