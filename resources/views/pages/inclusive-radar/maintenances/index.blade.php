@extends('layouts.master')

@section('title', 'Manutenções')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Manutenções' => route('inclusive-radar.maintenances.index'),
        ]" />
    </div>

    {{-- CARD UNIFICADO --}}
    <div class="custom-table-card shadow-sm border rounded-3 overflow-hidden">

        {{-- HEADER --}}
        <x-table.page-header
            title="Manutenções"
            subtitle="Gerenciamento das tecnologias assistivas com manutenção pendente ou concluída."
        >
            {{-- Nenhum botão de ação adicional --}}
        </x-table.page-header>

        {{-- FILTROS --}}
        <div class="px-3 pt-3">
            <x-table.filters.form
                data-dynamic-filter
                data-target="#maintenances-table"
                :fields="[
                    ['name' => 'resource', 'placeholder' => 'Filtrar por recurso...'],
                    ['name' => 'status', 'type' => 'select', 'options' => [
                        ''        => 'Status (Todos)',
                        'pending' => 'Pendente',
                        'completed' => 'Concluída',
                    ]],
                ]"
            />
        </div>

        {{-- TABELA --}}
        <div id="maintenances-table" class="p-3">
            @include('pages.inclusive-radar.maintenances.partials.table')
        </div>

    </div>

    @push('scripts')
        @vite('resources/js/components/dynamicFilters.js')
    @endpush
@endsection
