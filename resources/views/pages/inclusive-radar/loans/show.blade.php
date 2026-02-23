@extends('layouts.master')

@section('title', "Empréstimo $loan->id ")

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Empréstimos' => route('inclusive-radar.loans.index'),
            $loan->id => null
        ]" />
    </div>

    {{-- Cabeçalho --}}
    <div class="d-flex justify-content-between mb-3 align-items-center">
        <div>
            <h2 class="text-title">Detalhes do Empréstimo</h2>
            <p class="text-muted">Visualize as informações do empréstimo, prazos e histórico do recurso.</p>
        </div>

        <div>
            <x-buttons.link-button :href="route('inclusive-radar.loans.edit', $loan)" variant="warning">
                <i class="fas fa-edit"></i> Editar
            </x-buttons.link-button>
            <x-buttons.link-button :href="route('inclusive-radar.loans.index')" variant="secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </x-buttons.link-button>
        </div>
    </div>

    {{-- Alerta de Atraso --}}
    @if($loan->status->value === 'active' && $loan->due_date->isPast())
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

            <div class="col-md-12 mb-4 px-4">
                <div class="p-3 border rounded bg-light d-flex align-items-center gap-3">
                    <div class="bg-purple-dark text-white p-3 rounded shadow-sm" style="background-color: #4c1d95;">
                        <i class="fas {{ $loan->loanable_type === 'App\Models\InclusiveRadar\AssistiveTechnology' ? 'fa-microchip' : 'fa-book' }} fa-lg"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold text-purple-dark">
                            {{ $loan->loanable->name ?? ($loan->loanable->title ?? ($loan->loanable->description ?? 'Item não identificado')) }}
                        </h5>
                        <small class="text-muted text-uppercase">Patrimônio: {{ $loan->loanable->asset_code ?? 'N/A' }}</small>
                    </div>
                </div>
            </div>

            {{-- SEÇÃO 2: Responsáveis e Usuário --}}
            <x-forms.section title="Beneficiário e Responsável" />
            <div class="row g-3 mb-4 px-4">
                <x-show.info-item label="Estudante (Beneficiário)" column="col-md-6" isBox="true">
                    {{ $loan->student->person->name ?? '---' }}
                </x-show.info-item>

                <x-show.info-item label="Profissional (Beneficiário)" column="col-md-6" isBox="true">
                    {{ $loan->professional->person->name ?? '---' }}
                </x-show.info-item>

                <x-show.info-item label="Usuário Autenticado (Responsável)" column="col-md-12" isBox="true">
                    {{ $loan->user->name ?? '---' }}
                </x-show.info-item>
            </div>

            {{-- SEÇÃO 3: Datas e Status --}}
            <x-forms.section title="Prazos e Observações" />
            <div class="row g-3 mb-4 px-4">
                <x-show.info-item label="Data de Saída" column="col-md-6" isBox="true">
                    {{ $loan->loan_date->format('d/m/Y H:i') }}
                </x-show.info-item>

                <x-show.info-item label="Previsão de Devolução" column="col-md-6" isBox="true">
                    {{ $loan->due_date->format('d/m/Y') }}
                </x-show.info-item>

                @php
                    $currentStatus = $loan->status instanceof \App\Enums\InclusiveRadar\LoanStatus
                        ? $loan->status
                        : \App\Enums\InclusiveRadar\LoanStatus::tryFrom($loan->status);

                    if ($currentStatus === \App\Enums\InclusiveRadar\LoanStatus::ACTIVE && $loan->due_date->isPast()) {
                        $statusLabel = 'Em Atraso';
                    } else {
                        $statusLabel = $currentStatus?->label() ?? $loan->status;
                    }

                    $isOverdue = ($loan->status === \App\Enums\InclusiveRadar\LoanStatus::ACTIVE && $loan->due_date->isPast());
                    $statusColor = $isOverdue ? 'danger' : $loan->status->color();
                @endphp

                <x-show.info-item label="Status do Empréstimo" column="col-md-6" isBox="true">
                    <span class="badge bg-{{ $statusColor }}-subtle text-{{ $statusColor }}-emphasis border px-3">
                        {{ $statusLabel }}
                    </span>
                </x-show.info-item>

                <x-show.info-item label="Data Real da Devolução" column="col-md-6" isBox="true">
                    {{ $loan->return_date?->format('d/m/Y H:i') ?? 'Não devolvido' }}
                </x-show.info-item>
            </div>

            <div class="row g-3 mb-4 px-4">
                <x-show.info-item label="Observações" column="col-md-12" isBox="true">
                    {{ $loan->observation ?? '---' }}
                </x-show.info-item>
            </div>

            {{-- Rodapé de Ações --}}
            <div class="col-12 border-top p-4 d-flex justify-content-between align-items-center bg-light no-print">
                <div class="text-muted small d-flex align-items-center">
                    <i class="fas fa-id-card me-1" aria-hidden="true"></i> ID no Sistema: #{{ $loan->id }}
                    <x-buttons.pdf-button
                        :href="route('inclusive-radar.loans.pdf', $loan)"
                        class="ms-3"
                    />
                </div>

                <div class="d-flex gap-3">
                    {{-- BOTÃO DE DEVOLUÇÃO ABRE MODAL --}}
                    @if($loan->status->value === 'active')
                        <x-buttons.submit-button type="button" variant="success" data-bs-toggle="modal" data-bs-target="#returnLoanModal">
                            <i class="fas fa-undo"></i> Devolver
                        </x-buttons.submit-button>
                    @endif

                    <form action="{{ route('inclusive-radar.loans.destroy', $loan) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <x-buttons.submit-button
                            variant="danger"
                            onclick="return confirm('Deseja excluir este empréstimo?')"
                        >
                            <i class="fas fa-trash-alt"></i> Excluir
                        </x-buttons.submit-button>
                    </form>

                    <x-buttons.link-button :href="route('inclusive-radar.loans.index')" variant="secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </x-buttons.link-button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL DE DEVOLUÇÃO --}}
    <x-modal id="returnLoanModal" size="md">
        @slot('header')
            Confirmar Devolução
        @endslot

        <form action="{{ route('inclusive-radar.loans.return', $loan) }}" method="POST" id="returnLoanForm">
            @csrf
            @method('PATCH')

            <div class="modal-body px-0 py-3">
                <p class="mb-3">Deseja realmente realizar a devolução deste item?</p>

                <x-forms.checkbox
                    name="is_damaged"
                    label="Item devolvido com avaria"
                />
            </div>
        </form>

        @slot('footer')
            <x-buttons.link-button
                variant="secondary"
                data-bs-dismiss="modal"
            >
                <i class="fas fa-times"></i> Cancelar
            </x-buttons.link-button>

            <x-buttons.submit-button
                variant="success"
                onclick="document.getElementById('returnLoanForm').submit()"
            >
                <i class="fas fa-undo"></i> Devolver
            </x-buttons.submit-button>
        @endslot
    </x-modal>

    @push('scripts')
        @vite('resources/js/pages/inclusive-radar/loans.js')
    @endpush
@endsection
