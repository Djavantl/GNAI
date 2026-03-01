@extends('layouts.master')

@section('title', 'Documentos do Aluno')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Alunos' => route('specialized-educational-support.students.index'),
            $student->person->name => route('specialized-educational-support.students.show', $student),
            'Documentos' => null
        ]" />
    </div>

    {{-- CARD UNIFICADO --}}
    <div class="custom-table-card shadow-sm border rounded-3 overflow-hidden">
        {{-- HEADER --}}
        <x-table.page-header
            title="Documentos — {{ $student->person->name }}"
            subtitle="Gestão de laudos, relatórios e planos de AEE."
        >
            <div class="d-flex gap-2">
                <x-buttons.link-button
                    :href="route('specialized-educational-support.students.show', $student)"
                    variant="secondary"
                >
                    <i class="fas fa-arrow-left"></i>
                </x-buttons.link-button>

                <x-buttons.link-button
                    :href="route('specialized-educational-support.student-documents.create', $student)"
                    variant="new"
                    title="Adicionar documento"
                >
                    <i class="fas fa-plus"></i>
                </x-buttons.link-button>
            </div>
        </x-table.page-header>

        {{-- FILTROS --}}
        <div class="px-3 pt-3">
            <x-table.filters.form
                data-dynamic-filter
                data-target="#documents-table"
                :fields="[
                    [
                        'name' => 'title',
                        'placeholder' => 'Buscar por título...'
                    ],
                    [
                        'name' => 'type',
                        'type' => 'select',
                        'options' => $types
                    ],
                    [
                        'name' => 'semester_id',
                        'type' => 'select',
                        'options' => $semesters
                    ],
                    [
                        'name' => 'version',
                        'type' => 'select',
                        'options' => $versions
                    ],
                ]"
            />
        </div>

        {{-- TABELA --}}
        <div id="documents-table" class="p-3">
            @include('pages.specialized-educational-support.student-documents.partials.table')
        </div>
    </div>

    @push('scripts')
        @vite('resources/js/components/dynamicFilters.js')
    @endpush
@endsection