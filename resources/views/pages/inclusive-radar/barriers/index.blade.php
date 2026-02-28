@extends('layouts.master')

@section('title', 'Barreiras')

@section('content')

    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Barreiras' => route('inclusive-radar.barriers.index'),
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3 align-items-center">
        <div>
            <h2 class="text-title">Mapa de Barreiras</h2>
            <p class="text-muted text-base">
                Contribuições da comunidade para uma instituição mais acessível.
            </p>
        </div>

        <x-buttons.link-button
            :href="route('inclusive-radar.barriers.create')"
            variant="new"
        >
            <i class="fas fa-plus"></i> Adicionar
        </x-buttons.link-button>
    </div>

    {{-- 🔎 Filtros (padrão TA) --}}
    <x-table.filters.form
        data-dynamic-filter
        data-target="#barriers-table"
        :fields="[
            [
                'name' => 'name',
                'placeholder' => 'Filtrar por nome da barreira...'
            ],
            [
                'name' => 'category',
                'placeholder' => 'Filtrar por categoria...'
            ],
            [
                'name' => 'priority',
                'type' => 'select',
                'options' => collect(\App\Enums\Priority::cases())
                    ->mapWithKeys(fn ($case) => [
                        $case->value => $case->label()
                    ])
                    ->prepend('Prioridade (Todas)', '')
                    ->toArray(),
            ],
            [
                'name' => 'status',
                'type' => 'select',
                'options' => collect(\App\Enums\InclusiveRadar\BarrierStatus::cases())
                    ->mapWithKeys(fn ($case) => [
                        $case->value => $case->label()
                    ])
                    ->prepend('Status (Todos)', '')
                    ->toArray(),
            ],
        ]"
    />

    {{-- Tabela --}}
    <div id="barriers-table">
        @include('pages.inclusive-radar.barriers.partials.table')
    </div>

    @push('scripts')
        @vite('resources/js/components/dynamicFilters.js')
    @endpush

@endsection
