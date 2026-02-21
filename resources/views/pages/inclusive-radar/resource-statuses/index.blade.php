@extends('layouts.master')

@section('title', 'Status do Sistema')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Status dos Recursos' => route('inclusive-radar.resource-statuses.index'),
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3 align-items-center">
        <div>
            <h2 class="text-title">Status dos Recursos</h2>
            <p class="text-muted text-base">Gerencie como os recursos são classificados e as regras de empréstimo.</p>
        </div>
    </div>

    {{-- Tabela --}}
    <div id="resource-statuses-table">
        @include('pages.inclusive-radar.resource-statuses.partials.table')
    </div>

    @push('scripts')
        @vite('resources/js/components/dynamicFilters.js')
    @endpush
@endsection
