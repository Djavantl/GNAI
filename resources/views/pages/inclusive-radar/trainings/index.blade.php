@extends('layouts.master')

@section('title', 'Treinamentos')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Treinamentos' => route('inclusive-radar.trainings.index'),
        ]"/>
    </div>

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Treinamentos</h2>
            <p class="text-muted text-base">Gerenciamento de treinamentos vinculados aos recursos.</p>
        </div>
        <x-buttons.link-button
            :href="route('inclusive-radar.trainings.create')"
            variant="new"
        >
            <i class="fas fa-plus"></i> Adicionar
        </x-buttons.link-button>
    </div>

    <x-table.filters.form
        data-dynamic-filter
        data-target="#trainings-table"
        :fields="[
        [
            'name' => 'title',
            'placeholder' => 'Filtrar por nome do treinamento...'
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
    <div id="trainings-table">
        @include('pages.inclusive-radar.trainings.partials.table', ['trainings' => $trainings])
    </div>

    @push('scripts')
        @vite('resources/js/components/dynamicFilters.js')
    @endpush
@endsection
