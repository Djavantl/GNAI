@extends('layouts.master')

@section('title', 'Deficiências')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Deficiências' => null
        ]" />
    </div>
    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Lista de Deficiências</h2>
            <p class="text-muted">Categorias e códigos CID registrados.</p>
        </div>
        <x-buttons.link-button
            :href="route('specialized-educational-support.deficiencies.create')"
            variant="new"
        >
             <i class="fas fa-plus"></i>Adiconar
        </x-buttons.link-button>
    </div>

    {{-- Filtros --}}
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

    <div id="deficiencies-table">
        @include('pages.specialized-educational-support.deficiencies.partials.table')
    </div>

    @push('scripts')
        @vite('resources/js/components/dynamicFilters.js')
    @endpush
@endsection