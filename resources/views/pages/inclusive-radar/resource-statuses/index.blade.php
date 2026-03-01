@extends('layouts.master')

@section('title', 'Status do Sistema')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Status dos Recursos' => route('inclusive-radar.resource-statuses.index'),
        ]" />
    </div>

    {{-- CARD UNIFICADO --}}
    <div class="custom-table-card shadow-sm border rounded-3 overflow-hidden">

        {{-- HEADER --}}
        <x-table.page-header
            title="Status dos Recursos"
            subtitle="Gerencie como os recursos são classificados e as regras de empréstimo."
        >
            {{-- Nenhum botão de ação --}}
        </x-table.page-header>

        {{-- TABELA --}}
        <div id="resource-statuses-table" class="p-3">
            @include('pages.inclusive-radar.resource-statuses.partials.table')
        </div>

    </div>

    @push('scripts')
        @vite('resources/js/components/dynamicFilters.js')
    @endpush
@endsection
