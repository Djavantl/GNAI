@extends('layouts.master')

@section('title', 'Profissionais')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Profissionais' => null
        ]" />
    </div>

    {{-- CARD UNIFICADO --}}
    <div class="custom-table-card shadow-sm border rounded-3 overflow-hidden">
        {{-- HEADER --}}
        <x-table.page-header
            title="Profissionais"
            subtitle="Gerencie os profissionais e seus documentos de apoio especializado."
        >
            <x-buttons.link-button
                :href="route('specialized-educational-support.professionals.create')"
                variant="new"
                title="Adicionar profissional"
            >
                <i class="fas fa-plus"></i>
            </x-buttons.link-button>
        </x-table.page-header>

        {{-- FILTROS --}}
        <div class="px-3 pt-3">
            <x-table.filters.form
                data-dynamic-filter
                data-target="#professionals-table"
                :fields="[
                    [
                        'name' => 'name',
                        'placeholder' => 'Nome do Profissional...'
                    ],
                    [
                        'name' => 'email',
                        'placeholder' => 'Email...'
                    ],
                    [
                        'name' => 'position',
                        'type' => 'select',
                        'options' => ['' => 'Cargo (Todos)'] +
                            collect($positions)
                                ->mapWithKeys(fn($position) => [
                                    $position->id => $position->name
                                ])
                                ->toArray()
                    ],
                    [
                        'name' => 'status',
                        'type' => 'select',
                        'options' => [
                            '' => 'Status (Todos)',
                            'active' => 'Ativo',
                            'locked' => 'Trancado',
                            'completed' => 'Concluído',
                            'dropped' => 'Evadido',
                        ]
                    ],
                    [
                        'name' => 'semester',
                        'type' => 'select',
                        'options' => ['' => 'Semestre (Todos)'] +
                            collect($semesters)
                                ->mapWithKeys(fn($semester) => [
                                    $semester->id => $semester->label
                                ])
                                ->toArray()
                    ],
                ]"
            />
        </div>

        {{-- TABELA --}}
        <div id="professionals-table" class="p-3">
            @include('pages.specialized-educational-support.professionals.partials.table')
        </div>
    </div>

    @push('scripts')
        @vite('resources/js/components/dynamicFilters.js')
    @endpush
@endsection