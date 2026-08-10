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
            subtitle="Cadastre profissionais ou tutores de pares no sistema. Embora esta seção seja denominada “Profissionais”, também é possível cadastrar e gerenciar tutores de pares, utilizando a mesma estrutura de dados e funcionalidades disponíveis."
        >
        @can('professional.create')
            <x-buttons.link-button
                :href="route('specialized-educational-support.professionals.create')"
                variant="new"
                title="Adicionar profissional"
            >
                <i class="fas fa-plus"></i>
            </x-buttons.link-button>
        @endcan
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
                        'name' => 'registration',
                        'placeholder' => 'Matrícula...'
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
                        'options' => ['' => 'Status (Todos)'] +
                            $professionalStatuses->all()
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
