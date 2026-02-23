@extends('layouts.master')

@section('title', 'Cargos')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Cargos' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Lista de Cargos</h2>
            <p class="text-muted">Gerenciamento de funções para o suporte especializado.</p>
        </div>
        <x-buttons.link-button
            :href="route('specialized-educational-support.positions.create')"
            variant="new"
        >
            <i class="fas fa-plus"></i>Adicionar
        </x-buttons.link-button>
    </div>

    <x-table.filters.form
        data-dynamic-filter
        data-target="#positions-table"
        :fields="[
            [
                'name' => 'name',
                'placeholder' => 'Buscar por nome...'
            ],
            [
                'name' => 'description',
                'placeholder' => 'Descrição...'
            ],
            [
                'name' => 'is_active',
                'type' => 'select',
                'options' => [
                    '' => 'Status (Todos)',
                    1 => 'Ativo',
                    0 => 'Inativo'
                ]
            ]
        ]"
    />

    <div id="positions-table">
        @include('pages.specialized-educational-support.positions.partials.table')
    </div>

    @push('scripts')
        @vite('resources/js/components/dynamicFilters.js')
    @endpush
@endsection