@extends('layouts.master')

@section('title', 'Vínculos de Atributos')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Vínculos de Atributos' => route('inclusive-radar.type-attribute-assignments.index'),
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3 align-items-center">
        <div>
            <h2 class="text-title">Vínculos de Atributos</h2>
            <p class="text-muted text-base">Gerencie quais campos técnicos cada tipo de recurso deve possuir no formulário.</p>
        </div>
        <x-buttons.link-button
            :href="route('inclusive-radar.type-attribute-assignments.create')"
            variant="new"
        >
            <i class="fas fa-plus"></i> Adicionar
        </x-buttons.link-button>
    </div>

    <x-table.filters.form
        data-dynamic-filter
        data-target="#assignments-table"
        :fields="[
        [
            'name' => 'type_name',
            'placeholder' => 'Filtrar por tipo de recurso...'
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

    <div id="assignments-table">
        @include('pages.inclusive-radar.attribute-assignments.partials.table')
    </div>

    @push('scripts')
        @vite('resources/js/components/dynamicFilters.js')
    @endpush
@endsection
