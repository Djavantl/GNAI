@extends('layouts.app')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Sessões' => route('specialized-educational-support.sessions.index'),
            'Sessão' => route('specialized-educational-support.session-records.show', $session),
            'Registro' => route('specialized-educational-support.session-records.show', $sessionRecord),
            'Editar' => null,
            
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Editar Registro de Sessão</h2>
            <p class="text-muted">
                Registro #{{ $sessionRecord->id }} • Sessão #{{ $sessionRecord->attendance_sessions_id }}
            </p>
        </div>
    </div>

    <div class="mt-3">
        <x-forms.form-card action="{{ route('specialized-educational-support.session-records.update', $sessionRecord) }}" method="POST">
            @method('PUT')
            <input type="hidden" name="attendance_sessions_id" value="{{ $sessionRecord->attendance_sessions_id }}">

            <x-forms.section title="Informações da Sessão" />

            <div class="col-md-6">
                <x-forms.input 
                    name="record_date" 
                    label="Data do Registro" 
                    type="date" 
                    :value="old('record_date', \Carbon\Carbon::parse($sessionRecord->record_date)->format('Y-m-d'))" 
                />
            </div>

            <div class="col-md-6">
                <x-forms.input 
                    name="duration" 
                    label="Duração *" 
                    required 
                    :value="old('duration', $sessionRecord->duration)" 
                />
            </div>

            <div class="col-md-6">
                <x-forms.input 
                    name="student_participation" 
                    label="Participação do Aluno *" 
                    required 
                    :value="old('student_participation', $sessionRecord->student_participation)" 
                />
            </div>

            <div class="col-md-6">
                <x-forms.input 
                    name="engagement_level" 
                    label="Nível de Engajamento" 
                    :value="old('engagement_level', $sessionRecord->engagement_level)" 
                />
            </div>

            <div class="col-md-12 mt-2">
                <x-forms.checkbox 
                    name="external_referral_needed" 
                    label="Encaminhamento Externo" 
                    :checked="old('external_referral_needed', $sessionRecord->external_referral_needed)" 
                />
            </div>

            <x-forms.section title="Atividades e Avaliação" />

            <div class="col-md-12">
                <x-forms.textarea 
                    name="activities_performed" 
                    label="Atividades Realizadas *" 
                    rows="3" 
                    required 
                    :value="old('activities_performed', $sessionRecord->activities_performed)" 
                />
            </div>

            <div class="col-md-12">
                <x-forms.textarea 
                    name="development_evaluation" 
                    label="Avaliação do Desenvolvimento *" 
                    rows="3" 
                    required 
                    :value="old('development_evaluation', $sessionRecord->development_evaluation)" 
                />
            </div>

            <div class="col-md-12">
                <x-forms.textarea 
                    name="general_observations" 
                    label="Observações Gerais" 
                    rows="3" 
                    :value="old('general_observations', $sessionRecord->general_observations)" 
                />
            </div>

            <div class="col-12 d-flex justify-content-between border-t pt-4 px-4 pb-4">
                <div>
                    <x-buttons.link-button href="{{ route('specialized-educational-support.session-records.show', $sessionRecord) }}" variant="secondary">
                        Voltar
                    </x-buttons.link-button>
                </div>

                <div class="d-flex gap-3">
                    <x-buttons.submit-button type="submit" class="btn-action new submit px-5">
                        <i class="fas fa-sync mr-2"></i> Atualizar Registro
                    </x-buttons.submit-button>
                </div>
            </div>
        </x-forms.form-card>
    </div>
@endsection