@extends('layouts.master')

@section('title', 'Atendimentos AEE')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Atendimentos AEE' => null
        ]" />
    </div>

    <div class="custom-table-card shadow-sm border rounded-3 overflow-hidden">
        <x-table.page-header
            title="Meus Atendimentos Especializados"
            subtitle="Histórico de registros dos seus atendimentos AEE."
        >
            @can('aee-record.view-all')
                <x-buttons.link-button :href="route('specialized-educational-support.aee-records.index')" variant="secondary">
                    <i class="bi bi-people me-1"></i> Todos os Atendimentos
                </x-buttons.link-button>
            @endcan
        </x-table.page-header>

        <div class="px-3 pt-3">
            <x-table.filters.form
                data-dynamic-filter
                data-target="#my-records-table"
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
                ]"
            />
        </div>

        <div id="my-records-table" class="p-3">
            @include('pages.specialized-educational-support.aee-records.partials.table')
        </div>
    </div>

    @push('scripts')
        @vite('resources/js/components/dynamicFilters.js')
    @endpush
@endsection
