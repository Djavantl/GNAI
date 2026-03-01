@extends('layouts.master')

@section('title', 'Sessões de Atendimento')

@section('content')

    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Sessões' => null
        ]" />
    </div>

    {{-- CARD UNIFICADO --}}
    <div class="custom-table-card shadow-sm border rounded-3 overflow-hidden">
        {{-- HEADER --}}
        <x-table.page-header
            title="Sessões de Atendimento"
            subtitle="Gerencie os atendimentos realizados com estudantes."
        >
            <x-buttons.link-button
                :href="route('specialized-educational-support.sessions.create')"
                variant="new"
                title="Nova sessão"
            >
                <i class="fas fa-plus"></i>
            </x-buttons.link-button>
        </x-table.page-header>

        {{-- FILTROS --}}
        <div class="px-3 pt-3">
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
        </div>

        {{-- TABELA --}}
        <div id="sessions-table" class="p-3">
            @include('pages.specialized-educational-support.sessions.partials.table')
        </div>
    </div>

    @push('scripts')
        @vite('resources/js/components/dynamicFilters.js')
    @endpush
@endsection