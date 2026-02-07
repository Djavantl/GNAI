@extends('layouts.app')

@section('content')
    <h2 class="text-title">Editar Curso: {{ $course->name }}</h2>

    <x-forms.form-card action="{{ route('specialized-educational-support.courses.update', $course) }}" method="POST">
        @method('PUT')

        <x-forms.section title="Informações Básicas" />
        <div class="col-md-12">
            <x-forms.input name="name" label="Nome do Curso *" required :value="old('name', $course->name)" />
        </div>

        <x-forms.section title="Grade Curricular (Selecione as Disciplinas)" />
        <div class="col-md-12">
            <div class="row px-3">
                @foreach($disciplines as $discipline)
                    <div class="col-md-4 mb-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="discipline_ids[]" value="{{ $discipline->id }}"
                                id="disc_{{ $discipline->id }}" {{ $course->disciplines->contains($discipline->id) ? 'checked' : '' }}>
                            <label class="form-check-label" for="disc_{{ $discipline->id }}">{{ $discipline->name }}</label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ADICIONE ESTE CAMPO ABAIXO --}}
        <div class="col-md-6">
            <x-forms.select
                name="is_active"
                label="Status do Curso *"
                :options="[1 => 'Ativo', 0 => 'Inativo']"
                :selected="old('is_active', $course->is_active)"
            />
        </div>

        <div class="col-12 d-flex justify-content-end gap-3 pt-4">
            <x-buttons.link-button href="{{ route('specialized-educational-support.courses.index') }}" variant="secondary">Cancelar</x-buttons.link-button>
            <x-buttons.submit-button type="submit">Atualizar Curso</x-buttons.submit-button>
        </div>
    </x-forms.form-card>
@endsection
