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

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="text-title">Sessões de Atendimento</h2>
            <p class="text-muted">Histórico de atendimentos para: <strong>{{ $student->person->name }}</strong></p>
        </div>
        <div class="d-flex gap-2">

            <x-buttons.link-button
                :href="route('specialized-educational-support.students.show', $student->id)"
                variant="secondary"
            >
                <i class="fas fa-arrow-left" aria-hidden="true"></i>Voltar ao Prontuário
            </x-buttons.link-button>
        </div>
    </div>
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

    <div id="sessions-table">
        @include('pages.specialized-educational-support.sessions.partials.table-student')
    </div>
   @push('scripts')
        @vite('resources/js/components/dynamicFilters.js')
    @endpush
@endsection