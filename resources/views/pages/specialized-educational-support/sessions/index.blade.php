@extends('layouts.master')

@section('title', 'Sessões de Atendimento')

@section('content')

    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Sessões' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="text-title">Sessões de Atendimento</h2>
        <div class="d-flex gap-2">
            <x-buttons.link-button
                :href="route('specialized-educational-support.sessions.create')"
                variant="new"
            >
               <i class="fas fa-plus" aria-hidden="true"></i>  Nova Sessão
            </x-buttons.link-button>
        </div>
    </div>

    <x-table.filters.form
        data-dynamic-filter
        data-target="#sessions-table"
        :fields="[
            [
                'name' => 'student',
                'type' => 'select',
                'options' => ['' => 'Aluno (Todos)'] +
                    collect($students)->mapWithKeys(fn($s) => [
                        $s->id => $s->person->name ?? 'Aluno'
                    ])->toArray()
            ],
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
                    'completed' => 'Realizada',
                    'canceled' => 'Cancelada',
                ]
            ],
        ]"
    />

    <div id="sessions-table">
        @include('pages.specialized-educational-support.sessions.partials.table')
    </div>

    
    @push('scripts')
        @vite('resources/js/components/dynamicFilters.js')
    @endpush
@endsection
