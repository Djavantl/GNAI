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

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Histórico de Contextos</h2>
            <p class="text-muted">Veja o histórico completo das características comportamentais observadas do aluno(a) {{ $student->person->name }}.</p>
        </div>
        <div>
            <x-buttons.link-button
                :href="route('specialized-educational-support.students.show', $student)"
                variant="secondary"
                class="me-3"
            >
                <i class="fas fa-arrow-left"></i> Voltar
            </x-buttons.link-button>
            @can('student-context.create')
                @if($contexts->isEmpty())

                    {{-- Primeiro contexto --}}
                    <x-buttons.link-button
                        href="{{ route('specialized-educational-support.student-context.create', $student->id) }}"
                        class="btn-action new">
                        <i class="fas fa-plus"></i>
                        Adicionar Contexto
                    </x-buttons.link-button>

                @else

                    {{-- Nova versão --}}
                    <x-buttons.link-button
                        href="{{ route('specialized-educational-support.student-context.new-version', $student->id) }}"
                        class="btn-action new">
                        <i class="fas fa-plus"></i>
                        Nova Versão
                    </x-buttons.link-button>

                @endif
            @endcan
        </div>
    </div>

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

    <div id="contexts-table">
        @include('pages.specialized-educational-support.student-context.partials.table')
    </div>

    @push('scripts')
        @vite('resources/js/components/dynamicFilters.js')
    @endpush

@endsection