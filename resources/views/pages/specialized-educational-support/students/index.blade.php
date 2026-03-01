@extends('layouts.master')

@section('title', 'Alunos')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Alunos' => null
        ]" />
    </div>
 
    {{-- CARD UNIFICADO --}}
    <div class="custom-table-card shadow-sm border rounded-3 overflow-hidden">
        {{-- HEADER --}}
        <x-table.page-header
            title="Alunos"
            subtitle="Gerencie os estudantes e seus documentos de apoio especializado."
        >
            {{-- Botão de ação --}}
            <x-buttons.link-button
                :href="route('specialized-educational-support.students.create')"
                variant="new"
                title="Adicionar alunos"
            >
                <i class="fas fa-plus"></i>
            </x-buttons.link-button>
        </x-table.page-header>

        <div class="px-3 pt-3">
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
        </div>
        <div id="students-table" class="p-3">
            @include('pages.specialized-educational-support.students.partials.table')
        </div>
    </div>
    @push('scripts')
        @vite('resources/js/components/dynamicFilters.js')
    @endpush
@endsection
