@extends('layouts.master')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Agendamentos' => route('specialized-educational-support.sessions.index'),
            'Agendamento #' . $session->id => route('specialized-educational-support.sessions.show', $session),
            'Atendimento Pedagógico' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Novo Atendimento Pedagógico</h2>
            <p class="text-muted">Agendamento #{{ $session->id }}</p>
        </div>

        <x-buttons.link-button href="{{ route('specialized-educational-support.sessions.show', $session) }}" variant="secondary">
            <i class="fas fa-times"></i> Cancelar
        </x-buttons.link-button>
    </div>

    <x-forms.form-card action="{{ route('specialized-educational-support.pedagogical-records.store') }}" method="POST">
        @include('pages.specialized-educational-support.pedagogical-records.partials.form', [
            'record' => null,
            'session' => $session,
            'submitLabel' => 'Salvar',
        ])
    </x-forms.form-card>
@endsection
