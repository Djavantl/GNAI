@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Vincular Deficiência</h2>
            <p class="text-muted">
                Aluno: <strong>{{ $student->name }}</strong> • Matrícula: {{ $student->enrollment ?? 'N/A' }}
            </p>
        </div>
    </div>

    <div class="mt-3">
        <x-forms.form-card action="{{ route('specialized-educational-support.student-deficiencies.store', $student) }}" method="POST">
            
            <x-forms.section title="Identificação da Deficiência" />

            <div class="col-md-6">
                <x-forms.select
                    name="deficiency_id"
                    label="Deficiência *"
                    required
                    :options="$deficienciesList->pluck('name', 'id')"
                    :value="old('deficiency_id')"
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
                    :value="old('severity')"
                />
            </div>

            <div class="col-md-12 mt-2">
                <x-forms.checkbox 
                    name="uses_support_resources" 
                    label="Utiliza recursos de apoio" 
                    description="Marque se o aluno necessita de tecnologias assistivas ou recursos específicos para esta deficiência"
                    :checked="old('uses_support_resources')" 
                />
            </div>

            <div class="col-md-12">
                <x-forms.textarea 
                    name="notes" 
                    label="Observações" 
                    rows="4" 
                    placeholder="Detalhes adicionais sobre a deficiência do aluno..."
                    :value="old('notes')" 
                />
            </div>

            <div class="col-12 d-flex justify-content-end gap-3 border-t pt-4 px-4 pb-4">
                <x-buttons.link-button href="{{ route('specialized-educational-support.student-deficiencies.index', $student) }}" variant="secondary">
                    Cancelar
                </x-buttons.link-button>

                <x-buttons.submit-button type="submit" class="btn-action new submit px-5">
                    <i class="fas fa-link mr-2"></i> Vincular Deficiência
                </x-buttons.submit-button>
            </div>

        </x-forms.form-card>
    </div>
    
@endsection