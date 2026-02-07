@extends('layouts.app')

@section('content')
    <h2 class="text-title">Matricular Aluno em Curso</h2>

    <x-forms.form-card action="{{ route('specialized-educational-support.student-courses.store') }}" method="POST">

        <div class="col-md-6">
            <x-forms.select name="student_id" label="Selecionar Aluno *" required
                :options="$students->pluck('person.name', 'id')->toArray()" :value="old('student_id')" />
        </div>

        <div class="col-md-6">
            <x-forms.select name="course_id" label="Curso / Série *" required
                :options="$courses->pluck('name', 'id')->toArray()" :value="old('course_id')" />
        </div>

        <div class="col-md-4">
            <x-forms.input name="academic_year" label="Ano Letivo *" type="number" required :value="old('academic_year', date('Y'))" />
        </div>

        <div class="col-md-4">
            <x-forms.select name="status" label="Status Inicial" :options="['active' => 'Ativo', 'completed' => 'Concluído']" value="active" />
        </div>

        <div class="col-md-4 d-flex align-items-center pt-4">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="is_current" value="1" id="is_current" checked>
                <label class="form-check-label" for="is_current">Definir como Curso Atual</label>
            </div>
        </div>

        <div class="col-12 d-flex justify-content-end gap-3 pt-4">
            <x-buttons.link-button href="{{ route('specialized-educational-support.students.index') }}" variant="secondary">Voltar</x-buttons.link-button>
            <x-buttons.submit-button type="submit">Efetivar Matrícula</x-buttons.submit-button>
        </div>
    </x-forms.form-card>
@endsection
