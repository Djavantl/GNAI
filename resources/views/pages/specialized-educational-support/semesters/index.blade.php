@extends('layouts.master')

@section('title', 'Gestão de Semestres')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Semestres' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Semestres Letivos</h2>
            <p class="text-muted">Configuração de períodos para organização dos atendimentos e relatórios.</p>
        </div>
        <x-buttons.link-button
            :href="route('specialized-educational-support.semesters.create')"
            variant="new"
        >
            <i class="fas fa-plus"></i>Novo Semestre
        </x-buttons.link-button>
    </div>
    <x-table.filters.form
        data-dynamic-filter
        data-target="#semesters-table"
        :fields="[
            [
                'name' => 'year',
                'placeholder' => 'Ano...'
            ],
            [
                'name' => 'term',
                'type' => 'select',
                'options' => [
                    '' => 'Termo (Todos)',
                    1 => '1º semestre',
                    2 => '2º semestre'
                ]
            ],
            [
                'name' => 'label',
                'placeholder' => 'Label...'
            ],
            [
                'name' => 'is_current',
                'type' => 'select',
                'options' => [
                    '' => 'Status (Todos)',
                    1 => 'Atual',
                    0 => 'Não atual'
                ]
            ]
        ]"
    />

    <div id="semesters-table">
        @include('pages.specialized-educational-support.semesters.partials.table')
    </div>
    

    <div class="mt-4">
        <div class="alert alert-light border small text-muted">
            <i class="fas fa-info-circle mr-1"></i>
            O <strong>Semestre Atual</strong> determina qual período será selecionado por padrão em novos registros do sistema.
        </div>
    </div>
    @push('scripts')
        @vite('resources/js/components/dynamicFilters.js')
    @endpush
@endsection