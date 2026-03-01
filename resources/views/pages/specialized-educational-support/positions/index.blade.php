@extends('layouts.master')

@section('title', 'Cargos')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Cargos' => null
        ]" />
    </div>

    {{-- CARD UNIFICADO --}}
    <div class="custom-table-card shadow-sm border rounded-3 overflow-hidden">
        {{-- HEADER --}}
        <x-table.page-header
            title="Lista de Cargos"
            subtitle="Gerenciamento de funções para o suporte especializado."
        >
            <x-buttons.link-button
                :href="route('specialized-educational-support.positions.create')"
                variant="new"
                title="Adicionar cargo"
            >
                <i class="fas fa-plus"></i>
            </x-buttons.link-button>
        </x-table.page-header>

        {{-- FILTROS --}}
        <div class="px-3 pt-3">
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
        </div>

        {{-- TABELA --}}
        <div id="positions-table" class="p-3">
            @include('pages.specialized-educational-support.positions.partials.table')
        </div>
    </div>

    @push('scripts')
        @vite('resources/js/components/dynamicFilters.js')
    @endpush
@endsection