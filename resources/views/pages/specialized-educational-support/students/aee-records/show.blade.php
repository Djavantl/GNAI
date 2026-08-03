@extends('layouts.master')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Alunos' => route('specialized-educational-support.students.index'),
            'Aluno' => route('specialized-educational-support.students.show', $student),
            'Registros de Atendimentos AEE' => route('specialized-educational-support.students.aee-records.index', $student),
            'Visualizar' => null
        ]" />
    </div>

    {{-- Cabeçalho --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 no-print">
        <div>
            <h2 class="text-title">Registro de atendimento AEE do Aluno</h2>
            <p class="text-muted">
                {{ $student->person->name }} •
                Agendamento #{{ $evaluation->aeeRecord->attendance_session_id }} •
                Realizada em: {{ $evaluation->aeeRecord->attendanceSession->session_date->format('d/m/Y') }}
            </p>
        </div>

        <div class="d-flex gap-2 flex-wrap justify-content-end ms-md-auto">
            <x-buttons.pdf-button class="ms-3" :href="route('specialized-educational-support.students.aee-records.pdf', [$student, $evaluation])" />

            @can('aee-record.view')
                <x-buttons.link-button
                    class="ms-3"
                    :href="route('specialized-educational-support.aee-records.show', $evaluation->aeeRecord)"
                    variant="info">
                    <i class="fas fa-layer-group"></i> Ver Registro Completo
                </x-buttons.link-button>
            @endcan

            <x-buttons.link-button
                :href="route('specialized-educational-support.students.aee-records.index', $student)"
                variant="secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </x-buttons.link-button>
        </div>
    </div>

    <div class="custom-table-card bg-white shadow-sm overflow-hidden">
        <div class="row g-0">

            @php
                $aeeRecord = $evaluation->aeeRecord;
                $attendanceSession = $aeeRecord->attendanceSession;
                $professional = $attendanceSession->professional;
                $canManageEvaluation = auth()->user()?->professional?->id === $attendanceSession->professional_id;
            @endphp

            {{-- INFORMAÇÕES GERAIS --}}
            <x-forms.section title="Execução do Atendimento AEE" />

            <x-show.info-item label="Duração" column="col-md-4" isBox="true">
                {{ $aeeRecord->duration }}
            </x-show.info-item>

            <x-show.info-textarea label="Atividades Realizadas" column="col-md-12" isBox="true">
                {!! $aeeRecord->activities_performed ?? 'N/A' !!}
            </x-show.info-textarea>

            <x-show.info-textarea label="Estratégias Utilizadas" column="col-md-6" isBox="true">
                {!! $aeeRecord->strategies_used ?? 'N/A' !!}
            </x-show.info-textarea>

            <x-show.info-textarea label="Recursos Utilizados" column="col-md-6" isBox="true">
                {!! $aeeRecord->resources_used ?? 'N/A' !!}
            </x-show.info-textarea>

            <x-show.info-textarea label="Observações Gerais" column="col-md-12" isBox="true">
                {!! $aeeRecord->general_observations ?? 'N/A' !!}
            </x-show.info-textarea>

            {{-- AVALIAÇÃO INDIVIDUAL --}}
            <x-forms.section title="Avaliação Individual do Aluno" />

            <div class="col-12 p-4">
                <div class="row g-0 border rounded">
                    {{-- Lista Lateral --}}
                    <div class="col-md-3 bg-light border-end">
                        <div class="list-group list-group-flush" id="students-list" role="tablist">
                            <div class="list-group-item active d-flex justify-content-between align-items-center">
                                <span>
                                    {{ $student->person->name }}
                                    @if(!$evaluation->is_present)
                                        <br><small class="badge bg-danger">Falta</small>
                                    @endif
                                </span>
                                <i class="fas fa-chevron-right small opacity-50"></i>
                            </div>
                        </div>
                    </div>

                    {{-- Conteúdo --}}
                    <div class="col-md-9 p-4 bg-white">
                        <div class="tab-content">
                            <div class="tab-pane fade show active">
                                <h5 class="mb-4 border-bottom pb-2 text-title">
                                    Avaliação: {{ $student->person->name }}
                                </h5>

                                @if(!$evaluation->is_present)
                                    <div class="alert alert-danger border-0 d-flex align-items-center">
                                        <i class="fas fa-user-times me-3 fa-2x"></i>
                                        <div>
                                            <h5 class="mb-1">Aluno Ausente</h5>
                                            <p class="mb-0">
                                                <strong>Motivo:</strong>
                                                {!! \App\Support\RichTextSanitizer::sanitize((string) ($evaluation->absence_reason ?? 'Não justificado.')) !!}
                                            </p>
                                        </div>
                                    </div>
                                @else
                                    <div class="row g-3">
                                        <x-show.info-textarea label="Participação" column="col-md-12" isBox="true">
                                            {!! $evaluation->student_participation ?? 'N/A' !!}
                                        </x-show.info-textarea>

                                        <x-show.info-textarea label="Adaptações Realizadas" column="col-md-12" isBox="true">
                                            {!! $evaluation->adaptations_made ?? 'Nenhuma adaptação informada.' !!}
                                        </x-show.info-textarea>

                                        <x-show.info-textarea label="Avaliação do Desenvolvimento" column="col-md-12" isBox="true">
                                            {!! $evaluation->development_evaluation ?? 'N/A' !!}
                                        </x-show.info-textarea>

                                        <x-show.info-textarea label="Indicadores de Progresso" column="col-md-12" isBox="true">
                                            {!! $evaluation->progress_indicators ?? 'N/A' !!}
                                        </x-show.info-textarea>

                                        <x-show.info-textarea label="Recomendações" column="col-md-6" isBox="true">
                                            {!! $evaluation->recommendations ?? 'Nenhuma recomendação.' !!}
                                        </x-show.info-textarea>

                                        <x-show.info-textarea label="Ajustes para Próximo atendimento AEE" column="col-md-6" isBox="true">
                                            {!! $evaluation->next_session_adjustments ?? 'N/A' !!}
                                        </x-show.info-textarea>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <footer class="col-12 border-top p-4 d-flex flex-wrap justify-content-end gap-2 bg-light-subtle">
                <div class="d-flex flex-wrap gap-2" role="group" aria-label="Ações de gestão">
                    @can('aee-record.update')
                    @if($canManageEvaluation)
                    <x-buttons.link-button
                        :href="route('specialized-educational-support.students.aee-records.edit', [$student, $evaluation])"
                        variant="warning">
                        <i class="fas fa-edit"></i> Editar
                    </x-buttons.link-button>
                    @endif
                    @endcan

                    @can('aee-record.delete')
                    @if($canManageEvaluation)
                    <x-buttons.submit-button
                        type="button"
                        variant="danger"
                        data-bs-toggle="modal"
                        data-bs-target="#globalConfirmActionModal"
                        data-confirm-title="Excluir Registro do Aluno"
                        data-confirm-message="Excluir apenas o registro deste aluno neste atendimento AEE?"
                        data-confirm-action="{{ route('specialized-educational-support.students.aee-records.destroy', [$student, $evaluation]) }}"
                        data-confirm-method="DELETE"
                        data-confirm-submit-text="Confirmar Exclusao"
                        data-confirm-variant="danger"
                    >
                            <i class="fas fa-trash-alt"></i> Excluir
                        </x-buttons.submit-button>
                    @endif
                    @endcan

                    <x-buttons.link-button
                        :href="route('specialized-educational-support.students.aee-records.index', $student)"
                        variant="secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </x-buttons.link-button>
                </div>
            </footer>
        </div>
    </div>
@endsection
