@extends('layouts.app')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Semestres' => route('specialized-educational-support.semesters.index'),
            'Cadastrar' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Cadastrar Novo Semestre</h2>
            <p class="text-muted">Defina o período letivo e sua vigência no sistema.</p>
        </div>
        <x-buttons.link-button href="{{ route('specialized-educational-support.semesters.index') }}" variant="secondary">
            <i class="fas fa-times"></i>Cancelar
        </x-buttons.link-button>
    </div>

    <div class="mt-3">
        <x-forms.form-card action="{{ route('specialized-educational-support.semesters.store') }}" method="POST">

            <x-forms.section title="Identificação do Período" />

            <div class="col-md-6">
                <x-forms.input name="year" label="Ano Letivo" type="number" :value="old('year', date('Y'))" required />
            </div>

            <div class="col-md-6">
                <x-forms.input name="term" label="Período (Ex: 1 ou 2)" type="number" :value="old('term', 1)" min="1" required />
            </div>

            <x-forms.section title="Duração do Semestre" />

            <div class="col-md-6">
                <x-forms.input name="start_date" label="Data de Início" type="date" :value="old('start_date')" />
            </div>

            <div class="col-md-6">
                <x-forms.input name="end_date" label="Data de Término" type="date" :value="old('end_date')" />
            </div>

            <div class="col-12 mt-3">
                <x-forms.checkbox name="is_current" label="Definir este como o semestre atual do sistema" :checked="old('is_current')" />
            </div>

            <div class="col-12 d-flex justify-content-end gap-3 border-t pt-4 px-4 pb-4">
                <x-buttons.link-button href="{{ route('specialized-educational-support.semesters.index') }}" variant="secondary">
                    Cancelar
                </x-buttons.link-button>

                <x-buttons.submit-button type="submit" class="btn-action new submit">
                    <i class="fas fa-save"></i> Salvar
                </x-buttons.submit-button>
            </div>

        </x-forms.form-card>
    </div>
@endsection