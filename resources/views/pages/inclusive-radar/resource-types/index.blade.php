@extends('layouts.master')

@section('title', 'Tipos de Recursos')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Tipos de Recursos' => route('inclusive-radar.resource-types.index'),
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3 align-items-center">
        <div>
            <h2 class="text-title">Tipos de Recursos</h2>
            <p class="text-muted text-base">Definição de categorias e naturezas para Tecnologias e Materiais.</p>
        </div>
        <x-buttons.link-button
            :href="route('inclusive-radar.resource-types.create')"
            variant="new"
        >
            <i class="fas fa-plus"></i> Adicionar
        </x-buttons.link-button>
    </div>

    <x-table.filters.form
        data-dynamic-filter
        data-target="#resource-types-table"
        :fields="[
        [
            'name' => 'name',
            'placeholder' => 'Filtrar por nome...'
        ],
        [
            'name' => 'is_digital',
            'type' => 'select',
            'options' => [
                '' => 'Natureza (Todos)',
                '1' => 'Digital',
                '0' => 'Físico',
            ]
        ],
        [
            'name' => 'is_active',
            'type' => 'select',
            'options' => [
                '' => 'Status (Todos)',
                '1' => 'Ativo',
                '0' => 'Inativo'
            ]
        ],
    ]"
    />

    {{-- Tabela --}}
    <div id="resource-types-table">
        @include('pages.inclusive-radar.resource-types.partials.table')
    </div>

    @push('scripts')
        @vite('resources/js/components/dynamicFilters.js')
    @endpush
@endsection
