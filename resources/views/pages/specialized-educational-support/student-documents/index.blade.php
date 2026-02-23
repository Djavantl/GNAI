@extends('layouts.master')

@section('title', 'Documentos do Aluno')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Alunos' => route('specialized-educational-support.students.index'),
            'Documentos' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3 align-items-center">
        <div>
            <h2 class="text-title mb-0">Documentos de {{ $student->person->name }}</h2>
            <p class="text-muted">Gestão de laudos, relatórios e planos de AEE</p>
        </div>
        
        <x-buttons.link-button
            :href="route('specialized-educational-support.student-documents.create', $student)"
            variant="new"
        >
             <i class="fas fa-plus" aria-hidden="true"></i> Adicionar 
        </x-buttons.link-button>
    </div>

    :x-table.filters.form
    
     <x-table.filters.form
        data-dynamic-filter
        data-target="#documents-table"
        :fields="[
            [
                'name' => 'title',
                'placeholder' => 'Buscar por título...'
            ],
             [
                'name' => 'type',
                'type' => 'select',
                'options' => $types
            ],
            [
                'name' => 'semester_id',
                'type' => 'select',
                'options' => $semesters
            ],
            [
                'name' => 'version',
                'type' => 'select',
                'options' => $versions
            ],
            
        ]"
    />

    <div id="documents-table">
        @include('pages.specialized-educational-support.student-documents.partials.table')
    </div>

    @push('scripts')
        @vite('resources/js/components/dynamicFilters.js')
    @endpush
    
@endsection