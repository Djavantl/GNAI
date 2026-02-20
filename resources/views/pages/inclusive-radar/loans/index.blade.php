@extends('layouts.master')

@section('title', 'Empréstimos')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Empréstimos' => route('inclusive-radar.loans.index'),
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3 align-items-center">
        <div>
            <h2 class="text-title">Empréstimos de Recursos</h2>
            <p class="text-muted">Controle de saídas e devoluções de tecnologias e materiais pedagógicos.</p>
        </div>

        <x-buttons.link-button
            :href="route('inclusive-radar.loans.create')"
            variant="new"
        >
            Novo Empréstimo
        </x-buttons.link-button>
    </div>

    {{-- Filtros Dinâmicos --}}
    <x-table.filters
        data-dynamic-filter
        data-target="#loans-table"
        :fields="[
        [
            'name' => 'item',
            'label' => 'Item',
            'placeholder' => 'Digite o nome do item'
        ],
        [
            'name' => 'student',
            'label' => 'Aluno',
            'placeholder' => 'Digite o nome do aluno'
        ],
        [
            'name' => 'professional',
            'label' => 'Profissional',
            'placeholder' => 'Digite o nome do profissional'
        ],
        [
            'name' => 'status',
            'label' => 'Status',
            'type' => 'select',
            'options' => [
                '' => 'Todos',
                'active' => 'Ativo',
                'returned' => 'Devolvido',
                'late' => 'Em atraso',
                'damaged' => 'Danificado',
            ]
        ],
    ]"
    />

    {{-- Tabela de Empréstimos --}}
    <div id="loans-table">
        @include('pages.inclusive-radar.loans.partials.table')
    </div>

    @push('scripts')
        @vite('resources/js/components/dynamicFilters.js')
    @endpush
@endsection
