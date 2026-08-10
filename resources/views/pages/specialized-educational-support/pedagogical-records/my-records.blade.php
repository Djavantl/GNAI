@extends('layouts.master')

@section('title', 'Atendimentos Pedagógicos')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Atendimentos Pedagógicos' => null
        ]" />
    </div>

    <div class="custom-table-card shadow-sm border rounded-3 overflow-hidden">
        <x-table.page-header
            title="Meus Atendimentos Pedagógicos"
            subtitle="Histórico dos seus atendimentos pedagógicos."
        >
            @can('pedagogical-record.view-all')
                <x-buttons.link-button :href="route('specialized-educational-support.pedagogical-records.index')" variant="secondary">
                    <i class="bi bi-people me-1"></i> Todos os Atendimentos
                </x-buttons.link-button>
            @endcan
        </x-table.page-header>

        <div class="px-3 pt-3">
            <x-table.filters.form
                data-dynamic-filter
                data-target="#my-pedagogical-records-table"
                :fields="[
                    [
                        'name' => 'student',
                        'type' => 'select',
                        'options' => ['' => 'Estudante (Todos)'] + collect($students)->mapWithKeys(fn ($student) => [
                            $student->id => $student->person->name ?? ('ID ' . $student->id),
                        ])->toArray(),
                    ],
                    [
                        'name' => 'course_id',
                        'type' => 'select',
                        'options' => ['' => 'Curso (Todos)', 0 => 'Sem curso'] + collect($courses)->mapWithKeys(fn ($course) => [
                            $course->id => $course->name,
                        ])->toArray(),
                    ],
                    [
                        'name' => 'is_present',
                        'type' => 'select',
                        'options' => ['' => 'Presença (Todas)', '1' => 'Presente', '0' => 'Ausente'],
                    ],
                    [
                        'name' => 'with_guardians',
                        'type' => 'select',
                        'options' => ['' => 'Com responsáveis (Todos)', '1' => 'Sim', '0' => 'Não'],
                    ],
                ]"
            />
        </div>

        <div id="my-pedagogical-records-table" class="p-3">
            @include('pages.specialized-educational-support.pedagogical-records.partials.table')
        </div>
    </div>

    @push('scripts')
        @vite('resources/js/components/dynamicFilters.js')
    @endpush
@endsection
