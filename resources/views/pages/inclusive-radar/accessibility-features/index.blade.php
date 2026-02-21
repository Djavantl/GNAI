@extends('layouts.master')

@section('title', 'Recursos de Acessibilidade')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Recursos de Acessibilidade' => route('inclusive-radar.accessibility-features.index')
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3 align-items-center">
        <div>
            <h2 class="text-title">Recursos de Acessibilidade</h2>
            <p class="text-muted text-base">
                Gestão de serviços, adaptações e recursos promotores de acessibilidade.
            </p>
        </div>

        <x-buttons.link-button
            :href="route('inclusive-radar.accessibility-features.create')"
            variant="new"
        >
            <i class="fas fa-plus"></i> Adicionar
        </x-buttons.link-button>
    </div>

    {{-- FILTROS --}}
    <x-table.filters.form
        data-dynamic-filter
        data-target="#features-table"
        :fields="[
        [
            'name' => 'name',
            'placeholder' => 'Filtrar por nome...'
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

    {{-- TABELA --}}
    <div id="features-table">
        @include('pages.inclusive-radar.accessibility-features.partials.table')
    </div>

    @push('scripts')
        @vite('resources/js/components/dynamicFilters.js')
    @endpush
@endsection
