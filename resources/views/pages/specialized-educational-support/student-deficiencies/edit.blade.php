@extends('layouts.app')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Alunos' => route('specialized-educational-support.students.index'),
            $student->person->name => route('specialized-educational-support.students.show', $student),
            'Deficiências' => route('specialized-educational-support.student-deficiencies.index', $student),
            
            // Mudamos aqui: pegamos a deficiência através do vínculo
            $student_deficiency->deficiency->name => auth()->user()->is_admin 
                ? route('specialized-educational-support.deficiencies.show', $student_deficiency->deficiency) 
                : null,
                
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
    </div>

    <div class="mt-3">
        <x-forms.form-card action="{{ route('specialized-educational-support.student-deficiencies.update', $student_deficiency) }}" method="POST">
            @method('PUT')

            <x-forms.section title="Atualizar Informações" />

            <div class="col-md-6">
                <x-forms.select
                    name="deficiency_id"
                    label="Deficiência *"
                    required
                    :options="$deficienciesList->pluck('name', 'id')"
                    :value="old('deficiency_id', $student_deficiency->deficiency_id)"
                    :selected="old('deficiency_id', $student_deficiency->deficiency_id)"
                />
            </div>

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

            <div class="col-md-12 mt-2">
                <x-forms.checkbox 
                    name="uses_support_resources" 
                    label="Utiliza recursos de apoio" 
                    :checked="old('uses_support_resources', $student_deficiency->uses_support_resources)" 
                />
            </div>

            <div class="col-md-12">
                <x-forms.textarea 
                    name="notes" 
                    label="Observações" 
                    rows="4" 
                    :value="old('notes', $student_deficiency->notes)" 
                />
            </div>

            <div class="col-12 d-flex justify-content-end gap-3 border-t pt-4 px-4 pb-4">
                <x-buttons.link-button href="{{ route('specialized-educational-support.student-deficiencies.index', $student) }}" variant="secondary">
                    Voltar
                </x-buttons.link-button>

                <x-buttons.submit-button type="submit" class="btn-action new submit px-5">
                    <i class="fas fa-sync mr-2"></i> Atualizar Deficiência
                </x-buttons.submit-button>
            </div>

        </x-forms.form-card>
    </div>
    
@endsection