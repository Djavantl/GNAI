@extends('layouts.master')

@section('title', "Editar - Empréstimo $loan->id ")

@section('content')
    @php
        $isReturned = $loan->status !== 'active' || $loan->return_date !== null;
    @endphp

    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Empréstimos' => route('inclusive-radar.loans.index'),
            $loan->id => route('inclusive-radar.loans.show', $loan),
            'Editar' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3 align-items-center">
        <div>
            <h2 class="text-title">Editar Registro de Empréstimo</h2>
            <p class="text-muted">Atualize prazos, status ou registre a devolução do recurso.</p>
        </div>
        <div>
            <x-buttons.link-button href="{{ route('inclusive-radar.loans.index') }}" variant="secondary">
                <i class="fas fa-times"></i> Cancelar
            </x-buttons.link-button>
        </div>
    </div>

    {{-- Alerta de edição restrita --}}
    @if($isReturned)
        <div class="alert alert-info border-0 shadow-sm mb-4 d-flex align-items-center gap-3">
            <i class="fas fa-info-circle fs-4"></i>
            <div>
                <p class="mb-0 fw-bold">Atenção: Este empréstimo já foi registrado.</p>
                <small>Apenas o campo de observações pode ser atualizado nesta tela.</small>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm mb-4">
            <p class="font-weight-bold mb-1"><i class="fas fa-exclamation-triangle me-2"></i> Atenção: Existem erros no preenchimento.</p>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if($loan->status === 'active' && $loan->due_date->isPast())
        <div class="alert alert-warning border-0 shadow-sm mb-4 d-flex align-items-center gap-3">
            <i class="fas fa-clock fa-spin fs-4"></i>
            <div>
                <p class="mb-0 font-weight-bold">Atenção: Este item está com a devolução atrasada!</p>
                <small>O prazo encerrou em {{ $loan->due_date->format('d/m/Y') }}.</small>
            </div>
        </div>
    @endif

    <div class="mt-3">
        <x-forms.form-card action="{{ route('inclusive-radar.loans.update', $loan) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Campos ocultos --}}
            <input type="hidden" name="loanable_id" value="{{ $loan->loanable_id }}">
            <input type="hidden" name="loanable_type" value="{{ $loan->loanable_type }}">

            {{-- Recurso --}}
            <x-forms.section title="Recurso Emprestado" />
            <div class="col-md-12 mb-4">
                <div class="p-3 border rounded bg-light d-flex align-items-center gap-3">
                    <div class="bg-purple-dark text-white p-3 rounded shadow-sm" style="background-color: #4c1d95;">
                        <i class="fas {{ $loan->loanable_type === 'App\Models\InclusiveRadar\AssistiveTechnology' ? 'fa-microchip' : 'fa-book' }} fa-lg"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold text-purple-dark">
                            {{ $loan->loanable->name ?? ($loan->loanable->title ?? ($loan->loanable->description ?? 'Item não identificado')) }}
                        </h5>
                        <small class="text-muted text-uppercase">Patrimônio: {{ $loan->loanable->asset_code ?? 'N/A' }}</small>
                        <div class="mt-1">
                            <span class="badge bg-info text-dark" style="font-size: 0.65rem;">BLOQUEADO PARA EDIÇÃO</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Responsáveis --}}
            <x-forms.section title="Beneficiário e Responsável" />
            <div class="col-md-6">
                <x-forms.select
                    name="student_id"
                    label="Estudante (Beneficiário)"
                    :options="$students->mapWithKeys(fn($s) => [$s->id => $s->person->name . ' (' . $s->registration . ')'])"
                    :selected="$loan->student_id"
                    :disabled="$isReturned"
                />
            </div>
            <div class="col-md-6">
                <x-forms.select
                    name="professional_id"
                    label="Profissional (Beneficiário)"
                    :options="$professionals->mapWithKeys(fn($p) => [$p->id => $p->person->name])"
                    :selected="$loan->professional_id"
                    :disabled="$isReturned"
                />
            </div>

            <div class="col-md-12">
                <x-forms.input
                    name="user_id_display"
                    label="Usuário Autenticado (Responsável)"
                    :value="$authUser->name"
                    disabled
                />
                <input type="hidden" name="user_id" value="{{ $authUser->id }}">
            </div>

            {{-- Prazos e Status --}}
            <x-forms.section title="Prazos e Observações" />
            <div class="col-md-6">
                <x-forms.input
                    name="loan_date"
                    label="Data de Saída"
                    type="datetime-local"
                    readonly
                    :value="old('loan_date', $loan->loan_date->format('Y-m-d\TH:i'))"
                />
            </div>
            <div class="col-md-6">
                <x-forms.input
                    name="due_date"
                    label="Previsão de Devolução"
                    type="date"
                    :value="old('due_date', $loan->due_date->format('Y-m-d'))"
                    :readonly="$isReturned"
                />
            </div>
            <div class="col-md-6">
                <x-forms.input
                    name="status_display"
                    label="Status Atual"
                    :value="($loan->status instanceof \App\Enums\InclusiveRadar\LoanStatus ? $loan->status : \App\Enums\InclusiveRadar\LoanStatus::tryFrom($loan->status))?->label() ?? 'Status desconhecido'"
                    disabled
                />
            </div>
            <div class="col-md-6">
                <x-forms.input
                    name="return_date_display"
                    label="Data Real da Devolução"
                    :value="$loan->return_date ? $loan->return_date->format('d/m/Y H:i') : 'Ainda não devolvido'"
                    disabled
                />
            </div>

            {{-- Observações (sempre editável) --}}
            <div class="col-md-12">
                <x-forms.textarea
                    name="observation"
                    label="Observações / Estado do Item"
                    rows="3"
                    :value="old('observation', $loan->observation)"
                    placeholder="Relate o estado do item na entrega..."
                    {{-- nunca bloqueado --}}
                    :disabled="false"
                />
            </div>

            {{-- Botões --}}
            <div class="col-12 d-flex justify-content-end gap-3 border-t pt-4 px-4 pb-4">
                @if(!$isReturned)
                    <form action="{{ route('inclusive-radar.loans.return', $loan) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <x-buttons.submit-button variant="success" onclick="return confirm('Confirmar a devolução?')">
                            Devolver
                        </x-buttons.submit-button>
                    </form>

                    <x-buttons.submit-button type="submit" class="btn-action new submit">
                        <i class="fas fa-save mr-2"></i> Salvar
                    </x-buttons.submit-button>
                @else
                    {{-- Só exibe botão de salvar observações --}}
                    <x-buttons.submit-button type="submit" class="btn-action new submit">
                        <i class="fas fa-save mr-2"></i> Salvar Observações
                    </x-buttons.submit-button>
                @endif

                <x-buttons.link-button href="{{ route('inclusive-radar.loans.index') }}" variant="secondary">
                    <i class="fas fa-times"></i> Cancelar
                </x-buttons.link-button>
            </div>
        </x-forms.form-card>
    </div>

    @push('scripts')
        @vite('resources/js/pages/inclusive-radar/loans.js')
    @endpush
@endsection
