@extends('layouts.master')

@section('title', 'Deficiências')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Deficiências' => null
        ]" />
    </div>

    {{-- CARD UNIFICADO --}}
    <div class="custom-table-card shadow-sm border rounded-3 overflow-hidden">
        {{-- HEADER --}}
        <x-table.page-header
            title="Lista de Deficiências"
            subtitle="Categorias e códigos CID registrados."
        >
            <x-buttons.link-button
                :href="route('specialized-educational-support.deficiencies.create')"
                variant="new"
                title="Adicionar deficiência"
            >
                <i class="fas fa-plus"></i>
            </x-buttons.link-button>
        </x-table.page-header>

        {{-- FILTROS --}}
        <div class="px-3 pt-3">
            <x-table.filters.form
                data-dynamic-filter
                data-target="#deficiencies-table"
                :fields="[
                    ['name' => 'name', 'placeholder' => 'Nome da deficiência...'],
                    ['name' => 'cid_code', 'placeholder' => 'Código CID...'],
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
        <div id="deficiencies-table" class="p-3">
            @include('pages.specialized-educational-support.deficiencies.partials.table')
        </div>
    </div>

    @push('scripts')
        @vite('resources/js/components/dynamicFilters.js')
    @endpush
@endsection