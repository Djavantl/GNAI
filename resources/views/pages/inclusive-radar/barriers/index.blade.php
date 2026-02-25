@extends('layouts.master')

@section('title', 'Radar de Barreiras')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Radar de Barreiras' => route('inclusive-radar.barriers.index'),
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3 align-items-center">
        <div>
            <h2 class="text-title">Radar de Barreiras</h2>
            <p class="text-muted text-base">Identificação e monitoramento de barreiras à inclusão escolar.</p>
        </div>
        <x-buttons.link-button
            :href="route('inclusive-radar.barriers.create')"
            variant="new"
        >
            <i class="fas fa-plus"></i> Identificar Barreira
        </x-buttons.link-button>
    </div>

    {{-- 🔎 Filtros --}}
    <x-table.filters.form
        data-dynamic-filter
        data-target="#barriers-table"
        :fields="[
            [
                'name' => 'name',
                'placeholder' => 'Filtrar por nome...'
            ],
            [
                'name' => 'priority',
                'type' => 'select',
                'options' => [
                    '' => 'Prioridade (Todas)',
                    'low' => 'Baixa',
                    'medium' => 'Média',
                    'high' => 'Alta',
                    'critical' => 'Crítica',
                    'urgent' => 'Urgente',
                ]
            ],
            [
                'name' => 'status',
                'type' => 'select',
                'options' => [
                    '' => 'Status (Todos)',
                    'identified' => 'Identificada',
                    'under_analysis' => 'Em Análise',
                    'in_progress' => 'Em Tratamento',
                    'resolved' => 'Resolvida',
                    'not_applicable' => 'Não Aplicável',
                ]
            ],
        ]"
    />

    {{-- Tabela --}}
    <div id="barriers-table">
        @include('pages.inclusive-radar.barriers.partials.table')
    </div>

    @push('scripts')
        @vite('resources/js/components/dynamicFilters.js')
    @endpush
@endsection
