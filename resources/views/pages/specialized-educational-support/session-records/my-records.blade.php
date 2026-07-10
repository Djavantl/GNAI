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
            title="Atendimentos AEE"
            subtitle="Histórico de registros dos seus atendimentos AEE."
        >
        </x-table.page-header>

        <div class="px-3 pt-3">
            <x-table.filters.form
                data-dynamic-filter
                data-target="#my-records-table"
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

        <div id="my-records-table" class="p-3">
            @include('pages.specialized-educational-support.session-records.partials.my-table')
        </div>
    </div>

    @push('scripts')
        @vite('resources/js/components/dynamicFilters.js')
    @endpush
@endsection
