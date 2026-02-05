@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Novo Registro de Sessão</h2>
            <p class="text-muted">
                @if($session->student)
                    Aluno: <strong>{{ $session->student->person->name }}</strong> • Sessão #{{ $session->id }}
                @else
                    Sessão #{{ $session->id }}
                @endif
            </p>
        </div>
    </div>

    <div class="mt-3">
        <x-forms.form-card action="{{ route('specialized-educational-support.session-records.store') }}" method="POST">
            <input type="hidden" name="attendance_sessions_id" value="{{ $session->id }}">

            <x-forms.section title="Informações da Sessão" />

            <div class="col-md-6">
                <x-forms.input name="record_date" label="Data do Registro" type="date" :value="old('record_date', date('Y-m-d'))" />
            </div>

            <div class="col-md-6">
                <x-forms.input name="duration" label="Duração *" placeholder="Ex: 50 minutos" required :value="old('duration')" />
            </div>

            <div class="col-md-6">
                <x-forms.input name="student_participation" label="Participação do Aluno *" placeholder="Ex: Ativa, Moderada" required :value="old('student_participation')" />
            </div>

            <div class="col-md-6">
                <x-forms.input name="engagement_level" label="Nível de Engajamento" placeholder="Ex: Alto, Médio" :value="old('engagement_level')" />
            </div>

            <div class="col-md-12 mt-2 mb-4">
                <x-forms.checkbox 
                    name="external_referral_needed" 
                    label="Encaminhamento Externo" 
                    description="Indica se há necessidade de encaminhar o aluno para especialistas externos"
                    :checked="old('external_referral_needed')" 
                />
            </div>

            <x-forms.section title="Atividades e Estratégias" />

            <div class="col-md-12">
                <x-forms.textarea name="activities_performed" label="Atividades Realizadas" rows="3" required :value="old('activities_performed')" />
            </div>

            <div class="col-md-12">
                <x-forms.textarea name="strategies_used" label="Estratégias Utilizadas" rows="2" :value="old('strategies_used')" />
            </div>

            <div class="col-md-12">
                <x-forms.textarea name="resources_used" label="Recursos Utilizados" rows="2" :value="old('resources_used')" />
            </div>

            <div class="col-md-12">
                <x-forms.textarea name="adaptations_made" label="Adaptações Realizadas" rows="2" :value="old('adaptations_made')" />
            </div>

            <x-forms.section title="Comportamento e Evolução" />

            <div class="col-md-12">
                <x-forms.textarea name="observed_behavior" label="Comportamento Observado" rows="3" :value="old('observed_behavior')" />
            </div>

            <div class="col-md-12">
                <x-forms.textarea name="development_evaluation" label="Avaliação do Desenvolvimento *" rows="3" required :value="old('development_evaluation')" />
            </div>

            <div class="col-md-12">
                <x-forms.textarea name="general_observations" label="Observações Gerais" rows="3" :value="old('general_observations')" />
            </div>

            <div class="col-12 d-flex justify-content-end gap-3 border-t pt-4 px-4 pb-4">
                <x-buttons.link-button href="{{ route('specialized-educational-support.sessions.show', $session) }}" variant="secondary">
                    Cancelar
                </x-buttons.link-button>

                <x-buttons.submit-button type="submit" class="btn-action new submit px-5">
                    <i class="fas fa-save mr-2"></i> Salvar Registro
                </x-buttons.submit-button>
            </div>
        </x-forms.form-card>
    </div>
@endsection