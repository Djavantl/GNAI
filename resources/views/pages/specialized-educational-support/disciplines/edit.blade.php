@extends('layouts.app')

@section('content')
    <h2 class="text-title">Editar Disciplina</h2>

    <x-forms.form-card action="{{ route('specialized-educational-support.disciplines.update', $discipline) }}" method="POST">
        @method('PUT')
        <div class="col-md-12">
            <x-forms.input name="name" label="Nome *" required :value="old('name', $discipline->name)" />
        </div>
        <div class="col-md-12">
            <x-forms.textarea name="description" label="Descrição" :value="old('description', $discipline->description)" />
        </div>
        <div class="col-md-6">
            <x-forms.select name="is_active" label="Status" :options="[1 => 'Ativo', 0 => 'Inativo']" :selected="old('is_active', $discipline->is_active)" />
        </div>

        <div class="col-12 d-flex justify-content-end gap-3 pt-4">
            <x-buttons.link-button href="{{ route('specialized-educational-support.disciplines.index') }}" variant="secondary">Cancelar</x-buttons.link-button>
            <x-buttons.submit-button type="submit">Atualizar</x-buttons.submit-button>
        </div>
    </x-forms.form-card>
@endsection
