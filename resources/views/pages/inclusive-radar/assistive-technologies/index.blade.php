@extends('layouts.master')

@section('title', 'Tecnologias Assistivas')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Tecnologias Assistivas' => route('inclusive-radar.assistive-technologies.index'),
        ]" />
    </div>

    <div class="custom-table-card shadow-sm border rounded-3 overflow-hidden">
        <x-table.page-header
            title="Tecnologias Assistivas"
            subtitle="Gerenciamento de periféricos, softwares e equipamentos de acessibilidade."
        >
            <x-buttons.link-button
                :href="route('inclusive-radar.assistive-technologies.create')"
                variant="new"
                title="Adicionar Tecnologias Assistivas"
            >
                <i class="fas fa-plus"></i>
            </x-buttons.link-button>
        </x-table.page-header>

        <div class="px-3 pt-3">
            <x-table.filters.form
                data-dynamic-filter
                data-target="#assistive-technologies-table"
                :fields="[
                    ['name' => 'name', 'placeholder' => 'Filtrar por nome...'],
                    ['name' => 'type', 'placeholder' => 'Filtrar por tipo...'],
                    ['name' => 'is_digital', 'type' => 'select', 'options' => [
                        '' => 'Natureza (Todos)',
                        '1' => 'Digital',
                        '0' => 'Físico'
                    ]],
                    ['name' => 'is_active', 'type' => 'select', 'options' => [
                        '' => 'Status (Todos)',
                        '1' => 'Ativo',
                        '0' => 'Inativo'
                    ]],
                    ['name' => 'available', 'type' => 'select', 'options' => [
                        '' => 'Disponibilidade (Todos)',
                        '1' => 'Disponível',
                        '0' => 'Indisponível'
                    ]]
                ]"
            />
        </div>

        <div id="assistive-technologies-table" class="p-3">
            @include('pages.inclusive-radar.assistive-technologies.partials.table')
        </div>
    </div>

    @push('scripts')
        @vite('resources/js/components/dynamicFilters.js')
    @endpush
@endsection
