@extends('layouts.app')

@section('content')
    <h2 class="text-title">Editar Matrícula / Histórico</h2>

    <x-forms.form-card action="{{ route('specialized-educational-support.student-courses.update', $studentCourse) }}" method="POST">
        @method('PUT')

        <div class="col-md-12 mb-3">
            <p>Aluno: <strong>{{ $studentCourse->student->person->name }}</strong></p>
            <p>Curso: <strong>{{ $studentCourse->course->name }}</strong></p>
        </div>

        <div class="col-md-6">
            <x-forms.input name="academic_year" label="Ano Letivo *" type="number" required :value="old('academic_year', $studentCourse->academic_year)" />
        </div>

        <div class="col-md-6">
            <x-forms.select name="status" label="Status da Matrícula"
                :options="['active' => 'Ativo', 'completed' => 'Concluído', 'dropped' => 'Evadido']"
                :value="old('status', $studentCourse->status)" />
        </div>

        <div class="col-md-6 pt-4">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="is_current" value="1" id="is_current" {{ $studentCourse->is_current ? 'checked' : '' }}>
                <label class="form-check-label" for="is_current">Curso Atual (Vigente)</label>
            </div>
        </div>

        <div class="col-12 d-flex justify-content-end gap-3 pt-4">
            <x-buttons.link-button href="{{ route('specialized-educational-support.student-courses.history', $studentCourse->student_id) }}" variant="secondary">
                Voltar
            </x-buttons.link-button>
            <x-buttons.submit-button type="submit">Atualizar Histórico</x-buttons.submit-button>
        </div>
    </x-forms.form-card>
@endsection
