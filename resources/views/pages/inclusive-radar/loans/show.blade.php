@extends('layouts.master')

@section('title', "Detalhes - Empréstimo #$loan->id")

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Empréstimos' => route('inclusive-radar.loans.index'),
            $loan->id => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3 align-items-center">
        <div>
            <h2 class="text-title">Detalhes do Empréstimo</h2>
            <p class="text-muted">Visualize as informações, prazos e histórico do recurso.</p>
        </div>

        <div class="d-flex gap-2">
            <x-buttons.link-button :href="route('inclusive-radar.loans.edit', $loan)" variant="warning">
                <i class="fas fa-edit"></i> Editar
            </x-buttons.link-button>

            <x-buttons.link-button :href="route('inclusive-radar.loans.index')" variant="secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </x-buttons.link-button>
        </div>
    </div>

    @if($isOverdue)
        <div class="alert alert-warning border-0 shadow-sm mb-4 d-flex align-items-center gap-3">
            <i class="fas fa-clock fa-spin fs-4"></i>
            <div>
                <p class="mb-0 fw-bold">Atenção: Este item está com a devolução atrasada!</p>
                <small>O prazo encerrou em {{ $loan->due_date->format('d/m/Y') }}.</small>
            </div>
        </div>
    @endif

    <div class="mt-3">
        <div class="custom-table-card bg-white shadow-sm border rounded">

            <x-forms.section title="Recurso Emprestado" />

            <div class="col-md-12 mb-4 px-4">
                <div class="p-3 border rounded bg-light d-flex align-items-center gap-3">
                    <div class="bg-purple-dark text-white p-3 rounded shadow-sm" style="background-color: #4c1d95;">
                        <i class="fas {{ $loan->loanable_type === 'App\Models\InclusiveRadar\AssistiveTechnology' ? 'fa-microchip' : 'fa-book' }} fa-lg"></i>
                    </div>

                    <div>
                        <h5 class="mb-0 fw-bold text-purple-dark">
                            {{ $loan->loanable->name ?? ($loan->loanable->title ?? 'Item não identificado') }}
                        </h5>
                        <small class="text-muted text-uppercase">Patrimônio: {{ $loan->loanable->asset_code ?? 'N/A' }}</small>
                    </div>
                </div>
            </div>

            <x-forms.section title="Beneficiário e Responsável" />
            <div class="row g-3 mb-4 px-4">
                <x-show.info-item label="Estudante (Beneficiário)" column="col-md-6" isBox="true">
                    {{ $loan->student?->person?->name ?? 'Não se aplica' }}
                </x-show.info-item>

                <x-show.info-item label="Profissional (Beneficiário)" column="col-md-6" isBox="true">
                    {{ $loan->professional?->person?->name ?? 'Não se aplica' }}
                </x-show.info-item>

                <x-show.info-item label="Usuário Responsável" column="col-md-12" isBox="true">
                    {{ $loan->user->name ?? '---' }}
                </x-show.info-item>
            </div>

            <x-forms.section title="Prazos e Status" />
            <div class="row g-3 mb-4 px-4">
                <x-show.info-item label="Data de Saída" column="col-md-6" isBox="true">
                    {{ $loan->loan_date->format('d/m/Y H:i') }}
                </x-show.info-item>

                <x-show.info-item label="Previsão de Devolução" column="col-md-6" isBox="true">
                    {{ $loan->due_date->format('d/m/Y') }}
                </x-show.info-item>

                <x-show.info-item label="Status do Empréstimo" column="col-md-6" isBox="true">
                    <span class="text-{{ $statusColor }} fw-bold text-uppercase">
                        {{ $statusLabel }}
                    </span>
                </x-show.info-item>

                <x-show.info-item label="Data Real da Devolução" column="col-md-6" isBox="true">
                    {{ $loan->return_date?->format('d/m/Y H:i') ?? 'Não devolvido' }}
                </x-show.info-item>

                <x-show.info-textarea label="Observações" column="col-md-12" :value="$loan->observation ?? 'Nenhuma observação registrada.'" :rich="true"/>
            </div>

            <div class="col-12 border-top p-4 d-flex justify-content-between align-items-center bg-light no-print">
                <div class="text-muted small d-flex align-items-center">
                    <i class="fas fa-fingerprint me-1"></i> ID: #{{ $loan->id }}
                    <x-buttons.pdf-button :href="route('inclusive-radar.loans.pdf', $loan)" class="ms-3" />
                </div>

                <div class="d-flex gap-2">
                    @if($loan->status->value === 'active')
                        <x-buttons.submit-button type="button" variant="success" data-bs-toggle="modal" data-bs-target="#returnLoanModal">
                            <i class="fas fa-undo"></i> Devolver
                        </x-buttons.submit-button>
                    @endif

                    <form action="{{ route('inclusive-radar.loans.destroy', $loan) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <x-buttons.submit-button variant="danger" onclick="return confirm('Excluir este empréstimo permanentemente?')">
                            <i class="fas fa-trash-alt"></i> Excluir
                        </x-buttons.submit-button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <x-modal id="returnLoanModal" title="Confirmar Devolução">
        <form action="{{ route('inclusive-radar.loans.return', $loan) }}" method="POST" id="returnLoanForm">
            @csrf
            @method('PATCH')
            <div class="py-2">
                <p>Deseja confirmar a devolução deste recurso ao acervo?</p>
                <x-forms.checkbox name="is_damaged" label="Item devolvido com avarias ou danos" />
            </div>
        </form>

        @slot('footer')
            <x-buttons.link-button variant="secondary" data-bs-dismiss="modal">Cancelar</x-buttons.link-button>
            <x-buttons.submit-button variant="success" onclick="document.getElementById('returnLoanForm').submit()">
                Confirmar Devolução
            </x-buttons.submit-button>
        @endslot
    </x-modal>
@endsection
