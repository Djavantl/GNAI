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
            title="Atendimentos Pedagógicos"
            subtitle="Histórico dos seus atendimentos pedagógicos."
        />

        <div class="px-3 pt-3">
            <x-table.filters.form
                data-dynamic-filter
                data-target="#my-pedagogical-records-table"
                :fields="[
                    [
                        'name' => 'student',
                        'type' => 'select',
                        'options' => ['' => 'Aluno (Todos)'] +
                            collect($students)->mapWithKeys(fn($s) => [
                                $s->id => $s->person->name ?? ('ID ' . $s->id)
                            ])->toArray()
                    ],
                ]"
            />
        </div>

        <div id="my-pedagogical-records-table" class="p-3">
            @include('pages.specialized-educational-support.pedagogical-records.partials.my-table')
        </div>
    </div>

    @push('scripts')
        @vite('resources/js/components/dynamicFilters.js')
    @endpush
@endsection
