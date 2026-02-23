@extends('layouts.app')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Alunos' => route('specialized-educational-support.students.index'),
            $student->person->name => route('specialized-educational-support.students.show', $student),
            'Deficiências' => route('specialized-educational-support.student-deficiencies.index', $student),
            $student_deficiency->deficiency->name => route('specialized-educational-support.student-deficiencies.show', [$student, $student_deficiency]),
            'Editar' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Editar Deficiência</h2>
            <p class="text-muted">
               Aluno: {{ $student->person->name }}
            </p>
        </div>
        <x-buttons.link-button href="{{ route('specialized-educational-support.student-deficiencies.index', $student) }}" variant="secondary">
            <i class="fas fa-times" aria-hidden="true"></i> Cancelar
        </x-buttons.link-button>
    </div>

    <div class="mt-3">
        <x-forms.form-card action="{{ route('specialized-educational-support.student-deficiencies.update', [$student, $student_deficiency]) }}" method="POST">
            @method('PUT')

            <x-forms.section title="Atualizar Informações" />

            {{-- Deficiência exibida, não editável --}}
            <div class="col-md-6">
                <x-forms.input
                    name="deficiency_display"
                    label="Deficiência"
                    :value="(string) optional($student_deficiency->deficiency)->name"
                    disabled
                />
            </div>

            <input type="hidden" name="deficiency_id" value="{{ $student_deficiency->deficiency_id }}">

            <div class="col-md-6">
                <x-forms.select
                    name="severity"
                    label="Severidade"
                    :options="[
                        'mild' => 'Leve',
                        'moderate' => 'Moderada',
                        'severe' => 'Severa'
                    ]"
                    :value="old('severity', $student_deficiency->severity)"
                    :selected="old('severity', $student_deficiency->severity)"
                />
            </div>

            <div class="col-md-6 mt-2">
                <x-forms.textarea 
                    name="notes" 
                    label="Observações" 
                    rows="4" 
                    :value="old('notes', $student_deficiency->notes)" 
                />
            </div>

            <div class="col-md-6 mt-5">
                <input type="hidden" name="uses_support_resources" value="0">
                <x-forms.checkbox 
                    name="uses_support_resources" 
                    label="Utiliza recursos de apoio" 
                    :checked="old('uses_support_resources', $student_deficiency->uses_support_resources)" 
                />
            </div>

            <div class="col-12 d-flex justify-content-end gap-3 border-t pt-4 px-4 pb-4">
                <x-buttons.link-button href="{{ route('specialized-educational-support.student-deficiencies.index', $student) }}" variant="secondary">
                    <i class="fas fa-times" aria-hidden="true"></i> Cancelar
                </x-buttons.link-button>

                <x-buttons.submit-button type="submit" class="btn-action new submit ">
                    <i class="fas fa-save"></i> Salvar
                </x-buttons.submit-button>
            </div>

        </x-forms.form-card>
    </div>
    
@endsection