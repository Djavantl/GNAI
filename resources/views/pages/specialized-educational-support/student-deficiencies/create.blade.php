@extends('layouts.app')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Alunos' => route('specialized-educational-support.students.index'),
            $student->person->name => route('specialized-educational-support.students.show', $student),
            'Deficiências' => route('specialized-educational-support.student-deficiencies.index', $student),
            'Cadastrar' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Vincular Deficiência</h2>
            <p class="text-muted">
                Aluno: {{ $student->person->name }}
            </p>
        </div>
        <x-buttons.link-button href="{{ route('specialized-educational-support.student-deficiencies.index', $student) }}" variant="secondary">
            <i class="fas fa-times" aria-hidden="true"></i> Cancelar
        </x-buttons.link-button>
    </div>

    <div class="mt-3">
        <x-forms.form-card action="{{ route('specialized-educational-support.student-deficiencies.store', $student) }}" method="POST">
            
            <x-forms.section title="Identificação da Deficiência" />

            <div class="col-md-6">
                <x-forms.select
                    name="deficiency_id"
                    label="Deficiência"
                    required
                    :options="$deficienciesList->pluck('name', 'id')"
                    :value="old('deficiency_id')"
                />
            </div>

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
                    :value="old('severity')"
                />
            </div>

            <div class="col-md-6">
                <x-forms.textarea 
                    name="notes" 
                    label="Observações" 
                    rows="4" 
                    placeholder="Detalhes adicionais sobre a deficiência do aluno..."
                    :value="old('notes')" 
                />
            </div>

            <div class="col-md-6">
                <x-forms.select
                    name="uses_support_resources"
                    label="Utiliza recursos de apoio"
                    description="Marque se o aluno necessita de tecnologias assistivas ou recursos específicos para esta deficiência"
                    :options="[1 => 'Sim', 0 => 'Não']"
                />
            </div>

            <div class="col-12 d-flex justify-content-end gap-3 border-t pt-4 px-4 pb-4">
                <x-buttons.link-button href="{{ route('specialized-educational-support.student-deficiencies.index', $student) }}" variant="secondary">
                    <i class="fas fa-times" aria-hidden="true"></i> Cancelar
                </x-buttons.link-button>

                <x-buttons.submit-button type="submit" class="btn-action new submit">
                    <i class="fas fa-save" aria-hidden="true"></i> Salvar
                </x-buttons.submit-button>
            </div>

        </x-forms.form-card>
    </div>
    
@endsection