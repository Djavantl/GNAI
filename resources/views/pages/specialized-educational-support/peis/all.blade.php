@extends('layouts.master')

@section('title', 'Todos os Planos Educacionais (PEI)')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'PEIs' => null
        ]" />
    </div>

    {{-- CARD UNIFICADO --}}
    <div class="custom-table-card shadow-sm border rounded-3 overflow-hidden">
        {{-- HEADER --}}
        <x-table.page-header
            title="Listagem Geral de PEIs"
            subtitle="Visualização consolidada de todos os planos e adaptações curriculares do campus."
        />

        {{-- FILTROS --}}
        <div class="px-3 pt-3">
            <x-table.filters.form
                data-dynamic-filter
                data-target="#peis-table"
                :fields="[
                    [
                        'name' => 'student_id',
                        'type' => 'select',
                        'options' => ['' => 'Estudante (Todos)'] +
                            collect($students)->mapWithKeys(fn($s) => [
                                $s->id => $s->person->name
                            ])->toArray()
                    ],
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
        <div id="peis-table" class="p-3">
            @include('pages.specialized-educational-support.peis.partials.table-all')
        </div>
    </div>

    @push('scripts')
        @vite('resources/js/components/dynamicFilters.js')
    @endpush
@endsection