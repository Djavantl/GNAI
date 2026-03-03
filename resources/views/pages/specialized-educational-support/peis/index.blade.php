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

    {{-- CARD UNIFICADO --}}
    <div class="custom-table-card shadow-sm border rounded-3 overflow-hidden">
        {{-- HEADER --}}
        <x-table.page-header
            title="Histórico de PEIs"
            subtitle="Aluno: {{ $student->person->name }}"
        >
            <div class="d-flex gap-2">
                <x-buttons.link-button
                    :href="route('specialized-educational-support.students.show', $student)"
                    variant="secondary"
                >
                    <i class="fas fa-arrow-left"></i> Voltar 
                </x-buttons.link-button>

                <x-buttons.link-button
                    :href="route('specialized-educational-support.pei.create', $student->id)"
                    variant="new"
                    title="Adicionar PEI"
                >
                    <i class="fas fa-plus"></i>
                </x-buttons.link-button>
            </div>
        </x-table.page-header>

        {{-- FILTROS --}}
        <div class="px-3 pt-3">
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
        </div>

        {{-- TABELA --}}
        <div id="peis-index-table" class="p-3">
            @include('pages.specialized-educational-support.peis.partials.table')
        </div>
    </div>

    @push('scripts')
        @vite('resources/js/components/dynamicFilters.js')
    @endpush
@endsection