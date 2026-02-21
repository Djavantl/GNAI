@extends('layouts.master')

@section('title', 'Profissionais')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Profissionais' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3 align-items-center">
        <div>
            <h2 class="text-title mb-0">Profissionais</h2>
            <p class="text-muted">Gerencie os profissionais e seus documentos de apoio especializado.</p>
        </div>
        <x-buttons.link-button
            :href="route('specialized-educational-support.professionals.create')"
            variant="new"
        >
            <i class="fas fa-plus"></i>Adicionar
        </x-buttons.link-button>
    </div>

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


    <div id="professionals-table">
        @include('pages.specialized-educational-support.professionals.partials.table')
    </div>

    
    @push('scripts')
        @vite('resources/js/components/dynamicFilters.js')
    @endpush
@endsection
