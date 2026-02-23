@extends('layouts.master')

@section('title', 'Pendências')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Pendências' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="text-title mb-0">Pendências</h2>

        <div class="d-flex gap-2">
            <x-buttons.link-button
                :href="route('specialized-educational-support.pendencies.my')"
                variant="info"
            >
                <i class="fas fa-user-check"></i> Minhas Pendências
            </x-buttons.link-button>
            
            <x-buttons.link-button 
                :href="route('specialized-educational-support.pendencies.create')" 
                variant="new">
               <i class="fas fa-plus" aria-hidden="true"></i> Adicionar
            </x-buttons.link-button>

            
        </div>
    </div>

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

    <div id="pendencies-table">
        @include('pages.specialized-educational-support.pendencies.partials.table')
    </div>

    @push('scripts')
        @vite('resources/js/components/dynamicFilters.js')
    @endpush
@endsection
