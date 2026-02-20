@extends('layouts.master')

@section('title', 'Tecnologias Assistivas')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
        'Home' => route('dashboard'),
        'Tecnologias Assistivas' => route('inclusive-radar.assistive-technologies.index'),
    ]" />
    </div>

    <div class="d-flex justify-content-between mb-3 align-items-center">
        <div>
            <h2 class="text-title">Tecnologias Assistivas</h2>
            <p class="text-muted text-base">Gerenciamento de periféricos, softwares e equipamentos de acessibilidade.</p>
        </div>
        <x-buttons.link-button
            :href="route('inclusive-radar.assistive-technologies.create')"
            variant="new"
        >
            Nova Tecnologia
        </x-buttons.link-button>
    </div>

    <x-table.filters
        data-dynamic-filter
        data-target="#assistive-technologies-table"
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

    {{-- Tabela --}}
    <div id="assistive-technologies-table">
        @include('pages.inclusive-radar.assistive-technologies.partials.table')
    </div>
    @push('scripts')
        @vite('resources/js/components/dynamicFilters.js')
    @endpush
@endsection
