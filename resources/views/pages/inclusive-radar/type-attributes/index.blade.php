@extends('layouts.master')

@section('title', 'Atributos de Recursos')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Atributos de Recursos' => route('inclusive-radar.type-attributes.index'),
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3 align-items-center">
        <div>
            <h2 class="text-title">Atributos de Recursos</h2>
            <p class="text-muted text-base">Gerencie campos dinâmicos para detalhamento técnico dos recursos.</p>
        </div>
        <x-buttons.link-button
            :href="route('inclusive-radar.type-attributes.create')"
            variant="new"
        >
            <i class="fas fa-plus"></i> Adicionar
        </x-buttons.link-button>
    </div>

    <x-table.filters.form
        data-dynamic-filter
        data-target="#type-attributes-table"
        :fields="[
        [
            'name' => 'label',
            'placeholder' => 'Filtrar por rótulo...'
        ],
        [
            'name' => 'is_required',
            'type' => 'select',
            'options' => [
                '' => 'Obrigatório (Todos)',
                '1' => 'Sim',
                '0' => 'Não',
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

    {{-- Tabela --}}
    <div id="type-attributes-table">
        @include('pages.inclusive-radar.type-attributes.partials.table')
    </div>

    @push('scripts')
        @vite('resources/js/components/dynamicFilters.js')
    @endpush
@endsection
