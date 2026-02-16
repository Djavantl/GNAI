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
             Adicionar Aluno
        </x-buttons.link-button>
    </div>

    {{-- Seção de Filtros --}}
    <x-table.filters :fields="[
        ['name' => 'name', 'label' => 'Nome', 'placeholder' => 'Digite o nome'],
        ['name' => 'email', 'label' => 'E-mail', 'placeholder' => 'Digite o e-mail'],
        ['name' => 'registration', 'label' => 'Matrícula', 'placeholder' => 'Digite a matrícula'],
        
    ]" />


    <div id="students-table">
        @include('pages.specialized-educational-support.students.partials.table')
    </div>
@endsection