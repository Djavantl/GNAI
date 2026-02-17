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
            Nova Instituição
        </x-buttons.link-button>
    </div>

    {{-- 🔎 FILTROS --}}
    <x-table.filters
        data-dynamic-filter
        data-target="#institutions-table"
        :fields="[
            [
                'name' => 'name',
                'label' => 'Nome',
                'placeholder' => 'Digite o nome'
            ],
            [
                'name' => 'location',
                'label' => 'Cidade / Estado',
                'placeholder' => 'Digite cidade ou estado'
            ],
            [
                'name' => 'is_active',
                'label' => 'Status',
                'type' => 'select',
                'options' => [
                    '' => 'Todos',
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
