@extends('layouts.master')

@section('title', 'Categorias de Barreiras')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Categorias de Barreiras' => route('inclusive-radar.barrier-categories.index'),
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3 align-items-center">
        <div>
            <h2 class="text-title">Categorias de Barreiras</h2>
            <p class="text-muted text-base">Classificação para o mapeamento de acessibilidade e identificação de obstáculos.</p>
        </div>
        <x-buttons.link-button
            :href="route('inclusive-radar.barrier-categories.create')"
            variant="new"
        >
            <i class="fas fa-plus"></i> Adicionar
        </x-buttons.link-button>
    </div>

    <x-table.filters.form
        data-dynamic-filter
        data-target="#barrier-categories-table"
        :fields="[
        [
            'name' => 'name',
            'placeholder' => 'Filtrar por nome da categoria...'
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
    <div id="barrier-categories-table">
        @include('pages.inclusive-radar.barrier-categories.partials.table')
    </div>

    @push('scripts')
        @vite('resources/js/components/dynamicFilters.js')
    @endpush
@endsection
