@extends('layouts.master')

@section('title', 'Minhas Sessões')

@section('content')

    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Sessões' => route('specialized-educational-support.sessions.index'),
            'Minhas Sessões' => null
        ]" />
    </div>

    <div class="custom-table-card shadow-sm border rounded-3 overflow-hidden">
        <x-table.page-header
            title="Minhas Sessões"
            subtitle="Visualize as sessões em que você está vinculado como profissional responsável."
        >
            <x-buttons.link-button
                :href="route('specialized-educational-support.sessions.create')"
                variant="new"
                title="Nova sessão"
            >
                <i class="fas fa-plus"></i>
            </x-buttons.link-button>
            <x-buttons.link-button
                :href="route('specialized-educational-support.sessions.index')"
                variant="secondary"
                title="Voltar para sessões"
            >
                <i class="fas fa-arrow-left"></i>
            </x-buttons.link-button>
        </x-table.page-header>

        <div class="px-3 pt-3">
            <x-table.filters.form
                data-dynamic-filter
                data-target="#my-sessions-table"
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

        <div id="my-sessions-table" class="p-3">
            @include('pages.specialized-educational-support.sessions.partials.my-table')
        </div>
    </div>

    @push('scripts')
        @vite('resources/js/components/dynamicFilters.js')
    @endpush
@endsection