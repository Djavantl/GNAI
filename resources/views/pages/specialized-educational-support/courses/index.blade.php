@extends('layouts.master')

@section('title', 'Cursos')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Cursos' => null
        ]" />
    </div>

    {{-- CARD UNIFICADO --}}
    <div class="custom-table-card shadow-sm border rounded-3 overflow-hidden">
        {{-- HEADER --}}
        <x-table.page-header
            title="Cursos e Séries"
            subtitle="Gerencie cursos e suas respectivas séries."
        >
            <x-buttons.link-button
                :href="route('specialized-educational-support.courses.create')"
                variant="new"
                title="Adicionar curso"
            >
                <i class="fas fa-plus"></i>
            </x-buttons.link-button>
        </x-table.page-header>

        {{-- FILTROS --}}
        <div class="px-3 pt-3">
            <x-table.filters.form
                data-dynamic-filter
                data-target="#courses-table"
                :fields="[
                    [
                        'name' => 'name',
                        'placeholder' => 'Buscar por nome do curso...'
                    ],
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
        <div id="courses-table" class="p-3">
            @include('pages.specialized-educational-support.courses.partials.table')
        </div>
    </div>

    @push('scripts')
        @vite('resources/js/components/dynamicFilters.js')
    @endpush
    
@endsection