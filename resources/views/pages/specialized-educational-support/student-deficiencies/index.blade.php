@extends('layouts.master')

@section('title', 'Perfis de Atendimento')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Alunos' => route('dashboard'),
            $student->person->name => route('specialized-educational-support.students.show', $student),
            'Perfis de Atendimento' => null
        ]" />
    </div>

    {{-- CARD UNIFICADO --}}
    <div class="custom-table-card shadow-sm border rounded-3 overflow-hidden">
        {{-- HEADER --}}
        <x-table.page-header
            title="Perfis de Atendimento do Aluno"
            subtitle="Aluno: {{ $student->person->name }}"
        >
            <div class="d-flex gap-2">
                <x-buttons.link-button
                    :href="route('specialized-educational-support.students.show', $student)"
                    variant="secondary"
                >
                    <i class="fas fa-arrow-left"></i>Voltar
                </x-buttons.link-button>

                @can('student-deficiency.create')
                <x-buttons.link-button
                    :href="route('specialized-educational-support.student-deficiencies.create', $student)"
                    variant="new"
                    title="Adicionar Perfil"
                >
                    <i class="fas fa-plus"></i>
                </x-buttons.link-button>
                @endcan
            </div>
        </x-table.page-header>

        {{-- FILTROS --}}
        <div class="px-3 pt-3">
            <x-table.filters.form
                data-dynamic-filter
                data-target="#student-deficiencies-table"
                :fields="[
                    [
                        'name' => 'deficiency_id',
                        'type' => 'select',
                        'options' => ['' => 'Perfis (todos)'] + $filterDeficiencies
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
        </div>

        {{-- TABELA --}}
        <div id="student-deficiencies-table" class="p-3">
            @include('pages.specialized-educational-support.student-deficiencies.partials.table')
        </div>
    </div>

    @push('scripts')
        @vite('resources/js/components/dynamicFilters.js')
    @endpush
@endsection