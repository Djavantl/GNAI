@extends('layouts.app')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Sessões' => route('specialized-educational-support.sessions.index'),
            'Sessão' => route('specialized-educational-support.sessions.show', $session),
            'Registro' => null
        ]" />
    </div>

    {{-- Cabeçalho da Página --}}
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <div>
            <h2 class="text-title">Registro de Sessão</h2>
            <p class="text-muted">
                @if($sessionRecord->session && $sessionRecord->session->student)
                    Aluno: {{ $sessionRecord->session->student->person->name }} • 
                    Sessão #{{ $sessionRecord->attendance_sessions_id }}
                @else
                    Registro #{{ $sessionRecord->id }}
                @endif
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('specialized-educational-support.session-records.pdf', $sessionRecord) }}" target="_blank" class="btn-action primary" style="text-decoration: none; display: inline-flex; align-items: center; gap: 5px;">
                <i class="fas fa-file-pdf"></i> Gerar PDF
            </a>    

            <x-buttons.link-button :href="route('specialized-educational-support.session-records.edit', $sessionRecord)" variant="warning">
                <i class="fas fa-edit"></i> Editar Registro
            </x-buttons.link-button>

            <x-buttons.link-button :href="route('specialized-educational-support.sessions.show', $sessionRecord->attendance_sessions_id)" variant="secondary">
                Voltar
            </x-buttons.link-button>
        </div>
    </div>

    <div class="custom-table-card bg-white shadow-sm">
        <div class="row g-0">
            
            {{-- SEÇÃO: INFORMAÇÕES BÁSICAS --}}
            <x-forms.section title="Informações Gerais" />
            
            <x-show.info-item label="Data do Registro" column="col-md-3" isBox="true">
                {{ $sessionRecord->record_date ? \Carbon\Carbon::parse($sessionRecord->record_date)->format('d/m/Y') : '---' }}
            </x-show.info-item>

            <x-show.info-item label="Duração" column="col-md-3" isBox="true">
                {{ $sessionRecord->duration ?? '---' }}
            </x-show.info-item>

            <x-show.info-item label="Participação" column="col-md-3" isBox="true">
                {{ $sessionRecord->student_participation ?? '---' }}
            </x-show.info-item>

            <x-show.info-item label="Engajamento" column="col-md-3" isBox="true">
                {{ $sessionRecord->engagement_level ?? '---' }}
            </x-show.info-item>

            @if($sessionRecord->external_referral_needed)
                <div class="col-12 px-4 mb-4">
                    <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center" style="border-radius: 12px;">
                        <i class="fas fa-exclamation-triangle me-3 fa-lg text-warning"></i>
                        <strong class="text-dark">Atenção: Encaminhamento Externo Necessário</strong>
                    </div>
                </div>
            @endif

            {{-- SEÇÃO: ATIVIDADES E ESTRATÉGIAS --}}
            <x-forms.section title="Atividades e Estratégias" />

            <x-show.info-textarea label="Atividades Realizadas" column="col-md-12" isBox="true">
                {{ $sessionRecord->activities_performed }}
            </x-show.info-textarea>

            <x-show.info-textarea label="Estratégias Utilizadas" column="col-md-6" isBox="true">
                {{ $sessionRecord->strategies_used ?? 'Não informado' }}
            </x-show.info-textarea>

            <x-show.info-textarea label="Recursos Utilizados" column="col-md-6" isBox="true">
                {{ $sessionRecord->resources_used ?? 'Não informado' }}
            </x-show.info-textarea>

            <x-show.info-textarea label="Adaptações Realizadas" column="col-md-12" isBox="true">
                {{ $sessionRecord->adaptations_made ?? 'Nenhuma adaptação informada.' }}
            </x-show.info-textarea>

            {{-- SEÇÃO: COMPORTAMENTO --}}
            <x-forms.section title="Comportamento e Observações" />

            <x-show.info-textarea label="Comportamento Observado" column="col-md-6" isBox="true">
                {{ $sessionRecord->observed_behavior ?? 'Não informado' }}
            </x-show.info-textarea>

            <x-show.info-textarea label="Resposta às Atividades" column="col-md-6" isBox="true">
                {{ $sessionRecord->response_to_activities ?? 'Não informado' }}
            </x-show.info-textarea>

            {{-- SEÇÃO: AVALIAÇÃO --}}
            <x-forms.section title="Avaliação do Desenvolvimento" />

            <x-show.info-textarea label="Avaliação do Desenvolvimento" column="col-md-12" isBox="true">
                {{ $sessionRecord->development_evaluation }}
            </x-show.info-textarea>

            <x-show.info-textarea label="Indicadores de Progresso" column="col-md-12" isBox="true">
                {{ $sessionRecord->progress_indicators ?? 'Não informado' }}
            </x-show.info-textarea>

            {{-- SEÇÃO: RECOMENDAÇÕES --}}
            <x-forms.section title="Recomendações e Ajustes" />

            <x-show.info-textarea label="Recomendações" column="col-md-6" isBox="true">
                {{ $sessionRecord->recommendations ?? 'Não informado' }}
            </x-show.info-textarea>

            <x-show.info-textarea label="Ajustes para Próxima Sessão" column="col-md-6" isBox="true">
                {{ $sessionRecord->next_session_adjustments ?? 'Não informado' }}
            </x-show.info-textarea>

            @if($sessionRecord->general_observations)
                <x-show.info-textarea label="Observações Gerais" column="col-md-12" isBox="true">
                    {{ $sessionRecord->general_observations }}
                </x-show.info-textarea>
            @endif

            {{-- Rodapé / Ações Finais --}}
            <div class="col-12 border-top p-4 d-flex justify-content-between align-items-center bg-light no-print">
                <small class="text-muted italic">
                    <i class="fas fa-clock me-1"></i>
                    Última atualização: {{ $sessionRecord->updated_at->format('d/m/Y H:i') }}
                </small>
                
                <div class="d-flex gap-3">
                    <form action="{{ route('specialized-educational-support.session-records.destroy', $sessionRecord) }}" 
                          method="POST" 
                          onsubmit="return confirm('Tem certeza que deseja excluir este registro?')">
                        @csrf
                        @method('DELETE')
                        <x-buttons.submit-button variant="danger">
                            <i class="fas fa-trash-alt me-1"></i> Excluir Registro
                        </x-buttons.submit-button>
                    </form>

                    <x-buttons.link-button :href="route('specialized-educational-support.session-records.edit', $sessionRecord)" variant="warning">
                        <i class="fas fa-edit me-1"></i> Editar Registro
                    </x-buttons.link-button>
                </div>
            </div>
        </div>
    </div>
@endsection