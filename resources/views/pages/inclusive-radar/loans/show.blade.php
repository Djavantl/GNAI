@extends('layouts.app')

@section('content')
    {{-- Cabeçalho --}}
    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Detalhes do Empréstimo</h2>
            <p class="text-muted">Visualize as informações do empréstimo, prazos e histórico do recurso.</p>
        </div>

        <div class="text-end">
            <span class="d-block text-muted small uppercase fw-bold">ID no Sistema</span>
            <span class="badge bg-purple fs-6">{{ $loan->id }}</span>
        </div>
    </div>

    {{-- Alerta de Atraso --}}
    @if($loan->status === 'active' && $loan->due_date->isPast())
        <div class="alert alert-warning border-0 shadow-sm mb-4 d-flex align-items-center gap-3">
            <i class="fas fa-clock fa-spin fs-4"></i>
            <div>
                <p class="mb-0 fw-bold">Atenção: Este item está com a devolução atrasada!</p>
                <small>O prazo encerrou em {{ $loan->due_date->format('d/m/Y') }}.</small>
            </div>
        </div>
    @endif

    <div class="mt-3">
        <div class="custom-table-card bg-white shadow-sm">

            {{-- SEÇÃO 1: Recurso Emprestado --}}
            <x-forms.section title="Recurso Emprestado" />
            <div class="row g-3 mb-4">
                <x-show.info-item label="Nome do Recurso" column="col-md-6" isBox="true">
                    {{ $loan->loanable->name ?? ($loan->loanable->title ?? 'Item não identificado') }}
                </x-show.info-item>

                <x-show.info-item label="Patrimônio / Código" column="col-md-6" isBox="true">
                    {{ $loan->loanable->asset_code ?? 'N/A' }}
                </x-show.info-item>

                <x-show.info-item label="Tipo de Recurso" column="col-md-6" isBox="true">
                    {{ $loan->loanable_type === 'App\Models\InclusiveRadar\AssistiveTechnology' ? 'Tecnologia Assistiva' : 'Material Educacional' }}
                </x-show.info-item>

                <x-show.info-item label="Quantidade Emprestada" column="col-md-6" isBox="true">
                    {{ $loan->quantity ?? 1 }}
                </x-show.info-item>
            </div>

            {{-- SEÇÃO 2: Responsáveis --}}
            <x-forms.section title="Responsáveis e Beneficiário" />
            <div class="row g-3 mb-4">
                <x-show.info-item label="Estudante" column="col-md-6" isBox="true">
                    {{ $loan->student->person->name ?? '---' }}
                </x-show.info-item>

                <x-show.info-item label="Profissional Responsável" column="col-md-6" isBox="true">
                    {{ $loan->professional->person->name ?? '---' }}
                </x-show.info-item>
            </div>

            {{-- SEÇÃO 3: Datas e Status --}}
            <x-forms.section title="Prazos e Observações" />
            <div class="row g-3 mb-4">
                <x-show.info-item label="Data do Empréstimo" column="col-md-6" isBox="true">
                    {{ $loan->loan_date->format('d/m/Y H:i') }}
                </x-show.info-item>

                <x-show.info-item label="Data Prevista de Devolução" column="col-md-6" isBox="true">
                    {{ $loan->due_date->format('d/m/Y') }}
                </x-show.info-item>

                <x-show.info-item label="Data de Devolução Real" column="col-md-6" isBox="true">
                    {{ $loan->return_date?->format('d/m/Y H:i') ?? 'Não devolvido' }}
                </x-show.info-item>

                <x-show.info-item label="Status do Empréstimo" column="col-md-6" isBox="true">
                    @php
                        $statusLabels = [
                            'active' => 'Ativo (Com o aluno)',
                            'returned' => 'Devolvido (No prazo)',
                            'late' => 'Devolvido (Com atraso)',
                            'damaged' => 'Devolvido (Com Avaria)'
                        ];
                    @endphp
                    {{ $statusLabels[$loan->status] ?? $loan->status }}
                </x-show.info-item>
            </div>

            <div class="row g-3 mb-4">
                <x-show.info-item label="Observações" column="col-md-12" isBox="true">
                    {{ $loan->observation ?? '---' }}
                </x-show.info-item>
            </div>

            {{-- Rodapé de Ações --}}
            <div class="col-12 border-top p-4 d-flex justify-content-between align-items-center bg-light no-print">
                <div class="text-muted small">
                    <i class="fas fa-id-card me-1"></i> ID do Sistema: #{{ $loan->id }}
                </div>

                <div class="d-flex gap-3">
                    <x-buttons.link-button :href="route('inclusive-radar.loans.edit', $loan)" variant="warning">
                        Editar Registro
                    </x-buttons.link-button>

                    <x-buttons.link-button :href="route('inclusive-radar.loans.index')" variant="secondary">
                        Voltar para Lista
                    </x-buttons.link-button>
                </div>
            </div>

        </div>
    </div>
@endsection
