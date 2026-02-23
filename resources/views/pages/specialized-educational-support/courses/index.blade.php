@extends('layouts.master')

@section('title', 'Cursos')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Cursos' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3">
        <h2 class="text-title">Cursos e Séries</h2>
        <div class="d-flex gap-2">
            <x-buttons.link-button :href="route('specialized-educational-support.courses.create')" variant="new">
                <i class="fas fa-plus"></i>Adicionar Curso
            </x-buttons.link-button>
        </div>
    </div>
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

    <div id="courses-table">
        @include('pages.specialized-educational-support.courses.partials.table')
    </div>

    @push('scripts')
        @vite('resources/js/components/dynamicFilters.js')
    @endpush
    
@endsection
