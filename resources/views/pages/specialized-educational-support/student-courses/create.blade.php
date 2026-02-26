@extends('layouts.app')

@section('content')

    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Alunos' => route('specialized-educational-support.students.index'),
            $student->person->name => route('specialized-educational-support.students.show', $student),
            'Matrículas' => route('specialized-educational-support.student-courses.history', $student),
            'Cadastrar' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3 align-items-center">
        <div>
            <h2 class="text-title">Nova Matrícula</h2>
            <p class="text-muted">Vincule o aluno a um curso ou série.</p>
        </div>

        <x-buttons.link-button 
            href="{{ route('specialized-educational-support.student-courses.history', $student) }}" 
            variant="secondary"
            aria-label="Cancelar criação de matrícula">
            <i class="fas fa-times" aria-hidden="true"></i> Cancelar
        </x-buttons.link-button>
    </div>

    <div class="mt-3">
        <x-forms.form-card 
            action="{{ route('specialized-educational-support.student-courses.store', $student) }}" 
            method="POST">

            <x-forms.section title="Aluno" />

            <x-show.info-item label="Nome do Aluno" column="col-md-6" isBox="true">
                <span class="fw-bold">
                    {{ $student->person->name }}
                </span>
            </x-show.info-item>

            <x-show.info-item label="Matrícula" column="col-md-6" isBox="true">
                {{ $student->registration ?? '—' }}
            </x-show.info-item>

            <x-forms.section title="Dados da Matrícula" />

            <div class="col-md-6">
                <x-forms.select 
                    name="course_id" 
                    label="Curso / Série " 
                    required
                    aria-label="Selecionar curso ou série"
                    :options="$courses->pluck('name', 'id')->toArray()" 
                    :value="old('course_id')" />
            </div>

            <div class="col-md-6">
                <x-forms.input 
                    name="academic_year" 
                    label="Ano Letivo " 
                    type="number" 
                    required
                    aria-label="Ano letivo"
                    :value="old('academic_year', date('Y'))" />
            </div>

            <div class="col-md-6">
                <x-forms.select
                    name="is_current"
                    label="Curso atual "
                    :options="[1 => 'Ativo', 0 => 'Inativo']"
                />
            </div>

            <div class="col-12 d-flex justify-content-end gap-3 border-t pt-4 px-4 pb-4">
                <x-buttons.link-button 
                    href="{{ route('specialized-educational-support.student-courses.history', $student) }}" 
                    variant="secondary"
                    aria-label="Cancelar e voltar ao prontuário">
                    <i class="fas fa-times" aria-hidden="true"></i> Cancelar
                </x-buttons.link-button>

                <x-buttons.submit-button 
                    type="submit"
                    aria-label="Efetivar matrícula do aluno">
                    <i class="fas fa-save" aria-hidden="true"></i> Salvar
                </x-buttons.submit-button>
            </div>

        </x-forms.form-card>
    </div>
@endsection