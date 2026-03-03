@extends('layouts.master')

@section('title', 'Recursos de Acessibilidade')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Recursos de Acessibilidade' => route('inclusive-radar.accessibility-features.index')
        ]" />
    </div>

    {{-- CARD UNIFICADO --}}
    <div class="custom-table-card shadow-sm border rounded-3 overflow-hidden">

        {{-- HEADER --}}
        <x-table.page-header
            title="Recursos de Acessibilidade"
            subtitle="Gestão de serviços, adaptações e recursos promotores de acessibilidade."
        >
            {{-- Botão de ação --}}
            <x-buttons.link-button
                :href="route('inclusive-radar.accessibility-features.create')"
                variant="new"
                title="Adicionar Recursos de Acessibilidade"
            >
                <i class="fas fa-plus"></i>
            </x-buttons.link-button>
        </x-table.page-header>

        {{-- FILTROS --}}
        <div class="px-3 pt-3">
            <x-table.filters.form
                data-dynamic-filter
                data-target="#features-table"
                :fields="[
                    ['name' => 'name', 'placeholder' => 'Filtrar por nome...'],
                    ['name' => 'is_active', 'type' => 'select', 'options' => [
                        '' => 'Status (Todos)',
                        '1' => 'Ativo',
                        '0' => 'Inativo'
                    ]]
                ]"
            />
        </div>

        {{-- TABELA --}}
        <div id="features-table" class="p-3">
            @include('pages.inclusive-radar.accessibility-features.partials.table')
        </div>

    </div>

    @push('scripts')
        @vite('resources/js/components/dynamicFilters.js')
    @endpush
@endsection
