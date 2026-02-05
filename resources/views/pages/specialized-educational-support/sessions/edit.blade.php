@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Editar Sessão #{{ $session->id }}</h2>
            <p class="text-muted">Atenção: Você está editando os dados desta sessão.</p>
        </div>
    </div>

    <div class="mt-3">
        <x-forms.form-card action="{{ route('specialized-educational-support.sessions.update', $session) }}" method="POST">
            @method('PUT')

            {{-- Campos ocultos mantidos do original --}}
            <input type="hidden" name="student_id" value="{{ $session->student_id }}">
            <input type="hidden" name="professional_id" value="{{ $session->professional_id }}">

            <x-forms.section title="Informações de Agendamento" />

            <div class="col-md-6">
                <x-forms.input 
                    name="session_date" 
                    label="Data *" 
                    type="date" 
                    required 
                    :value="old('session_date', $session->session_date)" 
                />
            </div>

            <div class="col-md-6">
                <div class="row">
                    <div class="col-6">
                        <x-forms.input 
                            name="start_time" 
                            label="Início *" 
                            type="time" 
                            required 
                            :value="old('start_time', \Carbon\Carbon::parse($session->start_time)->format('H:i'))" 
                        />
                    </div>
                    <div class="col-6">
                        <x-forms.input 
                            name="end_time" 
                            label="Fim" 
                            type="time" 
                            :value="old('end_time', $session->end_time ? \Carbon\Carbon::parse($session->end_time)->format('H:i') : '')" 
                        />
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <x-forms.input 
                    name="type" 
                    label="Tipo de Atendimento" 
                    :value="old('type', $session->type)" 
                />
            </div>

            <div class="col-md-6">
                <x-forms.input 
                    name="location" 
                    label="Local" 
                    :value="old('location', $session->location)" 
                />
            </div>

            <div class="col-md-6">
                <x-forms.select
                    name="status"
                    label="Status"
                    :options="['Agendado' => 'Agendado', 'Realizado' => 'Realizado', 'Cancelado' => 'Cancelado']"
                    :value="old('status', $session->status)"
                    :selected="old('status', $session->status)"
                />
            </div>

            <div class="col-md-12">
                <x-forms.textarea
                    name="session_objective"
                    label="Objetivo da Sessão"
                    rows="3"
                    :value="old('session_objective', $session->session_objective)"
                />
            </div>

            <div class="col-12 d-flex justify-content-end gap-3 border-t pt-4 px-4 pb-4">
                <x-buttons.link-button href="{{ route('specialized-educational-support.sessions.index') }}" variant="secondary">
                    Voltar
                </x-buttons.link-button>

                <x-buttons.submit-button type="submit" class="btn-action new submit px-5">
                    <i class="fas fa-sync mr-2"></i> Atualizar Sessão
                </x-buttons.submit-button>
            </div>

        </x-forms.form-card>
    </div>
@endsection