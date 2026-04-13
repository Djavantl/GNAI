@extends('layouts.master')

@section('title', 'Minhas Pendências')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Pendências' => route('specialized-educational-support.pendencies.index'),
            'Minhas Pendências' => null
        ]" />
    </div>

    <div class="custom-table-card shadow-sm border rounded-3 overflow-hidden">
        <x-table.page-header
            title="Minhas Pendências"
            subtitle="Pendências atribuídas a você como responsável."
        >
            <x-buttons.link-button
                :href="route('specialized-educational-support.pendencies.index')"
                variant="secondary"
                title="Voltar para pendências"
            >
                <i class="fas fa-arrow-left"></i>
            </x-buttons.link-button>
        </x-table.page-header>

        <div class="px-3 pt-3">
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
        </div>

        <div id="pendencies-my-table" class="p-3">
            @include('pages.specialized-educational-support.pendencies.partials.table-my')
        </div>
    </div>

    @push('scripts')
        @vite('resources/js/components/dynamicFilters.js')
    @endpush
@endsection