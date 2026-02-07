@extends('layouts.app')

@section('content')
    <h2 class="text-title">Cadastrar Novo Curso</h2>

    <x-forms.form-card action="{{ route('specialized-educational-support.courses.store') }}" method="POST">
        <x-forms.section title="Dados do Curso" />
        <div class="col-md-12">
            <x-forms.input name="name" label="Nome do Curso *" required :value="old('name')" />
        </div>
        <div class="col-md-12">
            <x-forms.textarea name="description" label="Descrição" :value="old('description')" />
        </div>

        <x-forms.section title="Grade Inicial" />
        <div class="col-md-12">
            <div class="row px-3">
                @foreach($disciplines as $discipline)
                    <div class="col-md-4 mb-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="discipline_ids[]" value="{{ $discipline->id }}" id="disc_{{ $discipline->id }}">
                            <label class="form-check-label" for="disc_{{ $discipline->id }}">{{ $discipline->name }}</label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="col-12 d-flex justify-content-end gap-3 pt-4 px-4 pb-4">
            <x-buttons.link-button href="{{ route('specialized-educational-support.courses.index') }}" variant="secondary">Cancelar</x-buttons.link-button>
            <x-buttons.submit-button type="submit" class="btn-action new submit px-5">Salvar Curso</x-buttons.submit-button>
        </div>
    </x-forms.form-card>
@endsection
