@extends('layouts.master')

@section('title', 'Alunos')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Alunos' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3 align-items-center">
        <div>
            <h2 class="text-title mb-0">Alunos</h2>
            <p class="text-muted">Gerencie os estudantes e seus documentos de apoio especializado.</p>
        </div>
        <x-buttons.link-button
            :href="route('specialized-educational-support.students.create')"
            variant="new"
        >
            <i class="fas fa-plus"></i>Adicionar
        </x-buttons.link-button>
    </div>

    <x-ui.search
        url="{{ route('specialized-educational-support.students.index') }}"
        placeholder="Buscar por nome, email, matrícula, status..."
        :semester="true"
        :semesters="$semesters"
        target="#students-table"
    />

    <div id="students-table">
        @include('pages.specialized-educational-support.students.partials.table')
    </div>
    @push('scripts')
        @vite('resources/js/components/search-filter.js')
    @endpush
@endsection
