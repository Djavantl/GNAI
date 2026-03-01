@extends('layouts.master')

@section('title', "Sessões de Atendimento - {$student->person->name}")

@section('content')

    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Prontuários' => route('specialized-educational-support.students.index'),
            $student->person->name => route('specialized-educational-support.students.show', $student),
            'Sessões' => null
        ]" />
    </div>

    {{-- CARD UNIFICADO --}}
    <div class="custom-table-card shadow-sm border rounded-3 overflow-hidden">

        {{-- HEADER --}}
        <x-table.page-header
            title="Sessões de Atendimento — {{ $student->person->name }}"
            subtitle="Histórico de atendimentos realizados para o aluno."
        >
            <x-buttons.link-button
                :href="route('specialized-educational-support.students.show', $student)"
                variant="secondary"
                title="Voltar ao prontuário"
            >
                <i class="fas fa-arrow-left"></i>Voltar
            </x-buttons.link-button>
        </x-table.page-header>

        {{-- FILTROS --}}
        <div class="px-3 pt-3">
            <x-table.filters.form
                data-dynamic-filter
                data-target="#sessions-table"
                :fields="[
                    [
                        'name' => 'professional',
                        'type' => 'select',
                        'options' => ['' => 'Profissional (Todos)'] +
                            collect($professionals)->mapWithKeys(fn($p) => [
                                $p->id => $p->person->name ?? 'Profissional'
                            ])->toArray()
                    ],
                    [
                        'name' => 'type',
                        'type' => 'select',
                        'options' => [
                            '' => 'Tipo (Todos)',
                            'individual' => 'Individual',
                            'group' => 'Grupo',
                        ]
                    ],
                    [
                        'name' => 'status',
                        'type' => 'select',
                        'options' => [
                            '' => 'Status (Todos)',
                            'scheduled' => 'Agendada',
                            'realized' => 'Realizada',
                            'canceled' => 'Cancelada',
                        ]
                    ],
                ]"
            />
        </div>

        {{-- TABELA --}}
        <div id="sessions-table" class="p-3">
            @include('pages.specialized-educational-support.sessions.partials.table-student')
        </div>

    </div>

    @push('scripts')
        @vite('resources/js/components/dynamicFilters.js')
    @endpush

@endsection