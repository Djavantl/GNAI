@extends('layouts.master')

@section('title', 'Instituições')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Instituições' => route('inclusive-radar.institutions.index'),
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Instituições Base</h2>
            <p class="text-muted">
                Gerencie os locais centrais onde o radar de acessibilidade opera.
            </p>
        </div>

        <x-buttons.link-button
            :href="route('inclusive-radar.institutions.create')"
            variant="new"
        >
            <i class="fas fa-plus"></i> Adicionar
        </x-buttons.link-button>
    </div>

    <x-table.filters.form
        data-dynamic-filter
        data-target="#institutions-table"
        :fields="[
        [
            'name' => 'name',
            'placeholder' => 'Filtrar por nome...'
        ],
        [
            'name' => 'location',
            'placeholder' => 'Filtrar por cidade ou estado...'
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

    {{-- 📋 TABELA --}}
    <div id="institutions-table">
        @include('pages.inclusive-radar.institutions.partials.table')
    </div>

    @push('scripts')
        @vite('resources/js/components/dynamicFilters.js')
    @endpush
@endsection
