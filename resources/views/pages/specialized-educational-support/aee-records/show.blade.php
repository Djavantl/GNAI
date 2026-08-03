@extends('layouts.master')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Agendamentos' => route('specialized-educational-support.sessions.index'),
            'Agendamento #' . $aeeRecord->attendance_session_id => route('specialized-educational-support.sessions.show', $aeeRecord->attendance_session_id),
            'Registro' => null
        ]" />
    </div>

    {{-- Cabeçalho --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 no-print">
        <div>
            <h2 class="text-title">Registro de Atendimento AEE</h2>
            <p class="text-muted">
                Agendamento #{{ $aeeRecord->attendance_session_id }} • 
                Realizada em: {{ $aeeRecord->attendanceSession->session_date->format('d/m/Y') }}
            </p>
        </div>
        @php
            $canManageAeeRecord = auth()->user()?->professional?->id === $aeeRecord->attendanceSession->professional_id;
        @endphp
        <div class="d-flex gap-2 flex-wrap justify-content-end ms-md-auto">
            <x-buttons.pdf-button class="ms-3" :href="route('specialized-educational-support.aee-records.pdf', $aeeRecord)" />

            @can('aee-record.update')
            @if($canManageAeeRecord)
            <x-buttons.link-button :href="route('specialized-educational-support.aee-records.edit', $aeeRecord)" variant="warning">
                <i class="fas fa-edit"></i> Editar
            </x-buttons.link-button>
            @endif
            @endcan

            <x-buttons.link-button :href="route('specialized-educational-support.sessions.show', $aeeRecord->attendance_session_id)" variant="secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </x-buttons.link-button>
        </div>
    </div>

    <div class="custom-table-card bg-white shadow-sm overflow-hidden">
        <div class="row g-0">
            
            {{-- INFORMAÇÕES GERAIS --}}
            <x-forms.section title="Execução do Atendimento AEE" />
            
            <x-show.info-item label="Duração" column="col-md-4" isBox="true">
                {{ $aeeRecord->duration }}
            </x-show.info-item>

            <x-show.info-textarea label="Atividades Realizadas" column="col-md-12" isBox="true">
                {{ $aeeRecord->activities_performed }}
            </x-show.info-textarea>

            <x-show.info-textarea label="Estratégias Utilizadas" column="col-md-6" isBox="true">
                {{ $aeeRecord->strategies_used ?? 'N/A' }}
            </x-show.info-textarea>

            <x-show.info-textarea label="Recursos Utilizados" column="col-md-6" isBox="true">
                {{ $aeeRecord->resources_used ?? 'N/A' }}
            </x-show.info-textarea>

            <x-show.info-textarea label="Observações Gerais" column="col-md-12" isBox="true">
                {{ $aeeRecord->general_observations ?? 'N/A'}}
            </x-show.info-textarea>
            

            {{-- AVALIAÇÕES INDIVIDUAIS --}}
            <x-forms.section title="Desempenho Individual" />

            <div class="col-12 p-4">
                <div class="row g-0 border rounded">
                    {{-- Lista Lateral de Alunos --}}
                    <div class="col-md-3 bg-light border-end">
                        <div class="list-group list-group-flush" id="students-list" role="tablist">
                            @foreach($aeeRecord->studentEvaluations as $index => $evaluation)
                                <button type="button" 
                                    class="list-group-item list-group-item-action @if($loop->first) active @endif d-flex justify-content-between align-items-center" 
                                    data-bs-toggle="list" 
                                    data-bs-target="#eval-{{ $index }}">
                                    <span>
                                        {{ $evaluation->student->person->name }}
                                        @if(!$evaluation->is_present)
                                            <br><small class="badge bg-danger">Falta</small>
                                        @endif
                                    </span>
                                    <i class="fas fa-chevron-right small opacity-50"></i>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Conteúdo da Avaliação --}}
                    <div class="col-md-9 p-4 bg-white">
                        <div class="tab-content">
                            @foreach($aeeRecord->studentEvaluations as $index => $evaluation)
                                <div class="tab-pane fade @if($loop->first) show active @endif" id="eval-{{ $index }}">
                                    
                                    @if(!$evaluation->is_present)
                                        <div class="alert alert-danger border-0 d-flex align-items-center">
                                            <i class="fas fa-user-times me-3 fa-2x"></i>
                                            <div>
                                                <h5 class="mb-1">Aluno Ausente</h5>
                                                <p class="mb-0"><strong>Motivo:</strong> {!! \App\Support\RichTextSanitizer::sanitize((string) ($evaluation->absence_reason ?? 'Não justificado.')) !!}</p>
                                            </div>
                                        </div>
                                    @else
                                        <div class="row g-3">
                                            <div class="col-md-12 mb-3">
                                                <h5 class="text-title border-bottom pb-2">Avaliação: {{ $evaluation->student->person->name }}</h5>
                                            </div>
                                            
                                            <x-show.info-textarea label="Participação" column="col-md-12" isBox="true">
                                                {{ $evaluation->student_participation }}
                                            </x-show.info-textarea>

                                            <x-show.info-textarea label="Adaptações Realizadas" column="col-md-12" isBox="true">
                                                {{ $evaluation->adaptations_made ?? 'Nenhuma adaptação informada.' }}
                                            </x-show.info-textarea>

                                            <x-show.info-textarea label="Avaliação do Desenvolvimento" column="col-md-12" isBox="true">
                                                {{ $evaluation->development_evaluation }}
                                            </x-show.info-textarea>

                                            <x-show.info-textarea label="Indicadores de Progresso" column="col-md-12" isBox="true">
                                                {{ $evaluation->progress_indicators ?? 'N/A' }}
                                            </x-show.info-textarea>

                                            <x-show.info-textarea label="Recomendações" column="col-md-6" isBox="true">
                                                {{ $evaluation->recommendations ?? 'Nenhuma recomendação.' }}
                                            </x-show.info-textarea>

                                            <x-show.info-textarea label="Ajustes para Próximo Agendamento" column="col-md-6" isBox="true">
                                                {{ $evaluation->next_session_adjustments ?? 'N/A' }}
                                            </x-show.info-textarea>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <footer class="col-12 border-top p-4 d-flex flex-wrap justify-content-end gap-2 bg-light-subtle">
                <div class="d-flex flex-wrap gap-2" role="group" aria-label="Ações de gestão">
                    @can('aee-record.delete')
                    @if($canManageAeeRecord)
                    <x-buttons.submit-button
                        type="button"
                        variant="danger"
                        data-bs-toggle="modal"
                        data-bs-target="#globalConfirmActionModal"
                        data-confirm-title="Excluir Registro de Atendimento"
                        data-confirm-message="Deseja excluir permanentemente este registro de atendimento?"
                        data-confirm-action="{{ route('specialized-educational-support.aee-records.destroy', $aeeRecord) }}"
                        data-confirm-method="DELETE"
                        data-confirm-submit-text="Confirmar Exclusao"
                        data-confirm-variant="danger"
                    >
                            <i class="fas fa-trash-alt"></i> Excluir
                        </x-buttons.submit-button>
                    @endif
                    @endcan
                    <x-buttons.link-button :href="route('specialized-educational-support.sessions.show', $aeeRecord->attendance_session_id)" variant="secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </x-buttons.link-button>
                </div>
            </footer>
        </div>
    </div>
@endsection
