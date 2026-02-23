@extends('layouts.master')

@section('title', 'Todos os Planos Educacionais (PEI)')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'PEIs' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">

                Listagem Geral de PEIs
            </h2>
            <p class="text-muted">Visualização consolidada de todos os planos e adaptações curriculares do campus.</p>
        </div>
        {{-- O botão de "Novo" geralmente não fica aqui pois o PEI exige partir de um aluno específico --}}
    </div>

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
                'name' => 'discipline_id',
                'type' => 'select',
                'options' => ['' => 'Disciplina (Todas)'] +
                    collect($disciplines)->mapWithKeys(fn($d) => [
                        $d->id => $d->name
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

    <div id="peis-table">
        @include('pages.specialized-educational-support.peis.partials.table-all')
    </div>

    @push('scripts')
        @vite('resources/js/components/dynamicFilters.js')
    @endpush
@endsection