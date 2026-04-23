@extends('layouts.master')

@section('title', 'Professores')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Professores' => null
        ]" />
    </div>

    {{-- CARD UNIFICADO --}}
    <div class="custom-table-card shadow-sm border rounded-3 overflow-hidden">
        {{-- HEADER --}}
        <x-table.page-header
            title="Professores"
            subtitle="Gerencie o corpo docente e suas atribuições de disciplinas."
        >
            <div class="d-flex gap-2">
                {{-- Permissões --}}
                @can('teacher.update')
                    <x-buttons.link-button
                        :href="route('specialized-educational-support.teachers.permissions')"
                        variant="secondary"
                    >
                        <i class="fas fa-shield-alt"></i> Permissões 
                    </x-buttons.link-button>
                @endcan

                {{-- Novo professor (padrão igual ao de alunos) --}}
                @can('teacher.create')
                    <x-buttons.link-button
                        :href="route('specialized-educational-support.teachers.create')"
                        variant="new"
                        title="Adicionar professor"
                    >
                        <i class="fas fa-plus"></i>
                    </x-buttons.link-button>
                @endcan
            </div>
        </x-table.page-header>

        {{-- FILTROS --}}
        <div class="px-3 pt-3">
            <x-table.filters.form
                data-dynamic-filter
                data-target="#teachers-table"
                :fields="[
                    [
                        'name' => 'name',
                        'placeholder' => 'Nome do Professor...'
                    ],
                    [
                        'name' => 'email',
                        'placeholder' => 'Email...'
                    ],
                    [
                        'name' => 'registration',
                        'placeholder' => 'Matrícula...'
                    ],
                ]"
            />
        </div>

        {{-- TABELA --}}
        <div id="teachers-table" class="p-3">
            @include('pages.specialized-educational-support.teachers.partials.table')
        </div>
    </div>

    @push('scripts')
        @vite('resources/js/components/dynamicFilters.js')
    @endpush
@endsection