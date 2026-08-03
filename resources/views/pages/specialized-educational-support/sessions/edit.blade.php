@extends('layouts.master')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Agendamentos' => route('specialized-educational-support.sessions.index'),
            'Agendamento #' . $session->id => route('specialized-educational-support.sessions.show', $session),
            'Editar' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Editar Agendamento #{{ $session->id }}</h2>
            <p class="text-muted">Ajuste os dados de agendamento, local ou objetivo deste agendamento.</p>
        </div>
        <x-buttons.link-button href="{{ route('specialized-educational-support.sessions.show', $session) }}" variant="secondary">
            <i class="fas fa-times"></i> Cancelar
        </x-buttons.link-button>
    </div>

    <div class="mt-3">
        <x-forms.form-card action="{{ route('specialized-educational-support.sessions.update', $session) }}" method="POST">
            @method('PUT')

            {{-- Inputs Hidden Críticos para o Funcionamento --}}
            <input type="hidden" name="professional_id" value="{{ $session->professional_id }}">
            @php($hasLinkedRecord = $session->aeeRecord || $session->pedagogicalRecord)
            <input type="hidden" name="status" value="{{ $session->status }}">
            @if($hasLinkedRecord)
                <input type="hidden" name="attendance_type" value="{{ \App\Domains\SpecializedEducationalSupport\Domain\Enums\AttendanceType::valueOf($session->attendance_type) }}">
            @endif
            @foreach($session->students as $student)
                <input type="hidden" name="student_ids[]" class="student-select-item" value="{{ $student->id }}">
            @endforeach

            <x-forms.section title="Participantes" />

            <div class="row mb-4">
                {{-- Listagem de Alunos --}}
                <x-show.info-item label="Alunos" column="col-md-6" isBox="true">
                    <div class="d-flex flex-column gap-1">
                        @foreach($session->students as $student)
                            <div class="text-purple">{{ $student->person->name }}</div>
                        @endforeach
                    </div>
                </x-show.info-item>

                {{-- Profissional --}}
                <x-show.info-item 
                    label="Profissional Responsável" 
                    column="col-md-6" 
                    isBox="true"
                    :value="$session->professional->person->name"
                />
            </div>

            <x-forms.section title="Agendamento" />

            <div class="col-md-6">
                <x-forms.select
                    name="attendance_type"
                    label="Tipo de Atendimento"
                    required
                    :options="\App\Domains\SpecializedEducationalSupport\Domain\Enums\AttendanceType::options()"
                    :selected="old('attendance_type', \App\Domains\SpecializedEducationalSupport\Domain\Enums\AttendanceType::valueOf($session->attendance_type))"
                    id="attendance_type"
                    :disabled="$hasLinkedRecord"
                />
                @if($hasLinkedRecord)
                    <small class="text-muted">O tipo de atendimento não pode ser alterado porque já existe registro vinculado.</small>
                @endif
            </div>

            <div class="col-md-6">
                <x-forms.select
                    name="type"
                    label="Formato"
                    required
                    :options="\App\Domains\SpecializedEducationalSupport\Domain\Enums\SessionType::options()"
                    :selected="old('type', $session->type)"
                    id="session_type"
                />
            </div>

            <div class="col-md-6">
                <x-forms.input 
                    name="session_date" 
                    label="Data do Agendamento " 
                    type="date" 
                    required 
                    :value="old('session_date', optional($session->session_date)->format('Y-m-d'))"
                />
            </div>

            <div class="col-md-6">
                <div class="row">
                    <div class="col-6">
                        <x-forms.select 
                            name="start_time" 
                            label="Início " 
                            required 
                            :options="$startTimes" 
                            :selected="old('start_time', $session->start_time ? \Carbon\Carbon::parse($session->start_time)->format('H:i') : '')"
                        />
                    </div>
                    <div class="col-6">
                        <x-forms.select 
                            name="end_time" 
                            label="Fim " 
                            required
                            :options="$endTimes" 
                           :selected="old('end_time', $session->end_time ? \Carbon\Carbon::parse($session->end_time)->format('H:i') : '')" 
                        />
                    </div>
                </div>
            </div>

            <x-forms.section title="Disponibilidade do Dia" />

            <div class="col-md-12 mb-4">
                <div id="schedule" class="border rounded p-3 bg-white shadow-sm" style="overflow-x: auto;">
                    {{-- Preenchido via JavaScript --}}
                </div>
            </div>

            <x-forms.section title="Detalhes Adicionais" />

            <div class="col-md-12 mb-3">
                <x-forms.input 
                    name="location" 
                    label="Local" 
                    :value="old('location', $session->location)" 
                    required
                />
            </div>

            <div class="col-md-12">
                <x-forms.textarea
                    name="session_objective"
                    label="Objetivo do Agendamento"
                    rows="3"
                    :value="old('session_objective', $session->session_objective)"
                    required
                />
            </div>

            <div class="col-12 d-flex flex-wrap justify-content-end gap-2 border-t pt-4 px-4 pb-4">
                <x-buttons.link-button href="{{ route('specialized-educational-support.sessions.show', $session) }}" variant="secondary">
                    <i class="fas fa-times"></i> Cancelar
                </x-buttons.link-button>

                <x-buttons.submit-button type="submit" class="btn-action btn-warning">
                    <i class="fas fa-save"></i> Salvar 
                </x-buttons.submit-button>
            </div>

        </x-forms.form-card>
    </div>
    
    @push('scripts')
        @vite('resources/js/pages/specialized-educational-support/session.js')
        <script>
        window.routes = {
            sessionAvailability: "{{ route('specialized-educational-support.sessions.availability') }}"
        };
        </script>
    @endpush
@endsection
