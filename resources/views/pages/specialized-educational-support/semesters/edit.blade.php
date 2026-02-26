@extends('layouts.app')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Semestres' => route('specialized-educational-support.semesters.index'),
            $semester->label => route('specialized-educational-support.semesters.show', $semester),
            'Editar' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Editar Semestre</h2>
            <p class="text-muted">Alterando: {{ $semester->label ?? $semester->year . '.' . $semester->term }}</p>
        </div>
        <x-buttons.link-button href="{{ route('specialized-educational-support.semesters.show', $semester) }}" variant="secondary">
            <i class="fas fa-times"></i>Cancelar
        </x-buttons.link-button>
    </div>

    <div class="mt-3">
        <x-forms.form-card action="{{ route('specialized-educational-support.semesters.update', $semester) }}" method="POST">
            @method('PUT')

            <x-forms.section title="Identificação do Período" />

            <div class="col-md-6">
                <x-forms.input name="year" label="Ano Letivo" type="number" :value="old('year', $semester->year)" required />
            </div>

            <div class="col-md-6">
                <x-forms.input name="term" label="Período" type="number" :value="old('term', $semester->term)" required />
            </div>

            <x-forms.section title="Duração do Semestre" />

            <div class="col-md-6">
                <x-forms.input name="start_date" label="Data de Início" type="date" :value="old('start_date', $semester->start_date ? $semester->start_date->format('Y-m-d') : '')" />
            </div>

            <div class="col-md-6">
                <x-forms.input name="end_date" label="Data de Término" type="date" :value="old('end_date', $semester->end_date ? $semester->end_date->format('Y-m-d') : '')" />
            </div>

            <div class="col-12 mt-3">
                <x-forms.checkbox name="is_current" label="Este é o semestre atual" :checked="old('is_current', $semester->is_current)" />
            </div>

            <div class="col-12 d-flex justify-content-end gap-3 border-t pt-4 px-4 pb-4">
                <x-buttons.link-button href="{{ route('specialized-educational-support.semesters.show', $semester) }}" variant="secondary">
                    <i class="fas fa-times"></i>Cancelar
                </x-buttons.link-button>

                <x-buttons.submit-button type="submit" class="btn-action new submit">
                    <i class="fas fa-save"></i>Salvar
                </x-buttons.submit-button>
            </div>

        </x-forms.form-card>
    </div>
@endsection