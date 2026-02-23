@extends('layouts.master')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Sessões' => route('specialized-educational-support.sessions.index'),
            'Sessão #' . $session->id => route('specialized-educational-support.sessions.show', $session),
            'Editar' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Editar Sessão #{{ $session->id }}</h2>
            <p class="text-muted">Ajuste os dados de agendamento, local ou objetivo desta sessão.</p>
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
            <input type="hidden" name="type" value="{{ $session->type }}">
            <input type="hidden" name="status" value="{{ $session->status }}">
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
                <x-forms.input 
                    name="session_date" 
                    label="Data da Sessão *" 
                    type="date" 
                    required 
                    :value="old('session_date', $session->session_date)" 
                />
            </div>

            <div class="col-md-6">
                <div class="row">
                    <div class="col-6">
                        <x-forms.select 
                            name="start_time" 
                            label="Início *" 
                            required 
                            :options="$startTimes" 
                            :selected="old('start_time', $session->end_time ? \Carbon\Carbon::parse($session->end_time)->format('H:i') : '')" 
                        />
                    </div>
                    <div class="col-6">
                        <x-forms.select 
                            name="end_time" 
                            label="Fim *" 
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
    <script>
        window.routes = {
            sessionAvailability: "{{ route('specialized-educational-support.sessions.availability') }}"
        };

        document.addEventListener('DOMContentLoaded', function() {
            // Reutiliza a função loadSchedule que você já tem
            document.getElementById('session_date').addEventListener('change', loadSchedule);
            document.querySelector('select[name="start_time"]').addEventListener('change', updateEndTimeOptions);
            
            // Dispara a carga inicial para mostrar os horários do dia atual da sessão
            loadSchedule();
        });
    </script>
    @endpush
@endsection