@extends('layouts.master')

@section('title', 'Histórico de Contextos')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Alunos' => route('specialized-educational-support.students.index'),
            $student->person->name => route('specialized-educational-support.students.show', $student),
            'Contextos' => null
        ]" />
    </div>

    {{-- CARD UNIFICADO --}}
    <div class="custom-table-card shadow-sm border rounded-3 overflow-hidden">
        {{-- HEADER --}}
        <x-table.page-header
            title="Histórico de Contextos"
            subtitle="Veja o histórico completo das características comportamentais observadas do aluno(a) {{ $student->person->name }}."
        >
            <div class="d-flex gap-2">
                <x-buttons.link-button
                    :href="route('specialized-educational-support.students.show', $student)"
                    variant="secondary"
                >
                    <i class="fas fa-arrow-left"></i>Voltar
                </x-buttons.link-button>

                @can('student-context.create')
                    @if($contexts->isEmpty())
                        <x-buttons.link-button
                            :href="route('specialized-educational-support.student-context.create', $student->id)"
                            variant="new"
                            title="Adicionar contexto"
                        >
                            <i class="fas fa-plus"></i>
                        </x-buttons.link-button>
                    @else
                        <x-buttons.link-button
                            :href="route('specialized-educational-support.student-context.new-version', $student->id)"
                            variant="new"
                            title="Nova versão"
                        >
                            <i class="fas fa-plus"></i>
                        </x-buttons.link-button>
                    @endif
                @endcan
            </div>
        </x-table.page-header>

        {{-- FILTROS --}}
        <div class="px-3 pt-3">
            <x-table.filters.form
                data-dynamic-filter
                data-target="#contexts-table"
                :fields="[
                    [
                        'name' => 'semester_id',
                        'type' => 'select',
                        'options' => $semesters
                    ],
                    [
                        'name' => 'evaluation_type',
                        'type' => 'select',
                        'options' => [
                            '' => 'Tipo (Todos)',
                            'initial' => 'Inicial',
                            'periodic_review' => 'Revisão Periódica'
                        ]
                    ],
                    [
                        'name' => 'is_current',
                        'type' => 'select',
                        'options' => [
                            '' => 'Status (Todos)',
                            '1' => 'Atual',
                            '0' => 'Antigo'
                        ]
                    ],
                ]"
            />
        </div>

        {{-- TABELA --}}
        <div id="contexts-table" class="p-3">
            @include('pages.specialized-educational-support.student-context.partials.table')
        </div>
    </div>

    @push('scripts')
        @vite('resources/js/components/dynamicFilters.js')
    @endpush

@endsection