@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-title">Cadastrar Novo Semestre</h2>
            <p class="text-muted">Defina o período letivo e sua vigência no sistema.</p>
        </div>
        <x-buttons.link-button :href="route('specialized-educational-support.semesters.index')" variant="secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </x-buttons.link-button>
    </div>

    <div class="custom-table-card bg-white shadow-sm">
        <form action="{{ route('specialized-educational-support.semesters.store') }}" method="POST" class="p-4">
            @csrf

            <div class="row g-3">
                <x-forms.section title="Identificação do Período" />

                <div class="col-md-4">
                    <x-forms.input name="year" label="Ano Letivo" type="number" :value="old('year', date('Y'))" required />
                </div>

                <div class="col-md-4">
                    <x-forms.input name="term" label="Período (Ex: 1 ou 2)" type="number" :value="old('term', 1)" min="1" required />
                </div>

                <div class="col-md-4">
                    <x-forms.input name="label" label="Rótulo Exibido" placeholder="Ex: 2026.1" :value="old('label')" />
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

                <div class="col-12 border-top mt-4 pt-4 d-flex gap-2">
                    <x-buttons.submit-button variant="success">
                        <i class="fas fa-save"></i> Salvar Semestre
                    </x-buttons.submit-button>
                    
                    <x-buttons.link-button :href="route('specialized-educational-support.semesters.index')" variant="secondary">
                        Cancelar
                    </x-buttons.link-button>
                </div>
            </div>
        </form>
    </div>
@endsection