@extends('layouts.master')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Sessões' => route('specialized-educational-support.sessions.index'),
            'Sessão' => route('specialized-educational-support.sessions.show', $sessionRecord->attendance_session_id),
            'Registro' => null
        ]" />
    </div>

    {{-- Cabeçalho --}}
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <div>
            <h2 class="text-title">Registro de Atendimento</h2>
            <p class="text-muted">
                Sessão #{{ $sessionRecord->attendance_session_id }} • 
                Realizada em: {{ $sessionRecord->attendanceSession->session_date->format('d/m/Y') }}
            </p>
        </div>
        <div class="d-flex gap-2">

            <x-buttons.link-button :href="route('specialized-educational-support.session-records.edit', $sessionRecord)" variant="warning">
                <i class="fas fa-edit"></i> Editar
            </x-buttons.link-button>

            <x-buttons.link-button :href="route('specialized-educational-support.sessions.show', $sessionRecord->attendance_session_id)" variant="secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </x-buttons.link-button>
        </div>
    </div>

    <div class="custom-table-card bg-white shadow-sm overflow-hidden">
        <div class="row g-0">
            
            {{-- INFORMAÇÕES GERAIS --}}
            <x-forms.section title="Execução da Sessão" />
            
            <x-show.info-item label="Duração" column="col-md-4" isBox="true">
                {{ $sessionRecord->duration }}
            </x-show.info-item>

            <x-show.info-textarea label="Atividades Realizadas" column="col-md-12" isBox="true">
                {{ $sessionRecord->activities_performed }}
            </x-show.info-textarea>

            <x-show.info-textarea label="Estratégias Utilizadas" column="col-md-6" isBox="true">
                {{ $sessionRecord->strategies_used ?? 'N/A' }}
            </x-show.info-textarea>

            <x-show.info-textarea label="Recursos Utilizados" column="col-md-6" isBox="true">
                {{ $sessionRecord->resources_used ?? 'N/A' }}
            </x-show.info-textarea>

            <x-show.info-textarea label="Observações Gerais" column="col-md-12" isBox="true">
                {{ $sessionRecord->general_observations ?? 'N/A'}}
            </x-show.info-textarea>
            

            {{-- AVALIAÇÕES INDIVIDUAIS --}}
            <x-forms.section title="Desempenho Individual" />

            <div class="col-12 p-4">
                <div class="row g-0 border rounded">
                    {{-- Lista Lateral de Alunos --}}
                    <div class="col-md-3 bg-light border-end">
                        <div class="list-group list-group-flush" id="students-list" role="tablist">
                            @foreach($sessionRecord->studentEvaluations as $index => $evaluation)
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
                            @foreach($sessionRecord->studentEvaluations as $index => $evaluation)
                                <div class="tab-pane fade @if($loop->first) show active @endif" id="eval-{{ $index }}">
                                    
                                    @if(!$evaluation->is_present)
                                        <div class="alert alert-danger border-0 d-flex align-items-center">
                                            <i class="fas fa-user-times me-3 fa-2x"></i>
                                            <div>
                                                <h5 class="mb-1">Aluno Ausente</h5>
                                                <p class="mb-0"><strong>Motivo:</strong> {{ $evaluation->absence_reason ?? 'Não justificado.' }}</p>
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

                                            <x-show.info-textarea label="Ajustes para Próxima Sessão" column="col-md-6" isBox="true">
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

            <footer class="col-12 border-top p-4 d-flex justify-content-between align-items-center bg-light-subtle">
                <div class="text-muted small d-flex align-items-center">
                    <i class="fas fa-id-card me-1" aria-hidden="true"></i> ID no Sistema: #{{ $sessionRecord->id }}

                    <x-buttons.pdf-button class="ms-3" :href="route('specialized-educational-support.session-records.pdf', $sessionRecord)" />

                </div>
                <div class="d-flex gap-2" role="group" aria-label="Ações de gestão">
                    
                    <form action="{{ route('specialized-educational-support.session-records.destroy', $sessionRecord) }}" method="POST" onsubmit="return confirm('Deseja excluir permanentemente?')">
                        @csrf @method('DELETE')
                        <x-buttons.submit-button variant="danger">
                            <i class="fas fa-trash-alt"></i> Excluir
                        </x-buttons.submit-button>
                    </form>
                    <x-buttons.link-button :href="route('specialized-educational-support.sessions.show', $sessionRecord->attendance_session_id)" variant="secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </x-buttons.link-button>
                </div>
            </footer>
        </div>
    </div>
@endsection