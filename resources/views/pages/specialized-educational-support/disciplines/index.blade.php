@extends('layouts.master')

@section('title', 'Disciplinas')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Disciplinas' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3">
        <h2 class="text-title">Disciplinas</h2>
        <x-buttons.link-button :href="route('specialized-educational-support.disciplines.create')" variant="new">
            <i class="fas fa-plus"></i> Adicionar Disciplina
        </x-buttons.link-button>
    </div>

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

    <div id="disciplines-table">
        @include('pages.specialized-educational-support.disciplines.partials.table')
    </div>

    @push('scripts')
        @vite('resources/js/components/dynamicFilters.js')
    @endpush
@endsection