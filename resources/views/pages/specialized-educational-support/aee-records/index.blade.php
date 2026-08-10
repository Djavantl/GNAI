@extends('layouts.master')

@section('title', 'Todos os Atendimentos Especializados')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Atendimentos Especializados' => null,
        ]" />
    </div>

    <div class="custom-table-card shadow-sm border rounded-3 overflow-hidden">
        <x-table.page-header
            title="Todos os Atendimentos Especializados"
            subtitle="Visão geral dos registros de atendimento AEE dos profissionais permitidos."
        >
            @can('aee-record.view-own')
                <x-buttons.link-button :href="route('specialized-educational-support.aee-records.my-records')" variant="secondary">
                    <i class="bi bi-person me-1"></i> Meus Atendimentos
                </x-buttons.link-button>
            @endcan
        </x-table.page-header>

        <div class="px-3 pt-3">
            <x-table.filters.form
                data-dynamic-filter
                data-target="#aee-records-table"
                :fields="[
                    [
                        'name' => 'student',
                        'type' => 'select',
                        'options' => ['' => 'Estudante (Todos)'] + collect($students)->mapWithKeys(fn ($student) => [
                            $student->id => $student->person->name ?? ('ID ' . $student->id),
                        ])->toArray(),
                    ],
                    [
                        'name' => 'professional_id',
                        'type' => 'select',
                        'options' => ['' => 'Profissional (Todos)'] + collect($professionals)->mapWithKeys(fn ($professional) => [
                            $professional->id => $professional->person->name ?? ('ID ' . $professional->id),
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

        <div id="aee-records-table" class="p-3">
            @include('pages.specialized-educational-support.aee-records.partials.table')
        </div>
    </div>

    @push('scripts')
        @vite('resources/js/components/dynamicFilters.js')
    @endpush
@endsection
