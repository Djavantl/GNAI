@extends('layouts.master')

@section('title', 'Instituições')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Instituições' => route('inclusive-radar.institutions.index'),
        ]" />
    </div>

    {{-- CARD UNIFICADO --}}
    <div class="custom-table-card shadow-sm border rounded-3 overflow-hidden">

        {{-- HEADER --}}
        <x-table.page-header
            title="Instituições Base"
            subtitle="Gerencie os locais centrais onde o radar de acessibilidade opera."
        >
            {{-- Botão de ação --}}
            <x-buttons.link-button
                :href="route('inclusive-radar.institutions.create')"
                variant="new"
            >
                <i class="fas fa-plus"></i> Adicionar
            </x-buttons.link-button>
        </x-table.page-header>

        {{-- FILTROS --}}
        <div class="px-3 pt-3">
            <x-table.filters.form
                data-dynamic-filter
                data-target="#institutions-table"
                :fields="[
                    ['name' => 'name', 'placeholder' => 'Filtrar por nome...'],
                    ['name' => 'location', 'placeholder' => 'Filtrar por cidade ou estado...'],
                    ['name' => 'is_active', 'type' => 'select', 'options' => [
                        '' => 'Status (Todos)',
                        '1' => 'Ativo',
                        '0' => 'Inativo'
                    ]]
                ]"
            />
        </div>

        {{-- TABELA --}}
        <div id="institutions-table" class="p-3">
            @include('pages.inclusive-radar.institutions.partials.table')
        </div>

    </div>

    @push('scripts')
        @vite('resources/js/components/dynamicFilters.js')
    @endpush
@endsection
