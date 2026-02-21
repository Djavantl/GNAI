@extends('layouts.master')

@section('title', 'Fila de Espera')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Fila de Espera' => route('inclusive-radar.waitlists.index'),
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3 align-items-center">
        <div>
            <h2 class="text-title">Fila de Espera</h2>
            <p class="text-muted">Gerencie solicitações de recursos que estão indisponíveis para empréstimo.</p>
        </div>

        <x-buttons.link-button
            :href="route('inclusive-radar.waitlists.create')"
            variant="new"
        >
            <i class="fas fa-plus"></i> Adicionar
        </x-buttons.link-button>
    </div>

    <x-table.filters.form
        data-dynamic-filter
        data-target="#waitlists-table"
        :fields="[
        [
            'name' => 'student',
            'placeholder' => 'Filtrar por aluno...'
        ],
        [
            'name' => 'professional',
            'placeholder' => 'Filtrar por profissional...'
        ],
        [
            'name' => 'status',
            'type' => 'select',
            'options' => [
                '' => 'Status (Todos)',
                'waiting' => 'Em espera',
                'notified' => 'Notificado',
                'fulfilled' => 'Atendido',
                'cancelled' => 'Cancelado',
            ]
        ],
    ]"
    />

    {{-- Tabela de Fila de Espera --}}
    <div id="waitlists-table">
        @include('pages.inclusive-radar.waitlists.partials.table')
    </div>

    @push('scripts')
        @vite('resources/js/components/dynamicFilters.js')
    @endpush
@endsection
