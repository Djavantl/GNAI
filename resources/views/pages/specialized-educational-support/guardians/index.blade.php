@extends('layouts.master')

@section('title', 'Responsáveis do Aluno')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Alunos' => route('specialized-educational-support.students.index'),
            $student->person->name => route('specialized-educational-support.students.show', $student),
            'Responsáveis' => null
        ]" />
    </div>

    {{-- CARD UNIFICADO --}}
    <div class="custom-table-card shadow-sm border rounded-3 overflow-hidden">
        {{-- HEADER --}}
        <x-table.page-header
            title="Responsáveis — {{ $student->person->name }}"
            subtitle="Gerenciamento de vínculos familiares e contatos de emergência."
        >
            <div class="d-flex gap-2">
                <x-buttons.link-button
                    :href="route('specialized-educational-support.students.show', $student)"
                    variant="secondary"
                >
                    <i class="fas fa-arrow-left"></i> Voltar
                </x-buttons.link-button>

                <x-buttons.link-button
                    :href="route('specialized-educational-support.guardians.create', $student)"
                    variant="new"
                    title="Adicionar responsável"
                >
                    <i class="fas fa-plus"></i>
                </x-buttons.link-button>
            </div>
        </x-table.page-header>

        {{-- FILTROS --}}
        <div class="px-3 pt-3">
            <x-table.filters.form
                data-dynamic-filter
                data-target="#guardians-table"
                :fields="[
                    [
                        'name' => 'name',
                        'placeholder' => 'Nome do responsável...'
                    ],
                    [
                        'name' => 'email',
                        'placeholder' => 'E-mail...'
                    ],
                    [
                        'name' => 'relationship',
                        'type' => 'select',
                        'options' => ['' => 'Parentesco (Todos)'] + $relationships
                    ],
                ]"
            />
        </div>

        {{-- TABELA --}}
        <div id="guardians-table" class="p-3">
            @include('pages.specialized-educational-support.guardians.partials.table')
        </div>
    </div>

    @push('scripts')
        @vite('resources/js/components/dynamicFilters.js')
    @endpush
    
@endsection