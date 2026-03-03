@extends('layouts.master')

@section('title', 'Disciplinas')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Disciplinas' => null
        ]" />
    </div>

    {{-- CARD UNIFICADO --}}
    <div class="custom-table-card shadow-sm border rounded-3 overflow-hidden">
        {{-- HEADER --}}
        <x-table.page-header
            title="Disciplinas"
            subtitle="Gerencie as disciplinas cadastradas no sistema."
        >
            <x-buttons.link-button
                :href="route('specialized-educational-support.disciplines.create')"
                variant="new"
                title="Adicionar disciplina"
            >
                <i class="fas fa-plus"></i>
            </x-buttons.link-button>
        </x-table.page-header>

        {{-- FILTROS --}}
        <div class="px-3 pt-3">
            <x-table.filters.form
                data-dynamic-filter
                data-target="#disciplines-table"
                :fields="[
                    [
                        'name' => 'name',
                        'placeholder' => 'Nome da disciplina...'
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
        <div id="disciplines-table" class="p-3">
            @include('pages.specialized-educational-support.disciplines.partials.table')
        </div>
    </div>

    @push('scripts')
        @vite('resources/js/components/dynamicFilters.js')
    @endpush
@endsection