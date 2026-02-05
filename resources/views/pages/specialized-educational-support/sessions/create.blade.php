@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Agendar Nova Sessão</h2>
            <p class="text-muted">Preencha os dados para agendar o atendimento especializado.</p>
        </div>
    </div>

    <div class="mt-3">
        <x-forms.form-card action="{{ route('specialized-educational-support.sessions.store') }}" method="POST">
            
            <x-forms.section title="Participantes e Horário" />

            <div class="col-md-6">
                <x-forms.select
                    name="student_id"
                    label="Aluno *"
                    required
                    :options="$students->mapWithKeys(fn($s) => [$s->id => $s->person->name ?? 'Sem Nome'])"
                />
            </div>

            <div class="col-md-6">
                <x-forms.select
                    name="professional_id"
                    label="Profissional *"
                    required
                    :options="$professionals->mapWithKeys(fn($p) => [$p->id => $p->person->name ?? 'Sem Nome'])"
                />
            </div>

            <div class="col-md-6">
                <x-forms.input name="session_date" label="Data da Sessão *" type="date" required :value="old('session_date')" />
            </div>

            <div class="col-md-6">
                <div class="row">
                    <div class="col-6">
                        <x-forms.input name="start_time" label="Início *" type="time" required :value="old('start_time')" />
                    </div>
                    <div class="col-6">
                        <x-forms.input name="end_time" label="Fim *" type="time" required :value="old('end_time')" />
                    </div>
                </div>
            </div>

            <x-forms.section title="Detalhes do Atendimento" />

            <div class="col-md-6">
                <x-forms.input name="type" label="Tipo de Atendimento" placeholder="Ex: Individual" :value="old('type')" />
            </div>

            <div class="col-md-6">
                <x-forms.input name="location" label="Local" :value="old('location')" />
            </div>

            <div class="col-md-6">
                <x-forms.select
                    name="status"
                    label="Status"
                    :options="['Agendado' => 'Agendado', 'Realizado' => 'Realizado', 'Cancelado' => 'Cancelado']"
                    :value="old('status', 'Agendado')"
                />
            </div>

            <div class="col-md-12">
                <x-forms.textarea
                    name="session_objective"
                    label="Objetivo da Sessão"
                    rows="3"
                    :value="old('session_objective')"
                />
            </div>

            <div class="col-12 d-flex justify-content-end gap-3 border-t pt-4 px-4 pb-4">
                <x-buttons.link-button href="{{ route('specialized-educational-support.sessions.index') }}" variant="secondary">
                    Cancelar
                </x-buttons.link-button>

                <x-buttons.submit-button type="submit" class="btn-action new submit px-5">
                    <i class="fas fa-save mr-2"></i> Salvar Sessão
                </x-buttons.submit-button>
            </div>

        </x-forms.form-card>
    </div>
@endsection