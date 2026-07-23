@extends('layouts.master')

@section('content')
    @php($session = $pedagogicalRecord->attendanceSession)

    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Agendamentos' => route('specialized-educational-support.sessions.index'),
            'Agendamento #' . $session->id => route('specialized-educational-support.sessions.show', $session),
            'Atendimento Pedagógico' => route('specialized-educational-support.pedagogical-records.show', $pedagogicalRecord),
            'Editar' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Editar Atendimento Pedagógico</h2>
            <p class="text-muted">Agendamento #{{ $session->id }}</p>
        </div>

        <x-buttons.link-button href="{{ route('specialized-educational-support.pedagogical-records.show', $pedagogicalRecord) }}" variant="secondary">
            <i class="fas fa-times"></i> Cancelar
        </x-buttons.link-button>
    </div>

    <x-forms.form-card action="{{ route('specialized-educational-support.pedagogical-records.update', $pedagogicalRecord) }}" method="POST">
        @method('PUT')
        @include('pages.specialized-educational-support.pedagogical-records.partials.form', [
            'record' => $pedagogicalRecord,
            'session' => $session,
            'submitLabel' => 'Salvar',
        ])
    </x-forms.form-card>
@endsection
