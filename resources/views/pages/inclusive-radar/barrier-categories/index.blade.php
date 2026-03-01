@extends('layouts.master')

@section('title', 'Categorias de Barreiras')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Categorias de Barreiras' => route('inclusive-radar.barrier-categories.index'),
        ]" />
    </div>

    {{-- CARD UNIFICADO --}}
    <div class="custom-table-card shadow-sm border rounded-3 overflow-hidden">

        {{-- HEADER --}}
        <x-table.page-header
            title="Categorias de Barreiras"
            subtitle="Classificação para o mapeamento de acessibilidade e identificação de obstáculos."
        >
            {{-- Botão de ação --}}
            <x-buttons.link-button
                :href="route('inclusive-radar.barrier-categories.create')"
                variant="new"
            >
                <i class="fas fa-plus"></i> Adicionar
            </x-buttons.link-button>
        </x-table.page-header>

        {{-- FILTROS --}}
        <div class="px-3 pt-3">
            <x-table.filters.form
                data-dynamic-filter
                data-target="#barrier-categories-table"
                :fields="[
                    ['name' => 'name', 'placeholder' => 'Filtrar por nome da categoria...'],
                    ['name' => 'is_active', 'type' => 'select', 'options' => [
                        '' => 'Status (Todos)',
                        '1' => 'Ativo',
                        '0' => 'Inativo'
                    ]]
                ]"
            />
        </div>

        {{-- TABELA --}}
        <div id="barrier-categories-table" class="p-3">
            @include('pages.inclusive-radar.barrier-categories.partials.table')
        </div>

    </div>

    @push('scripts')
        @vite('resources/js/components/dynamicFilters.js')
    @endpush
@endsection
