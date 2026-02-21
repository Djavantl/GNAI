@extends('layouts.master')

@section('title', 'Pontos de Referência')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Pontos de Referência' => route('inclusive-radar.locations.index'),
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3 align-items-center">
        <div>
            <h2 class="text-title">Pontos de Referência</h2>
            <p class="text-muted text-base">
                Gerencie os prédios, salas e locais específicos dentro de cada instituição.
            </p>
        </div>

        <x-buttons.link-button
            :href="route('inclusive-radar.locations.create')"
            variant="new"
        >
            <i class="fas fa-plus"></i> Adicionar
        </x-buttons.link-button>
    </div>

    {{-- 🔎 Filtros (versão limpa e moderna) --}}
    <x-table.filters.form
        data-dynamic-filter
        data-target="#locations-table"
        :fields="[
            [
                'name' => 'name',
                'placeholder' => 'Filtrar por nome do local...'
            ],
            [
                'name' => 'institution_name',
                'placeholder' => 'Filtrar por instituição...'
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

    {{-- 📋 Tabela --}}
    <div id="locations-table">
        @include('pages.inclusive-radar.locations.partials.table')
    </div>

    @push('scripts')
        @vite('resources/js/components/dynamicFilters.js')
    @endpush
@endsection
