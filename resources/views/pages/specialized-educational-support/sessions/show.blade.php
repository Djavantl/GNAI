@extends('layouts.app')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Sessões' => route('specialized-educational-support.sessions.index'),
            $session->professional->person->name => null
        ]" />
    </div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-title">Detalhes da Sessão</h2>
            <p class="text-muted">Informações detalhadas do atendimento especializado.</p>
        </div>
        <div class="d-flex gap-2">
            @if($session->status !== 'cancelled' && $session->status !== 'Cancelado')
                <x-buttons.link-button :href="route('specialized-educational-support.sessions.edit', $session->id)" variant="warning">
                   <i class="fas fa-edit" aria-hidden="true"></i>  Editar Sessão
                </x-buttons.link-button>
            @endif
            <x-buttons.link-button :href="route('specialized-educational-support.sessions.index')" variant="secondary">
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
            
            <x-show.info-item label="Status" column="col-md-6">
                <span class="badge" style="background-color: var(--primary-color);">
                    {{ strtoupper($session->status) }}
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
            
            <x-show.info-item label="Tipo de Atendimento" :value="$session->type" isBox="true"/>

            <x-forms.section title="Conteúdo da Sessão" />

            <x-show.info-textarea label="Objetivo da Sessão" column="col-md-12" isBox="true">{{ $session->session_objective }}</x-show.info-textarea>

            @if($session->cancellation_reason)
                <x-show.info-textarea label="Motivo do Cancelamento" column="col-md-12" isBox="true">
                    {{ $session->cancellation_reason }}
                </x-show.info-textarea>
            @endif

            {{-- MODAL DE CANCELAMENTO --}}
            <div class="modal fade" id="modalCancelSessao" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="{{ route('specialized-educational-support.sessions.cancel', $session->id) }}" method="POST">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title">Confirmar Cancelamento</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>Tem certeza que deseja cancelar esta sessão? Esta ação enviará um e-mail de notificação para os participantes.</p>
                                
                                <div class="form-group">
                                    <label for="cancellation_reason" class="form-label">Motivo do Cancelamento *</label>
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
            <div class="col-12 border-top p-4  d-flex justify-content-end gap-3">
                @if($session->status !== 'cancelled' && $session->status !== 'Cancelado')
                    <x-buttons.submit-button variant="danger" data-bs-toggle="modal" data-bs-target="#modalCancelSessao" type="button">
                        <i class="fas fa-times" aria-hidden="true"></i> Cancelar Sessão
                    </x-buttons.submit-button>
                @endif
                {{-- Lógica do Registro --}}
                @if($session->sessionRecord)
                    <x-buttons.link-button
                        :href="route('specialized-educational-support.session-records.show', $session->sessionRecord->id)"
                        variant="dark"
                        
                    >
                       <i class="fas fa-eye" aria-hidden="true"></i>  Ver Registro
                    </x-buttons.link-button>
                @else
                    <x-buttons.link-button
                        :href="route('specialized-educational-support.session-records.create', $session->id)"
                        variant="new"
                    >
                        <i class="fas fa-plus" aria-hidden="true"></i> Criar Registro
                    </x-buttons.link-button>
                @endif
                 <form action="{{ route('specialized-educational-support.sessions.destroy', $session->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <x-buttons.submit-button variant="danger" onclick="return confirm('Excluir esta sessão permanentemente?')">
                        <i class="fas fa-trash" aria-hidden="true"></i> Excluir
                    </x-buttons.submit-button>
                </form>
            </div>
        </div>
    </div>
@endsection