@extends('layouts.master')

@section('title', 'Gestão de Matrículas')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Alunos' => route('specialized-educational-support.students.index'),
            'Prontuário do Aluno' => route('specialized-educational-support.students.show', $student),
            'Matrículas' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="text-title mb-0">Matrículas - {{ $student->person->name}} </h2>
            <p class="text-muted mb-0">Gerencie as matrículas e o histórico acadêmico do aluno.</p>
        </div>

        <x-buttons.link-button 
            :href="route('specialized-educational-support.student-courses.create', $student)" 
            variant="new"
            aria-label="Adicionar nova matrícula">
            <i class="fas fa-plus" aria-hidden="true"></i> Nova Matrícula
        </x-buttons.link-button>
    </div>

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

    <div id="student-courses-table">
        @include('pages.specialized-educational-support.student-courses.partials.table')
    </div>

    @push('scripts')
        @vite('resources/js/components/dynamicFilters.js')
    @endpush
@endsection