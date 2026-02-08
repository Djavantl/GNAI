@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Editar Registro de Empréstimo</h2>
            <p class="text-muted">Atualize prazos, status ou registre a devolução do recurso.</p>
        </div>
        <div class="text-end">
            <span class="d-block text-muted small uppercase fw-bold">ID no Sistema</span>
            <span class="badge bg-purple fs-6">{{ $loan->id }}</span>
        </div>
    </div>

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

    {{-- Alerta de Atraso --}}
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

            {{-- Campos ocultos para integridade --}}
            <input type="hidden" name="loanable_id" value="{{ $loan->loanable_id }}">
            <input type="hidden" name="loanable_type" value="{{ $loan->loanable_type }}">

            {{-- SEÇÃO 1: Recurso (Bloqueado) --}}
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

            {{-- SEÇÃO 2: Responsáveis --}}
            <x-forms.section title="Responsáveis e Beneficiário" />

            <div class="col-md-6">
                <x-forms.select
                    name="student_id"
                    label="Estudante *"
                    required
                    :options="$students->mapWithKeys(fn($s) => [$s->id => $s->person->name . ' (' . $s->registration . ')'])"
                    :selected="old('student_id', $loan->student_id)"
                />
            </div>

            <div class="col-md-6">
                <x-forms.select
                    name="professional_id"
                    label="Profissional Responsável *"
                    required
                    :options="$professionals->mapWithKeys(fn($p) => [$p->id => $p->person->name])"
                    :selected="old('professional_id', $loan->professional_id)"
                />
            </div>

            {{-- SEÇÃO 3: Prazos e Status --}}
            <x-forms.section title="Prazos e Observações" />

            <div class="col-md-6">
                <x-forms.input
                    name="loan_date"
                    label="Data de Saída"
                    type="datetime-local"
                    readonly
                    class="bg-light"
                    :value="old('loan_date', $loan->loan_date->format('Y-m-d\TH:i'))"
                />
            </div>

            <div class="col-md-6">
                <x-forms.input
                    name="due_date"
                    label="Nova Previsão de Entrega"
                    type="date"
                    :value="old('due_date', $loan->due_date->format('Y-m-d'))"
                />
            </div>

            <div class="col-md-6">
                <x-forms.select
                    name="status"
                    label="Status Atual do Empréstimo"
                    :options="[
                        'active' => 'Ativo (Com o aluno)',
                        'returned' => 'Devolvido (No prazo)',
                        'late' => 'Devolvido (Com atraso)',
                        'damaged' => 'Devolvido (Com Avaria)'
                    ]"
                    :selected="old('status', $loan->status)"
                />
            </div>

            <div class="col-md-6">
                <x-forms.input
                    name="return_date"
                    label="Data Real da Devolução"
                    type="datetime-local"
                    :value="old('return_date', $loan->return_date ? $loan->return_date->format('Y-m-d\TH:i') : '')"
                />
            </div>

            <div class="col-md-12">
                <x-forms.textarea
                    name="observation"
                    label="Observações do Histórico"
                    rows="3"
                    :value="old('observation', $loan->observation)"
                    placeholder="Relate o estado do item na entrega..."
                />
            </div>

            <div class="col-12 d-flex justify-content-end gap-3 border-t pt-4 px-4 pb-4">
                <x-buttons.link-button href="{{ route('inclusive-radar.loans.index') }}" variant="secondary">
                    Cancelar
                </x-buttons.link-button>

                <x-buttons.submit-button type="submit" class="btn-action new submit px-5">
                    <i class="fas fa-handshake mr-2"></i> Atualizar Registro
                </x-buttons.submit-button>
            </div>

        </x-forms.form-card>
    </div>
@endsection
