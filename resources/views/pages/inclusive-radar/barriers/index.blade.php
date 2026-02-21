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
            <p class="text-muted text-base">Contribuições da comunidade para uma instituição mais acessível.</p>
        </div>
        <x-buttons.link-button
            :href="route('inclusive-radar.barriers.create')"
            variant="new"
        >
            Relatar Barreira
        </x-buttons.link-button>
    </div>

    {{-- Container para Filtros Dinâmicos seguindo o padrão TA --}}
    <div id="barriers-table">
        @include('pages.inclusive-radar.barriers.partials.table')
    </div>

    @push('scripts')
        @vite('resources/js/components/dynamicFilters.js')
    @endpush
@endsection
