@extends('layouts.master')

@section('title', 'Registros de Sessões do Aluno')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Alunos' => route('specialized-educational-support.students.index'),
            $student->person->name => route('specialized-educational-support.students.show', $student),
            'Registros de Sessões' => null
        ]" />
    </div>

    <div class="custom-table-card shadow-sm border rounded-3 overflow-hidden">
        <x-table.page-header
            title="Histórico de Registros de Sessões"
            subtitle="Aluno: {{ $student->person->name }}"
        >
            <div class="d-flex gap-2">
                <x-buttons.link-button
                    :href="route('specialized-educational-support.students.show', $student)"
                    variant="secondary"
                >
                    <i class="fas fa-arrow-left"></i> Voltar
                </x-buttons.link-button>
            </div>
        </x-table.page-header>

        <div class="px-3 pt-3">
            <x-table.filters.form
                data-dynamic-filter
                data-target="#session-records-index-table"
                :fields="array_values(array_filter([
                    auth()->user()->can('session-record.view-all') ? [
                        'name' => 'professional_id',
                        'type' => 'select',
                        'options' => ['' => 'Profissional (Todos)'] +
                            collect($professionals)->mapWithKeys(fn($p) => [
                                $p->id => $p->person->name ?? ('ID ' . $p->id)
                            ])->toArray()
                    ] : null,
                    [
                        'name' => 'is_present',
                        'type' => 'select',
                        'options' => [
                            '' => 'Presença (Todos)',
                            '1' => 'Presente',
                            '0' => 'Ausente',
                        ]
                    ],
                ]))"
            />
        </div>

        <div id="session-records-index-table" class="p-3">
            @include('pages.specialized-educational-support.students.session-records.partials.table')
        </div>
    </div>

    @push('scripts')
        @vite('resources/js/components/dynamicFilters.js')
    @endpush
@endsection