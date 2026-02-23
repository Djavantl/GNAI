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


    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Responsáveis — {{ $student->person->name }}</h2>
            <p class="text-muted">Gerenciamento de vínculos familiares e contatos de emergência.</p>
        </div>
        <div class="d-flex gap-2 align-items-start">
            <x-buttons.link-button
                :href="route('specialized-educational-support.students.show', $student)"
                variant="secondary"
            >
                <i class="fas fa-arrow-left"></i> Voltar
            </x-buttons.link-button>

            <x-buttons.link-button
                :href="route('specialized-educational-support.guardians.create', $student)"
                variant="new"
            >
               <i class="fas fa-plus"></i> Adicionar
            </x-buttons.link-button>
        </div>
    </div>

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

    <div id="guardians-table">
        @include('pages.specialized-educational-support.guardians.partials.table')
    </div>

    @push('scripts')
        @vite('resources/js/components/dynamicFilters.js')
    @endpush
    
@endsection