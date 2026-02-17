@extends('layouts.master')

@section('title', 'Materiais Pedagógicos Acessíveis')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Materiais Pedagógicos Acessíveis' => route('inclusive-radar.accessible-educational-materials.index'),
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3">
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
            Novo Material
        </x-buttons.link-button>
    </div>

    {{-- FILTROS --}}
    <x-table.filters
        data-dynamic-filter
        data-target="#materials-table"
        :fields="[
            [
                'name' => 'name',
                'label' => 'Nome',
                'placeholder' => 'Digite o nome'
            ],
            [
                'name' => 'type',
                'label' => 'Tipo',
                'placeholder' => 'Digite o tipo'
            ],
            [
                'name' => 'is_digital',
                'label' => 'Natureza',
                'type' => 'select',
                'options' => [
                    '' => 'Todos',
                    '1' => 'Digital',
                    '0' => 'Físico',
                ]
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
            [
                'name' => 'available',
                'label' => 'Disponibilidade',
                'type' => 'select',
                'options' => [
                    '' => 'Todos',
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
