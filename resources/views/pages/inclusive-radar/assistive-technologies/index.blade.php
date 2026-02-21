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
            <i class="fas fa-plus"></i> Adicionar
        </x-buttons.link-button>
    </div>

    {{-- 🔎 Filtros (versão limpa e moderna) --}}
    <x-table.filters.form
        data-dynamic-filter
        data-target="#assistive-technologies-table"
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

    {{-- Tabela --}}
    <div id="assistive-technologies-table">
        @include('pages.inclusive-radar.assistive-technologies.partials.table')
    </div>
    @push('scripts')
        @vite('resources/js/components/dynamicFilters.js')
    @endpush
@endsection
