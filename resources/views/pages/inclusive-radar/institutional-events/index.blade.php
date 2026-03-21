@extends('layouts.master')

@section('title', 'Agenda Institucional')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Agenda Institucional' => route('inclusive-radar.institutional-events.index'),
        ]"/>
    </div>

    <div class="custom-table-card shadow-sm border rounded-3 overflow-hidden">

        <x-table.page-header
            title="Agenda Institucional"
            subtitle="Gerenciamento de eventos institucionais."
        >
            <x-buttons.link-button
                :href="route('inclusive-radar.institutional-events.create')"
                variant="new"
                title="Adicionar Evento"
            >
                <i class="fas fa-plus"></i>
            </x-buttons.link-button>
        </x-table.page-header>

        <div class="px-3 pt-3">
            <x-table.filters.form
                data-dynamic-filter
                data-target="#events-table"
                :fields="[
                    ['name' => 'title', 'placeholder' => 'Filtrar por nome do evento...'],
                    ['name' => 'is_active', 'type' => 'select', 'options' => [
                        '' => 'Status (Todos)',
                        '1' => 'Ativo',
                        '0' => 'Inativo'
                    ]],
                ]"
            />
        </div>

        <div id="events-table" class="p-3">
            @include('pages.inclusive-radar.institutional-events.partials.table', ['events' => $events])
        </div>
    </div>

    @push('scripts')
        @vite('resources/js/components/dynamicFilters.js')
    @endpush
@endsection
