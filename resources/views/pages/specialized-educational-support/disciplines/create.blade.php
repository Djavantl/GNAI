@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Cadastrar Disciplina</h2>
            <p class="text-muted">Defina o nome e descrição da matéria para o catálogo da instituição.</p>
        </div>
    </div>

    <x-forms.form-card action="{{ route('specialized-educational-support.disciplines.store') }}" method="POST">
        <div class="col-md-12">
            <x-forms.input name="name" label="Nome da Disciplina *" required :value="old('name')" />
        </div>
        <div class="col-md-12">
            <x-forms.textarea name="description" label="Descrição" :value="old('description')" />
        </div>
        <div class="col-md-6">
            <x-forms.select name="is_active" label="Status" :options="[1 => 'Ativo', 0 => 'Inativo']" :value="old('is_active', 1)" />
        </div>

        <div class="col-12 d-flex justify-content-end gap-3 pt-4">
            <x-buttons.link-button href="{{ route('specialized-educational-support.disciplines.index') }}" variant="secondary">Voltar</x-buttons.link-button>
            <x-buttons.submit-button type="submit" class="btn-action new submit px-5">Salvar</x-buttons.submit-button>
        </div>
    </x-forms.form-card>
@endsection
