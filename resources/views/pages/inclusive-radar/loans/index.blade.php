@extends('layouts.master')

@section('title', 'Empréstimos')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Empréstimos' => route('inclusive-radar.loans.index'),
        ]" />
    </div>

    {{-- CARD UNIFICADO --}}
    <div class="custom-table-card shadow-sm border rounded-3 overflow-hidden">

        {{-- HEADER --}}
        <x-table.page-header
            title="Empréstimos de Recursos"
            subtitle="Controle de saídas e devoluções de tecnologias e materiais pedagógicos."
        >
            {{-- Botão de ação --}}
            <x-buttons.link-button
                :href="route('inclusive-radar.loans.create')"
                variant="new"
            >
                <i class="fas fa-plus"></i> Adicionar
            </x-buttons.link-button>
        </x-table.page-header>

        {{-- FILTROS --}}
        <div class="px-3 pt-3">
            <x-table.filters.form
                data-dynamic-filter
                data-target="#loans-table"
                :fields="[
                    ['name' => 'item', 'placeholder' => 'Filtrar por item...'],
                    ['name' => 'student', 'placeholder' => 'Filtrar por aluno...'],
                    ['name' => 'professional', 'placeholder' => 'Filtrar por profissional...'],
                    ['name' => 'status', 'type' => 'select', 'options' => [
                        ''         => 'Status (Todos)',
                        'active'   => 'Ativo (Com o Beneficiário)',
                        'returned' => 'Devolvido (No prazo)',
                        'late'     => 'Devolvido (Com atraso)',
                        'damaged'  => 'Devolvido (Com avaria)',
                    ]],
                ]"
            />
        </div>

        {{-- TABELA --}}
        <div id="loans-table" class="p-3">
            @include('pages.inclusive-radar.loans.partials.table')
        </div>

    </div>

    @push('scripts')
        @vite('resources/js/components/dynamicFilters.js')
    @endpush
@endsection
