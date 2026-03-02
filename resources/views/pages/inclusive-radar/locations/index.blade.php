@extends('layouts.master')

@section('title', 'Pontos de Referência')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Pontos de Referência' => route('inclusive-radar.locations.index'),
        ]" />
    </div>

    {{-- CARD UNIFICADO --}}
    <div class="custom-table-card shadow-sm border rounded-3 overflow-hidden">

        {{-- HEADER --}}
        <x-table.page-header
            title="Pontos de Referência"
            subtitle="Gerencie os prédios, salas e locais específicos dentro de cada instituição."
        >
            {{-- Botão de ação --}}
            <x-buttons.link-button
                :href="route('inclusive-radar.locations.create')"
                variant="new"
                title="Adicionar Ponto de Referência"
            >
                <i class="fas fa-plus"></i>
            </x-buttons.link-button>
        </x-table.page-header>

        {{-- FILTROS --}}
        <div class="px-3 pt-3">
            <x-table.filters.form
                data-dynamic-filter
                data-target="#locations-table"
                :fields="[
                    ['name' => 'name', 'placeholder' => 'Filtrar por nome do local...'],
                    ['name' => 'institution_name', 'placeholder' => 'Filtrar por instituição...'],
                    ['name' => 'is_active', 'type' => 'select', 'options' => [
                        '' => 'Status (Todos)',
                        '1' => 'Ativo',
                        '0' => 'Inativo'
                    ]]
                ]"
            />
        </div>

        {{-- TABELA --}}
        <div id="locations-table" class="p-3">
            @include('pages.inclusive-radar.locations.partials.table')
        </div>

    </div>

    @push('scripts')
        @vite('resources/js/components/dynamicFilters.js')
    @endpush
@endsection
