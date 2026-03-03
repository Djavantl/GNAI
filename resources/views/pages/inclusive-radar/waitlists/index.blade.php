@extends('layouts.master')

@section('title', 'Fila de Espera')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Fila de Espera' => route('inclusive-radar.waitlists.index'),
        ]" />
    </div>

    {{-- CARD UNIFICADO --}}
    <div class="custom-table-card shadow-sm border rounded-3 overflow-hidden">

        {{-- HEADER --}}
        <x-table.page-header
            title="Fila de Espera"
            subtitle="Gerencie solicitações de recursos que estão indisponíveis para empréstimo."
        >
            {{-- Botão de ação --}}
            <x-buttons.link-button
                :href="route('inclusive-radar.waitlists.create')"
                variant="new"
                title="Adicionar Fila de Espera"
            >
                <i class="fas fa-plus"></i>
            </x-buttons.link-button>
        </x-table.page-header>

        {{-- FILTROS --}}
        <div class="px-3 pt-3">
            <x-table.filters.form
                data-dynamic-filter
                data-target="#waitlists-table"
                :fields="[
                    ['name' => 'student', 'placeholder' => 'Filtrar por aluno...'],
                    ['name' => 'professional', 'placeholder' => 'Filtrar por profissional...'],
                    ['name' => 'status', 'type' => 'select', 'options' => [
                        ''          => 'Status (Todos)',
                        'waiting'   => 'Em espera',
                        'notified'  => 'Notificado',
                        'fulfilled' => 'Atendido',
                        'cancelled' => 'Cancelado',
                    ]],
                ]"
            />
        </div>

        {{-- TABELA --}}
        <div id="waitlists-table" class="p-3">
            @include('pages.inclusive-radar.waitlists.partials.table')
        </div>

    </div>

    @push('scripts')
        @vite('resources/js/components/dynamicFilters.js')
    @endpush
@endsection
