@extends('layouts.master')

@section('title', 'Treinamentos')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Treinamentos' => route('inclusive-radar.trainings.index'),
        ]"/>
    </div>

    {{-- CARD UNIFICADO --}}
    <div class="custom-table-card shadow-sm border rounded-3 overflow-hidden">

        {{-- HEADER --}}
        <x-table.page-header
            title="Treinamentos"
            subtitle="Gerenciamento de treinamentos vinculados aos recursos."
        >
            {{-- Botão de ação --}}
            <x-buttons.link-button
                :href="route('inclusive-radar.trainings.create')"
                variant="new"
                title="Adicionar Treinamento"
            >
                <i class="fas fa-plus"></i>
            </x-buttons.link-button>
        </x-table.page-header>

        {{-- FILTROS --}}
        <div class="px-3 pt-3">
            <x-table.filters.form
                data-dynamic-filter
                data-target="#trainings-table"
                :fields="[
                    ['name' => 'title', 'placeholder' => 'Filtrar por nome do treinamento...'],
                    ['name' => 'is_active', 'type' => 'select', 'options' => [
                        '' => 'Status (Todos)',
                        '1' => 'Ativo',
                        '0' => 'Inativo'
                    ]],
                ]"
            />
        </div>

        {{-- TABELA --}}
        <div id="trainings-table" class="p-3">
            @include('pages.inclusive-radar.trainings.partials.table', ['trainings' => $trainings])
        </div>

    </div>

    @push('scripts')
        @vite('resources/js/components/dynamicFilters.js')
    @endpush
@endsection
