@extends('layouts.master')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Alunos' => route('specialized-educational-support.students.index'),
            'Aluno' => route('specialized-educational-support.students.show', $student),
            'Registros de atendimento AEE' => route('specialized-educational-support.students.session-records.index', $student),
            'Visualizar' => route('specialized-educational-support.students.session-records.show', [$student, $evaluation]),
            'Editar' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Editar Avaliação do Aluno</h2>
            <p class="text-muted">
                {{ $student->person->name }} •
                Registro #{{ $evaluation->sessionRecord->id }} •
                Agendamento #{{ $evaluation->sessionRecord->attendance_session_id }}
            </p>
        </div>

        <x-buttons.link-button
            href="{{ route('specialized-educational-support.students.session-records.show', [$student, $evaluation]) }}"
            variant="secondary">
            <i class="fas fa-times"></i> Cancelar
        </x-buttons.link-button>
    </div>

    <x-forms.form-card action="{{ route('specialized-educational-support.students.session-records.update', [$student, $evaluation]) }}" method="POST">
        @method('PUT')

        @php
            $sessionRecord = $evaluation->sessionRecord;
            $attendanceSession = $sessionRecord->attendanceSession;
        @endphp

        {{-- SEÇÃO 1: DADOS DA SESSÃO --}}
        <x-forms.section title="Informações do atendimento AEE" />

        <div class="col-md-4">
            <x-show.info-item label="Data" column="col-md-12" isBox="true">
                {{ $attendanceSession->session_date->format('d/m/Y') }}
            </x-show.info-item>
        </div>

        <div class="col-md-4">
            <x-show.info-item label="Duração" column="col-md-12" isBox="true">
                {{ $sessionRecord->duration }}
            </x-show.info-item>
        </div>

        <div class="col-md-4">
            <x-show.info-item label="Profissional" column="col-md-12" isBox="true">
                {{ $attendanceSession->professional->person->name ?? 'Não informado' }}
            </x-show.info-item>
        </div>

        <div class="col-md-12">
            <x-show.info-textarea label="Objetivo do Atendimento" column="col-md-12" isBox="true">
                {!! $attendanceSession->session_objective ?? 'N/A' !!}
            </x-show.info-textarea>
        </div>

        <div class="col-md-12">
            <x-show.info-textarea label="Atividades Realizadas" column="col-md-12" isBox="true">
                {!! $sessionRecord->activities_performed ?? 'N/A' !!}
            </x-show.info-textarea>
        </div>

        <div class="col-md-6">
            <x-show.info-textarea label="Estratégias Utilizadas" column="col-md-12" isBox="true">
                {!! $sessionRecord->strategies_used ?? 'N/A' !!}
            </x-show.info-textarea>
        </div>

        <div class="col-md-6">
            <x-show.info-textarea label="Recursos Utilizados" column="col-md-12" isBox="true">
                {!! $sessionRecord->resources_used ?? 'N/A' !!}
            </x-show.info-textarea>
        </div>

        <div class="col-md-12">
            <x-show.info-textarea label="Observações Gerais" column="col-md-12" isBox="true">
                {!! $sessionRecord->general_observations ?? 'N/A' !!}
            </x-show.info-textarea>
        </div>

        {{-- SEÇÃO 2: EDIÇÃO DA AVALIAÇÃO --}}
        <x-forms.section title="Editar Avaliação Individual" />

        <input type="hidden" name="evaluation_id" value="{{ $evaluation->id }}">
        <input type="hidden" name="student_id" value="{{ $evaluation->student_id }}">

        <div class="col-12">
            <div class="form-check form-switch p-3 bg-light rounded border mb-3">
                <input type="hidden" name="is_present" value="0">
                <input class="form-check-input ms-0 me-2 presence-toggle" type="checkbox"
                       name="is_present"
                       id="presence"
                       value="1"
                       {{ old('is_present', $evaluation->is_present) ? 'checked' : '' }}>
                <label class="form-label fw-bold text-purple-dark" for="presence">Aluno Presente</label>
            </div>
        </div>

        {{-- Bloco de Ausência --}}
        <div class="col-md-12" id="absence_fields" style="display: {{ old('is_present', $evaluation->is_present) ? 'none' : 'block' }};">
            <x-forms.textarea
                name="absence_reason"
                label="Motivo da Ausência"
                rows="2"
                required
                :value="old('absence_reason', $evaluation->absence_reason)"
            />
        </div>

        {{-- Bloco de Presença --}}
        <div class="col-md-12 evaluation-fields" id="eval_fields" style="display: {{ old('is_present', $evaluation->is_present) ? 'block' : 'none' }};">
            <div class="row g-3">
                <div class="col-md-12">
                    <x-forms.textarea
                        name="student_participation"
                        label="Participação"
                        rows="3"
                        required
                        :value="old('student_participation', $evaluation->student_participation)"
                    />
                </div>

                <div class="col-md-12">
                    <x-forms.textarea
                        name="adaptations_made"
                        label="Adaptações para este Aluno"
                        rows="3"
                        :value="old('adaptations_made', $evaluation->adaptations_made)"
                    />
                </div>

                <div class="col-md-12">
                    <x-forms.textarea
                        name="development_evaluation"
                        label="Avaliação do Desenvolvimento"
                        rows="3"
                        required
                        :value="old('development_evaluation', $evaluation->development_evaluation)"
                    />
                </div>

                <div class="col-md-12">
                    <x-forms.textarea
                        name="progress_indicators"
                        label="Indicadores de Progresso"
                        rows="3"
                        :value="old('progress_indicators', $evaluation->progress_indicators)"
                    />
                </div>

                <div class="col-md-6">
                    <x-forms.textarea
                        name="recommendations"
                        label="Recomendações"
                        rows="3"
                        :value="old('recommendations', $evaluation->recommendations)"
                    />
                </div>

                <div class="col-md-6">
                    <x-forms.textarea
                        name="next_session_adjustments"
                        label="Ajustes para próximo atendimento AEE"
                        rows="3"
                        :value="old('next_session_adjustments', $evaluation->next_session_adjustments)"
                    />
                </div>
            </div>
        </div>

        <div class="col-12 d-flex justify-content-between gap-3 border-t pt-4 px-4 pb-4 mt-4">
            <x-buttons.link-button
                href="{{ route('specialized-educational-support.students.session-records.show', [$student, $evaluation]) }}"
                variant="secondary">
                <i class="fas fa-times"></i> Cancelar
            </x-buttons.link-button>

            <div class="d-flex gap-2">
                <x-buttons.link-button
                    href="{{ route('specialized-educational-support.session-records.show', $sessionRecord) }}"
                    variant="info">
                    <i class="fas fa-layer-group"></i> Ver registro completo
                </x-buttons.link-button>

                <x-buttons.submit-button type="submit" class="btn-action new submit">
                    <i class="fas fa-save"></i> Salvar
                </x-buttons.submit-button>
            </div>
        </div>
    </x-forms.form-card>
@endsection

@push('scripts')
    @vite(['resources/js/pages/specialized-educational-support/session-record-create.js'])
@endpush
