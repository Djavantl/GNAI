@extends('layouts.app')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Alunos' => route('specialized-educational-support.students.index'),
            $student->person->name => route('specialized-educational-support.students.show', $student),
            'Perfis de Atendimento' => route('specialized-educational-support.student-deficiencies.index', $student),
            $student_deficiency->deficiency->name => route('specialized-educational-support.student-deficiencies.show', [$student, $student_deficiency]),
            'Editar' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Editar Perfil de Atendimento do Aluno</h2>
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

            {{-- Perfil exibido, não editável --}}
            <div class="col-md-6">
                <x-forms.input
                    name="deficiency_display"
                    label="Perfil"
                    :value="(string) optional($student_deficiency->deficiency)->name"
                    required
                    disabled
                />
            </div>

            <input type="hidden" name="deficiency_id" value="{{ $student_deficiency->deficiency_id }}">

            <div class="col-md-6">
                <x-forms.select
                    name="severity"
                    label="Severidade"
                    required
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

            <div class="col-md-6">
                <x-forms.select
                    name="uses_support_resources"
                    label="Utiliza recursos de apoio"
                    description="Marque se o aluno necessita de tecnologias assistivas ou recursos específicos para este perfil de atendimento"
                    :options="[1 => 'Sim', 0 => 'Não']"
                    :selected="old('uses_support_resources', $student_deficiency->uses_support_resources)" 
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
