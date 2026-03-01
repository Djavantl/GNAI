@extends('layouts.master')

@section('title', 'Pendências')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Pendências' => null
        ]" />
    </div>

    {{-- CARD UNIFICADO --}}
    <div class="custom-table-card shadow-sm border rounded-3 overflow-hidden">
        {{-- HEADER --}}
        <x-table.page-header
            title="Pendências"
            subtitle="Gerencie as pendências e seus responsáveis."
        >
            <div class="d-flex gap-2">
                <x-buttons.link-button
                    :href="route('specialized-educational-support.pendencies.my')"
                    variant="info"
                >
                    <i class="fas fa-user-check"></i> Minhas Pendências
                </x-buttons.link-button>

                <x-buttons.link-button 
                    :href="route('specialized-educational-support.pendencies.create')" 
                    variant="new"
                    title="Adicionar pendência"
                >
                    <i class="fas fa-plus"></i>
                </x-buttons.link-button>
            </div>
        </x-table.page-header>

        {{-- FILTROS --}}
        <div class="px-3 pt-3">
            <x-table.filters.form
                data-dynamic-filter
                data-target="#pendencies-table"
                :fields="[
                    [
                        'name' => 'title',
                        'placeholder' => 'Buscar por título...'
                    ],
                    [
                        'name' => 'assigned_to',
                        'type' => 'select',
                        'options' => ['' => 'Profissional (Todos)'] +
                            collect($professionals)->mapWithKeys(fn($p) => [
                                $p->id => $p->person->name
                            ])->toArray()
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

        {{-- TABELA --}}
        <div id="pendencies-table" class="p-3">
            @include('pages.specialized-educational-support.pendencies.partials.table')
        </div>
    </div>

    @push('scripts')
        @vite('resources/js/components/dynamicFilters.js')
    @endpush
@endsection