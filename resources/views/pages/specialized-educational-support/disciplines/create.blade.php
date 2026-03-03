@extends('layouts.app')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Disciplinas' => route('specialized-educational-support.disciplines.index'),
            'Cadastrar' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Cadastrar Disciplina</h2>
            <p class="text-muted">Defina o nome e descrição da matéria para o catálogo da instituição.</p>
        </div>
        <x-buttons.link-button href="{{ route('specialized-educational-support.disciplines.index') }}" variant="secondary">
            <i class="fas fa-times"></i> Cancelar
        </x-buttons.link-button>
    </div>

    <div class="mt-3">
        <x-forms.form-card action="{{ route('specialized-educational-support.disciplines.store') }}" method="POST">
            
            <x-forms.section title="Informações Básicas" />

            <div class="col-md-12">
                <x-forms.input name="name" label="Nome da Disciplina " required :value="old('name')" />
            </div>

            <div class="col-md-12">
                <x-forms.textarea name="description" label="Descrição" rows="3" :value="old('description')" />
            </div>

            <div class="col-md-6">
                <x-forms.select name="is_active" label="Status" :options="[1 => 'Ativo', 0 => 'Inativo']" :value="old('is_active', 1)" />
            </div>

            <div class="col-12 d-flex justify-content-end gap-3 border-t pt-4 px-4 pb-4">
                <x-buttons.link-button href="{{ route('specialized-educational-support.disciplines.index') }}" variant="secondary">
                    <i class="fas fa-times"></i> Cancelar
                </x-buttons.link-button>
                <x-buttons.submit-button type="submit" class="btn-action new submit">
                    <i class="fas fa-save"></i> Salvar
                </x-buttons.submit-button>
            </div>
        </x-forms.form-card>
    </div>
@endsection