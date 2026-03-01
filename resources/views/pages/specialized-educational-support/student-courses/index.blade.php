@extends('layouts.master')

@section('title', 'Gestão de Matrículas')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Alunos' => route('specialized-educational-support.students.index'),
            $student->person->name => route('specialized-educational-support.students.show', $student),
            'Matrículas' => null
        ]" />
    </div>

    {{-- CARD UNIFICADO --}}
    <div class="custom-table-card shadow-sm border rounded-3 overflow-hidden">
        {{-- HEADER --}}
        <x-table.page-header
            title="Matrículas — {{ $student->person->name }}"
            subtitle="Gerencie as matrículas e o histórico acadêmico do aluno."
        >
            <div class="d-flex gap-2">
                <x-buttons.link-button
                    :href="route('specialized-educational-support.students.show', $student)"
                    variant="secondary"
                >
                    <i class="fas fa-arrow-left"></i>Voltar
                </x-buttons.link-button>

                <x-buttons.link-button 
                    :href="route('specialized-educational-support.student-courses.create', $student)" 
                    variant="new"
                    title="Nova matrícula"
                >
                    <i class="fas fa-plus"></i>
                </x-buttons.link-button>
            </div>
        </x-table.page-header>

        {{-- FILTROS --}}
        <div class="px-3 pt-3">
            <x-table.filters.form
                data-dynamic-filter
                data-target="#student-courses-table"
                :fields="[
                    [
                        'name' => 'course_id',
                        'type' => 'select',
                        'options' => ['' => 'Filtrar por Curso (Histórico)'] + $courses
                    ],
                    [
                        'name' => 'academic_year',
                        'placeholder' => 'Ano letivo'
                    ],
                    [
                        'name' => 'is_current',
                        'type' => 'select',
                        'options' => [
                            '' => 'Status (Todos)',
                            '1' => 'Curso Atual',
                            '0' => 'Histórico Antigo',
                        ]
                    ],
                ]"
            />
        </div>

        {{-- TABELA --}}
        <div id="student-courses-table" class="p-3">
            @include('pages.specialized-educational-support.student-courses.partials.table')
        </div>
    </div>

    @push('scripts')
        @vite('resources/js/components/dynamicFilters.js')
    @endpush
@endsection