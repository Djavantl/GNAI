@extends('layouts.master')

@section('title', 'Manutenções')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
        'Home' => route('dashboard'),
        'Manutenções' => route('inclusive-radar.maintenances.index'),
    ]" />
    </div>

    <div class="d-flex justify-content-between mb-3 align-items-center">
        <div>
            <h2 class="text-title">Manutenções</h2>
            <p class="text-muted">Gerenciamento das tecnologias assistivas com manutenção pendente ou concluída.</p>
        </div>
    </div>

    {{-- Filtros --}}
    <x-table.filters.form
        data-dynamic-filter
        data-target="#maintenances-table"
        :fields="[
        ['name' => 'resource', 'placeholder' => 'Filtrar por recurso...'],
        ['name' => 'status', 'type' => 'select', 'options' => [
            '' => 'Status (Todos)',
            'pending' => 'Pendente',
            'completed' => 'Concluída',
        ]],
    ]"
    />

    <div id="maintenances-table">
        @include('pages.inclusive-radar.maintenances.partials.table')
    </div>

    @push('scripts')
        @vite('resources/js/components/dynamicFilters.js')
    @endpush
@endsection
