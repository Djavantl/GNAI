@extends('layouts.master')

@section('title', 'Atendimentos Pedagógicos do Aluno')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Alunos' => route('specialized-educational-support.students.index'),
            $student->person->name => route('specialized-educational-support.students.show', $student),
            'Atendimentos pedagógicos' => null,
        ]" />
    </div>

    <div class="custom-table-card shadow-sm border rounded-3 overflow-hidden">
        <x-table.page-header
            title="Histórico de Atendimentos Pedagógicos"
            subtitle="Aluno: {{ $student->person->name }}"
        >
            <x-buttons.link-button
                :href="route('specialized-educational-support.students.show', $student)"
                variant="secondary"
            >
                <i class="fas fa-arrow-left"></i> Voltar
            </x-buttons.link-button>
        </x-table.page-header>

        <div class="px-3 pt-3">
            <x-table.filters.form
                data-dynamic-filter
                data-target="#pedagogical-records-student-table"
                :fields="array_values(array_filter([
                    auth()->user()->can('pedagogical-record.view-all') ? [
                        'name' => 'professional_id',
                        'type' => 'select',
                        'options' => ['' => 'Profissional (Todos)'] +
                            collect($professionals)->mapWithKeys(fn($professional) => [
                                $professional->id => $professional->person->name ?? ('ID ' . $professional->id),
                            ])->toArray(),
                    ] : null,
                    [
                        'name' => 'is_present',
                        'type' => 'select',
                        'options' => ['' => 'Presença (Todas)', '1' => 'Presente', '0' => 'Ausente'],
                    ],
                    [
                        'name' => 'with_guardians',
                        'type' => 'select',
                        'options' => ['' => 'Com responsáveis (Todos)', '1' => 'Sim', '0' => 'Não'],
                    ],
                ]))"
            />
        </div>

        <div id="pedagogical-records-student-table" class="p-3">
            @include('pages.specialized-educational-support.students.pedagogical-records.partials.table')
        </div>
    </div>

    @push('scripts')
        @vite('resources/js/components/dynamicFilters.js')
    @endpush
@endsection
