@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-title">Editar Semestre</h2>
            <p class="text-muted">Alterando: {{ $semester->label ?? $semester->year . '.' . $semester->term }}</p>
        </div>
        <x-buttons.link-button :href="route('specialized-educational-support.semesters.index')" variant="secondary">
            Voltar
        </x-buttons.link-button>
    </div>

    <div class="custom-table-card bg-white shadow-sm">
        <form action="{{ route('specialized-educational-support.semesters.update', $semester) }}" method="POST" class="p-4">
            @csrf
            @method('PUT')

            <div class="row g-3">
                <x-forms.section title="Identificação do Período" />

                <div class="col-md-4">
                    <x-forms.input name="year" label="Ano Letivo" type="number" :value="old('year', $semester->year)" required />
                </div>

                <div class="col-md-4">
                    <x-forms.input name="term" label="Período" type="number" :value="old('term', $semester->term)" required />
                </div>

                <div class="col-md-4">
                    <x-forms.input name="label" label="Rótulo" :value="old('label', $semester->label)" />
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

                <div class="col-12 border-top mt-4 pt-4 d-flex gap-2">
                    <x-buttons.submit-button variant="warning">
                        <i class="fas fa-sync"></i> Atualizar Semestre
                    </x-buttons.submit-button>
                    
                    <x-buttons.link-button :href="route('specialized-educational-support.semesters.index')" variant="secondary">
                        Cancelar
                    </x-buttons.link-button>
                </div>
            </div>
        </form>
    </div>
@endsection