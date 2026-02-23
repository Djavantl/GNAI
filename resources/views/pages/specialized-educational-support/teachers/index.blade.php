.@extends('layouts.master')

@section('title', 'Professores')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Professores' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3 align-items-center">
        <div>
            <h2 class="text-title mb-0">Professores</h2>
            <p class="text-muted">Gerencie o corpo docente e suas atribuições de disciplinas.</p>
        </div>
        <div class="d-flex gap-2">
            {{-- Botão para Gerenciar Permissões Globais --}}
            <x-buttons.link-button
                :href="route('specialized-educational-support.teachers.permissions')"
                variant="secondary"
            >
                <i class="fas fa-shield-alt"></i> Permissões Para Professores
            </x-buttons.link-button>

            <x-buttons.link-button
                :href="route('specialized-educational-support.teachers.create')"
                variant="new"
            >
                <i class="fas fa-plus"></i> Adicionar Professor
            </x-buttons.link-button>
        </div>
    </div>

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

    <div id="teachers-table2">
        @include('pages.specialized-educational-support.teachers.partials.table')
    </div>

    @push('scripts')
        @vite('resources/js/components/dynamicFilters.js')
    @endpush
@endsection