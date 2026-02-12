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
            <x-buttons.link-button :href="route('specialized-educational-support.sessions.edit', $session->id)" variant="warning">
                Editar Sessão
            </x-buttons.link-button>
            
            <x-buttons.link-button :href="route('specialized-educational-support.sessions.index')" variant="secondary">
                Voltar
            </x-buttons.link-button>
        </div>
    </div>

    {{-- Reutilizando seu componente de card de formulário para manter o estilo --}}
    <div class="custom-table-card bg-white">
        <div class="row g-0">
            
            <x-forms.section title="Identificação" />
            
            <x-show.info-item label="Aluno" :value="$session->student->person->name" column="col-md-6" isBox="true"/>
            
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

            {{-- Rodapé do Card --}}
            <div class="col-12 border-top p-4  d-flex justify-content-end gap-3">
                 <form action="{{ route('specialized-educational-support.sessions.destroy', $session->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <x-buttons.submit-button variant="danger" onclick="return confirm('Excluir esta sessão permanentemente?')">
                        Excluir Registro
                    </x-buttons.submit-button>
                </form>
            </div>
        </div>
    </div>
@endsection