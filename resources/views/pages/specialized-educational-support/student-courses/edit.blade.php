@extends('layouts.app')

@section('content')

    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Alunos' => route('specialized-educational-support.students.index'),
            $studentCourse->student->person->name => route('specialized-educational-support.students.show', $studentCourse->student),
            'Matrículas' => route('specialized-educational-support.student-courses.history', $studentCourse->student),
            $studentCourse->course->name => route('specialized-educational-support.student-courses.show', $studentCourse),
            'Editar' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3 align-items-center">
        <div>
            <h2 class="text-title">Editar Matrícula</h2>
            <p class="text-muted">Atualize as informações acadêmicas desta matrícula.</p>
        </div>

        <x-buttons.link-button
            href="{{ route('specialized-educational-support.student-courses.show', $studentCourse) }}"
            variant="secondary"
            aria-label="Cancelar edição da matrícula">
            <i class="fas fa-times" aria-hidden="true"></i> Cancelar
        </x-buttons.link-button>
    </div>

    <div class="mt-3">
        <x-forms.form-card
            action="{{ route('specialized-educational-support.student-courses.update', $studentCourse) }}"
            method="POST">
            @method('PUT')

            <x-forms.section title="Dados do Aluno e Curso" />

            {{-- aluno somente leitura --}}
            <x-show.info-item label="Aluno" column="col-md-6" isBox="true">
                <strong>{{ $studentCourse->student->person->name }}</strong>
            </x-show.info-item>

            {{-- curso somente leitura --}}
            <x-show.info-item label="Curso / Série" column="col-md-6" isBox="true">
                <strong>{{ $studentCourse->course->name }}</strong>
            </x-show.info-item>
            <input type="hidden" name="course_id" value="{{ $studentCourse->course->id }}">

            <x-forms.section title="Dados da Matrícula" />

            <div class="col-md-6">
                <x-forms.input
                    name="academic_year"
                    label="Ano Letivo *"
                    type="number"
                    required
                    aria-label="Ano letivo da matrícula"
                    :value="old('academic_year', $studentCourse->academic_year)" />
            </div>

            <div class="col-md-6 d-flex align-items-center pt-4">
                <div class="form-check">
                    <input
                        class="form-check-input"
                        type="checkbox"
                        name="is_current"
                        value="1"
                        id="is_current"
                        {{ old('is_current', $studentCourse->is_current) ? 'checked' : '' }}
                        aria-label="Definir matrícula como curso atual">
                    <label class="form-check-label" for="is_current">
                        Curso Atual
                    </label>
                </div>
            </div>

            <div class="col-12 d-flex justify-content-end gap-3 border-t pt-4 px-4 pb-4">

                <x-buttons.submit-button
                    type="submit"
                    aria-label="Salvar alterações da matrícula">
                    <i class="fas fa-save" aria-hidden="true"></i> Atualizar Matrícula
                </x-buttons.submit-button>
            </div>

        </x-forms.form-card>
    </div>
@endsection