@extends('layouts.master')

@section('title', 'Alunos')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Alunos' => null
        ]" />
    </div>
 
    {{-- CARD UNIFICADO --}}
    <div class="custom-table-card shadow-sm border rounded-3 overflow-hidden">
        {{-- HEADER --}}
        <x-table.page-header
            title="Alunos"
            subtitle="Gerencie os estudantes e seus documentos de apoio especializado."
        >
            {{-- Botão de ação --}}
            @can('student.create')
                <x-buttons.link-button
                    :href="route('specialized-educational-support.students.create')"
                    variant="new"
                    title="Adicionar alunos"
                >
                    <i class="fas fa-plus"></i>
                </x-buttons.link-button>
            @endcan
        </x-table.page-header>

        <div class="px-3 pt-3">
            <x-table.filters.form
                data-dynamic-filter
                data-target="#students-table"
                :fields="[
                    [
                        'name' => 'name',
                        'placeholder' => 'Nome do aluno...'
                    ],
                    [
                        'name' => 'phone',
                        'placeholder' => 'Telefone...'
                    ],
                    [
                        'name' => 'email',
                        'placeholder' => 'Email...'
                    ],
                    [
                        'name' => 'status',
                        'type' => 'select',
                        'options' => collect(
                            \App\Domains\SpecializedEducationalSupport\Domain\Enums\StudentStatus::cases()
                        )->mapWithKeys(
                            fn ($status) => [$status->value => $status->label()]
                        )->prepend('Status (Todos)', '')->all()
                    ],
                ]"
            />
        </div>
        <div id="students-table" class="p-3">
            @include('pages.specialized-educational-support.students.partials.table')
        </div>
    </div>
    @push('scripts')
        @vite('resources/js/components/dynamicFilters.js')
    @endpush
@endsection
