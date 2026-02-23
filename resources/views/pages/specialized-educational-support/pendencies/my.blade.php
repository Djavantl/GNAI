@extends('layouts.master')

@section('title', 'Minhas Pendências')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Pendências' => route('specialized-educational-support.pendencies.index'),
             auth()->user()->name => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Minhas Pendências</h2>
            <p class="text-muted">
                Pendências atribuídas a você como responsável.
            </p>
        </div>
    </div>

    <x-table.filters.form
        data-dynamic-filter
        data-target="#pendencies-my-table"
        :fields="[
            [
                'name' => 'title',
                'placeholder' => 'Buscar por título...'
            ],
            [
                'name' => 'priority',
                'type' => 'select',
                'options' => [
                    '' => 'Prioridade (Todas)',
                    'low' => 'Baixa',
                    'medium' => 'Média',
                    'high' => 'Alta',
                ]
            ],
            [
                'name' => 'is_completed',
                'type' => 'select',
                'options' => [
                    '' => 'Status (Todos)',
                    '0' => 'Pendentes',
                    '1' => 'Concluídas',
                ]
            ],
        ]"
    />


    <div id="pendencies-my-table">
        @include('pages.specialized-educational-support.pendencies.partials.table-my')
    </div>

    @push('scripts')
        @vite('resources/js/components/dynamicFilters.js')
    @endpush
@endsection
