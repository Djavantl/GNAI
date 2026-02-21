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
            <i class="fas fa-plus"></i> Adicionar
        </x-buttons.link-button>
    </div>

    <x-table.filters.form
        data-dynamic-filter
        data-target="#loans-table"
        :fields="[
        [
            'name' => 'item',
            'placeholder' => 'Filtrar por item...'
        ],
        [
            'name' => 'student',
            'placeholder' => 'Filtrar por aluno...'
        ],
        [
            'name' => 'professional',
            'placeholder' => 'Filtrar por profissional...'
        ],
        [
            'name' => 'status',
            'type' => 'select',
            'options' => [
                '' => 'Status (Todos)',
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
