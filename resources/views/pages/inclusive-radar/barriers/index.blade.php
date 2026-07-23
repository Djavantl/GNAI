@extends('layouts.master')

@section('title', 'Barreiras')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Barreiras' => route('inclusive-radar.barriers.index'),
        ]" />
    </div>

    <div class="custom-table-card shadow-sm border rounded-3 overflow-hidden">
        <x-table.page-header
            title="Mapa de Barreiras"
            subtitle="Contribuições da comunidade para uma instituição mais acessível."
        >
            @can('barrier.create')
                <x-buttons.link-button
                    :href="route('inclusive-radar.barriers.create')"
                    variant="new"
                    title="Adicionar Barreiras"
                >
                    <i class="fas fa-plus"></i>
                </x-buttons.link-button>
            @endcan
        </x-table.page-header>

        <div class="px-3 pt-3">
            <x-table.filters.form
                data-dynamic-filter
                data-target="#barriers-table"
                :fields="[
                    ['name' => 'name', 'placeholder' => 'Filtrar por nome da barreira...'],
                    ['name' => 'category', 'placeholder' => 'Filtrar por categoria...'],
                    ['name' => 'priority', 'type' => 'select', 'options' => collect(\App\Enums\Priority::cases())
                        ->mapWithKeys(fn ($case) => [$case->value => $case->label()])
                        ->prepend('Prioridade (Todas)', '')
                        ->toArray()],
                    ['name' => 'status', 'type' => 'select', 'options' => collect(\App\Domains\InclusiveRadar\Domain\Enums\BarrierStatus::cases())
                        ->mapWithKeys(fn ($case) => [$case->value => $case->label()])
                        ->prepend('Status (Todos)', '')
                        ->toArray()],
                ]"
            />
        </div>

        <div id="barriers-table" class="p-3">
            @include('pages.inclusive-radar.barriers.partials.table')
        </div>
    </div>

    @push('scripts')
        @vite('resources/js/components/dynamicFilters.js')
    @endpush
@endsection
