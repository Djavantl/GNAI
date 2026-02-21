@extends('layouts.master')

@section('title', 'Materiais Pedagógicos Acessíveis')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Materiais Pedagógicos Acessíveis' => route('inclusive-radar.accessible-educational-materials.index'),
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3 align-items-center">
        <div>
            <h2 class="text-title">Materiais Pedagógicos Acessíveis</h2>
            <p class="text-muted text-base">
                Gestão de recursos didáticos, livros e jogos adaptados.
            </p>
        </div>

        <x-buttons.link-button
            :href="route('inclusive-radar.accessible-educational-materials.create')"
            variant="new"
        >
            <i class="fas fa-plus"></i> Adicionar
        </x-buttons.link-button>
    </div>

    {{-- FILTROS --}}
    <x-table.filters.form
        data-dynamic-filter
        data-target="#materials-table"
        :fields="[
        [
            'name' => 'name',
            'placeholder' => 'Filtrar por nome...'
        ],
        [
            'name' => 'type',
            'placeholder' => 'Filtrar por tipo...'
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
        [
            'name' => 'available',
            'type' => 'select',
            'options' => [
                '' => 'Disponibilidade (Todos)',
                '1' => 'Disponível',
                '0' => 'Indisponível'
            ]
        ],
    ]"
    />

    {{-- TABELA --}}
    <div id="materials-table">
        @include('pages.inclusive-radar.accessible-educational-materials.partials.table')
    </div>

    @push('scripts')
        @vite('resources/js/components/dynamicFilters.js')
    @endpush
@endsection
