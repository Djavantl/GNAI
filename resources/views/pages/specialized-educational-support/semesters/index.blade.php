@extends('layouts.master')

@section('title', 'Gestão de Semestres')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Semestres' => null
        ]" />
    </div>

    {{-- CARD UNIFICADO --}}
    <div class="custom-table-card shadow-sm border rounded-3 overflow-hidden">
        {{-- HEADER --}}
        <x-table.page-header
            title="Semestres Letivos"
            subtitle="Configuração de períodos para organização dos atendimentos e relatórios."
        >
            <x-buttons.link-button
                :href="route('specialized-educational-support.semesters.create')"
                variant="new"
                title="Novo semestre"
            >
                <i class="fas fa-plus"></i>
            </x-buttons.link-button>
        </x-table.page-header>

        {{-- FILTROS --}}
        <div class="px-3 pt-3">
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
        </div>

        {{-- TABELA --}}
        <div id="semesters-table" class="p-3">
            @include('pages.specialized-educational-support.semesters.partials.table')
        </div>

        {{-- INFO --}}
        <div class="px-3 pb-3">
            <div class="alert alert-light border small text-muted mb-0">
                <i class="fas fa-info-circle mr-1"></i>
                O <strong>Semestre Atual</strong> determina qual período será selecionado por padrão em novos registros do sistema.
            </div>
        </div>
    </div>

    @push('scripts')
        @vite('resources/js/components/dynamicFilters.js')
    @endpush
@endsection