@extends('layouts.app')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Agendamentos' => route('specialized-educational-support.sessions.index'),
            'Agendamento #' . $session->id => null
        ]" />
    </div>
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="text-title">Detalhes do Agendamento</h2>
            <p class="text-muted">Informações detalhadas do atendimento especializado.</p>
        </div>
        @php
            $canManageSessionRecord = auth()->user()?->professional?->id === $session->professional_id;
            $sessionStatus = mb_strtolower(trim((string) $session->status));
            $isScheduledSession = in_array($sessionStatus, ['agendada', 'agendado', 'scheduled'], true);
            $canManageSessionLifecycle = auth()->id() === $session->creator_id;
        @endphp
        <div class="d-flex gap-2 flex-wrap justify-content-end ms-md-auto">
            @can('session.update')
            @if($isScheduledSession && $canManageSessionLifecycle)
                <x-buttons.link-button :href="route('specialized-educational-support.sessions.edit', $session->id)" variant="warning">
                   <i class="fas fa-edit" aria-hidden="true"></i>  Editar 
                </x-buttons.link-button>
            @endif
            @endcan
            <x-buttons.link-button :href="auth()->user()->is_admin
                    ? route('specialized-educational-support.sessions.index')
                    : route('specialized-educational-support.sessions.my-sessions')" variant="secondary">
                <i class="fas fa-arrow-left" aria-hidden="true"></i> Voltar
            </x-buttons.link-button>
        </div>
    </div>

    {{-- Reutilizando seu componente de card de formulário para manter o estilo --}}
    <div class="custom-table-card bg-white">
        <div class="row g-0">
            
            <x-forms.section title="Identificação" />
            
            <x-show.info-item label="Alunos" column="col-md-6" isBox="true">
                <div class="d-flex flex-column gap-1">
                    @foreach($session->students as $student)
                        <div class="text-purple">{{ $student->person->name }}</div>
                    @endforeach
                </div>
            </x-show.info-item>
            
            <x-show.info-item label="Profissional" :value="$session->professional->person->name" column="col-md-6" isBox="true"/>
            
            <x-show.info-item label="Status" column="col-md-6" isBox="true">
                @php
                    $statusValue = strtolower($session->status);
                    $statusColor = match($statusValue) {
                        'agendada', 'agendado' => 'warning',
                        'realizada', 'realizado' => 'success',
                        'cancelada', 'cancelled', 'cancelado' => 'danger',
                        default => 'warning'
                    };
                @endphp
                <span class="text-{{ $statusColor }} fw-bold">
                    {{ $session->statusLabel() }}
                </span>
            </x-show.info-item>

            <x-forms.section title="Agendamento e Local"  />

            <x-show.info-item label="Data" isBox="true">
                {{ \Carbon\Carbon::parse($session->session_date)->format('d/m/Y') }}
            </x-show.info-item>

            <x-show.info-item label="Horário" isBox="true">
                {{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }} às {{ $session->end_time ? \Carbon\Carbon::parse($session->end_time)->format('H:i') : '--:--' }}
            </x-show.info-item>

            <x-show.info-item label="Local" :value="$session->location" isBox="true"/>
            
            <x-show.info-item label="Tipo de Atendimento" :value="$session->attendanceTypeLabel()" isBox="true"/>

            <x-show.info-item label="Formato" :value="$session->typeLabel()" isBox="true"/>

            <x-forms.section title="Conteúdo do Agendamento" />

            <x-show.info-textarea label="Objetivo do Agendamento" column="col-md-12" isBox="true">{{ $session->session_objective }}</x-show.info-textarea>

            @if($session->cancellation_reason)
                <x-show.info-textarea label="Motivo do Cancelamento" column="col-md-12" isBox="true">
                    {{ $session->cancellation_reason }}
                </x-show.info-textarea>
            @endif

            {{-- MODAL DE CANCELAMENTO --}}
            <div class="modal fade" id="modalCancelAgendamento" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="{{ route('specialized-educational-support.sessions.cancel', $session->id) }}" method="POST">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title">Confirmar Cancelamento</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>Tem certeza que deseja cancelar este agendamento? Esta ação enviará um e-mail de notificação para os participantes.</p>
                                
                                <div class="form-group">
                                    <label for="cancellation_reason" class="form-label">Motivo do Cancelamento <span class="text-danger">*</span></label>
                                    <textarea 
                                        name="cancellation_reason" 
                                        id="cancellation_reason" 
                                        class="form-control" 
                                        rows="3" 
                                        required 
                                        placeholder="Descreva o motivo obrigatório..."></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Voltar</button>
                                <button type="submit" class="btn btn-danger">Confirmar Cancelamento</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Rodapé do Card --}}
            <div class="col-12 border-top p-4  d-flex flex-wrap justify-content-end gap-2">
                @if($isScheduledSession)
                    @can('session.update')
                    @if($canManageSessionLifecycle)
                        <x-buttons.submit-button variant="dark" data-bs-toggle="modal" data-bs-target="#modalCancelAgendamento" type="button">
                            <i class="fas fa-times" aria-hidden="true"></i> Cancelar Agendamento
                        </x-buttons.submit-button>
                    @endif
                    @endcan
                @endif
                 {{-- Lógica dos registros por tipo de atendimento --}}
                @if($session->isAeeAttendance())
                    @if($session->sessionRecord)
                        @can('session-record.view')
                            <x-buttons.link-button
                                :href="route('specialized-educational-support.session-records.show', $session->sessionRecord->id)"
                                variant="info"
                            >
                                <i class="fas fa-eye" aria-hidden="true"></i> Ver Atendimento AEE
                            </x-buttons.link-button>
                        @endcan
                    @else
                        @can('session-record.create')
                            @if($canManageSessionRecord && $isScheduledSession)
                                <x-buttons.link-button
                                    :href="route('specialized-educational-support.session-records.create', $session->id)"
                                    variant="new"
                                >
                                    <i class="fas fa-plus" aria-hidden="true"></i> Criar Atendimento AEE
                                </x-buttons.link-button>
                            @endif
                        @endcan
                    @endif
                @elseif($session->isPedagogicalAttendance())
                    @if($session->pedagogicalRecord)
                        @can('session-record.view')
                            <x-buttons.link-button
                                :href="route('specialized-educational-support.pedagogical-records.show', $session->pedagogicalRecord)"
                                variant="info"
                            >
                                <i class="fas fa-eye" aria-hidden="true"></i> Ver Atendimento Pedagógico
                            </x-buttons.link-button>
                            <x-buttons.link-button
                                :href="route('specialized-educational-support.pedagogical-records.pdf', $session->pedagogicalRecord)"
                                variant="secondary"
                                target="_blank"
                            >
                                <i class="fas fa-file-pdf" aria-hidden="true"></i> PDF Pedagógico
                            </x-buttons.link-button>
                        @endcan
                    @else
                        @can('session-record.create')
                            @if($canManageSessionRecord && $isScheduledSession)
                                <x-buttons.link-button
                                    :href="route('specialized-educational-support.pedagogical-records.create', $session)"
                                    variant="new"
                                >
                                    <i class="fas fa-plus" aria-hidden="true"></i> Criar Registro Pedagógico
                                </x-buttons.link-button>
                            @endif
                        @endcan
                    @endif
                @endif
                @can('session.delete')
                @if($canManageSessionLifecycle)
                <x-buttons.submit-button
                    type="button"
                    variant="danger"
                    data-bs-toggle="modal"
                    data-bs-target="#globalConfirmActionModal"
                    data-confirm-title="Excluir Agendamento"
                    data-confirm-message="Excluir este agendamento permanentemente?"
                    data-confirm-action="{{ route('specialized-educational-support.sessions.destroy', $session->id) }}"
                    data-confirm-method="DELETE"
                    data-confirm-submit-text="Confirmar Exclusao"
                    data-confirm-variant="danger"
                >
                        <i class="fas fa-trash" aria-hidden="true"></i> Excluir
                    </x-buttons.submit-button>
                @endif
                @endcan
            </div>
        </div>
    </div>
@endsection
