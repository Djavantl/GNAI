@extends('layouts.master')

@section('content')
    @php
        $session = $pedagogicalRecord->attendanceSession;
        $student = $session->students->first();
    @endphp

    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Agendamentos' => route('specialized-educational-support.sessions.index'),
            'Agendamento #' . $session->id => route('specialized-educational-support.sessions.show', $session),
            'Atendimento Pedagógico' => null
        ]" />
    </div>

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 no-print">
        <div>
            <h2 class="text-title">Atendimento Pedagógico</h2>
            <p class="text-muted">
                Agendamento #{{ $session->id }} •
                Realizado em: {{ $session->session_date->format('d/m/Y') }}
            </p>
        </div>

        @php
            $canManageRecord = auth()->user()?->professional?->id === $session->professional_id;
        @endphp

        <div class="d-flex gap-2 flex-wrap justify-content-end ms-md-auto">
            <x-buttons.pdf-button class="ms-3" :href="route('specialized-educational-support.pedagogical-records.pdf', $pedagogicalRecord)" />

            @can('pedagogical-record.update')
                @if($canManageRecord)
                    <x-buttons.link-button :href="route('specialized-educational-support.pedagogical-records.edit', $pedagogicalRecord)" variant="warning">
                        <i class="fas fa-edit"></i> Editar
                    </x-buttons.link-button>
                @endif
            @endcan

            <x-buttons.link-button :href="route('specialized-educational-support.sessions.show', $session)" variant="secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </x-buttons.link-button>
        </div>
    </div>

    <div class="custom-table-card bg-white shadow-sm overflow-hidden">
        <div class="row g-0">
            <x-forms.section title="Identificação do Estudante" />

            <x-show.info-item label="Aluno" column="col-md-6" isBox="true">
                {{ $student?->person?->name ?? 'Aluno não informado' }}
            </x-show.info-item>

            <x-show.info-item label="Profissional" column="col-md-6" isBox="true">
                {{ $session->professional?->person?->name ?? 'Profissional não informado' }}
            </x-show.info-item>

            <x-show.info-item label="Horário" column="col-md-12" isBox="true">
                {{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }}
                às
                {{ $session->end_time ? \Carbon\Carbon::parse($session->end_time)->format('H:i') : '--:--' }}
            </x-show.info-item>

            @if($pedagogicalRecord->guardians->isNotEmpty())
                <x-show.info-item label="Responsáveis participantes" column="col-md-12" isBox="true">
                    {{ $pedagogicalRecord->guardian_names }}
                </x-show.info-item>
            @endif

            <x-forms.section title="Acompanhamento Pedagógico" />

            <x-show.info-textarea label="Motivo do Acompanhamento" column="col-md-12" isBox="true">
                {!! $pedagogicalRecord->follow_up_reason ?? 'Não informado.' !!}
            </x-show.info-textarea>

            <x-show.info-item label="Período/Duração do Acompanhamento" column="col-md-6" isBox="true">
                {{ $pedagogicalRecord->duration }}
            </x-show.info-item>

            <x-show.info-item label="Presença do Estudante no Atendimento" column="col-md-6" isBox="true">
                @if($pedagogicalRecord->is_present)
                    <span class="badge bg-success">
                        <i class="fas fa-check-circle me-1"></i> Presente
                    </span>
                @else
                    <span class="badge bg-danger">
                        <i class="fas fa-times-circle me-1"></i> Ausente
                    </span>
                @endif
            </x-show.info-item>

            @if(!$pedagogicalRecord->is_present)
                <x-show.info-textarea label="Motivo da Ausência" column="col-md-12" isBox="true">
                    {!! $pedagogicalRecord->absence_reason ?? 'Não informado.' !!}
                </x-show.info-textarea>
            @endif

            @if($pedagogicalRecord->is_present || $pedagogicalRecord->guardians->isNotEmpty())
                <x-show.info-textarea label="Registro do Acompanhamento Pedagógico Sistemático" column="col-md-12" isBox="true">
                    {!! $pedagogicalRecord->systematic_pedagogical_follow_up_record !!}
                </x-show.info-textarea>

                <x-show.info-textarea label="Estratégias e Recursos Adotados (quando necessário)" column="col-md-12" isBox="true">
                    {!! $pedagogicalRecord->strategies_and_resources_adopted ?? 'N/A' !!}
                </x-show.info-textarea>

                <x-show.info-textarea label="Encaminhamentos Realizados" column="col-md-12" isBox="true">
                    {!! $pedagogicalRecord->referrals_made ?? 'N/A' !!}
                </x-show.info-textarea>

                <x-show.info-textarea label="Observações Complementares" column="col-md-12" isBox="true">
                    {!! $pedagogicalRecord->complementary_observations ?? 'N/A' !!}
                </x-show.info-textarea>
            @endif

            <footer class="col-12 border-top p-4 d-flex flex-wrap justify-content-end gap-2 bg-light-subtle">
                @can('pedagogical-record.delete')
                    @if($canManageRecord)
                        <x-buttons.submit-button
                            type="button"
                            variant="danger"
                            data-bs-toggle="modal"
                            data-bs-target="#globalConfirmActionModal"
                            data-confirm-title="Excluir Atendimento Pedagógico"
                            data-confirm-message="Deseja excluir este atendimento pedagógico?"
                            data-confirm-action="{{ route('specialized-educational-support.pedagogical-records.destroy', $pedagogicalRecord) }}"
                            data-confirm-method="DELETE"
                            data-confirm-submit-text="Confirmar Exclusao"
                            data-confirm-variant="danger"
                        >
                            <i class="fas fa-trash-alt"></i> Excluir
                        </x-buttons.submit-button>
                    @endif
                @endcan

                <x-buttons.link-button :href="route('specialized-educational-support.sessions.show', $session)" variant="secondary">
                    <i class="fas fa-arrow-left"></i> Voltar
                </x-buttons.link-button>
            </footer>
        </div>
    </div>
@endsection
