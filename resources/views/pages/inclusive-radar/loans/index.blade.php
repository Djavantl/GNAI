@extends('layouts.master')

@section('title', 'Empréstimos')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Empréstimos' => route('inclusive-radar.loans.index'),
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Empréstimos de Recursos</h2>
            <p class="text-muted">Controle de saídas e devoluções de tecnologias e materiais pedagógicos.</p>
        </div>
        <x-buttons.link-button
            :href="route('inclusive-radar.loans.create')"
            variant="new"
        >
            Novo Empréstimo
        </x-buttons.link-button>
    </div>

    {{-- Agora com 7 colunas --}}
    <x-table.table :headers="['Item', 'Beneficiário', 'Prazo Entrega', 'Status', 'Ações']">
        @forelse($loans as $loan)
            <tr>
                {{-- ITEM --}}
                <x-table.td>
                    {{ $loan->loanable->name ?? ($loan->loanable->title ?? 'Item Removido') }}
                </x-table.td>

                {{-- BENEFICIÁRIO --}}
                <x-table.td>
                    {{ $loan->student->person->name }}
                    <small class="text-muted d-block">Matrícula: {{ $loan->student->registration }}</small>
                </x-table.td>

                {{-- PRAZO ENTREGA --}}
                <x-table.td>
                    <span class="{{ $loan->status === 'active' && $loan->due_date->isPast() ? 'text-danger fw-bold' : '' }}">
                        {{ $loan->due_date->format('d/m/Y') }}
                    </span>
                </x-table.td>

                <x-table.td>
                    @php
                        $currentStatus = $loan->status instanceof \App\Enums\InclusiveRadar\LoanStatus
                            ? $loan->status
                            : \App\Enums\InclusiveRadar\LoanStatus::tryFrom($loan->status);

                        if ($currentStatus === \App\Enums\InclusiveRadar\LoanStatus::ACTIVE && $loan->due_date->isPast()) {
                            $statusLabel = 'Em Atraso';
                            $statusColor = 'danger';
                        } else {
                            $statusLabel = $currentStatus?->label() ?? $loan->status;
                            $statusColor = match($currentStatus) {
                                \App\Enums\InclusiveRadar\LoanStatus::ACTIVE   => 'success',
                                \App\Enums\InclusiveRadar\LoanStatus::RETURNED => 'secondary',
                                \App\Enums\InclusiveRadar\LoanStatus::LATE     => 'warning',
                                \App\Enums\InclusiveRadar\LoanStatus::DAMAGED  => 'dark',
                                default => 'secondary',
                            };
                        }
                    @endphp

                    <span class="text-{{ $statusColor }} fw-bold">
                        {{ $statusLabel }}
                    </span>
                </x-table.td>

                {{-- AÇÕES --}}
                <x-table.td>
                    <x-table.actions>
                        <x-buttons.link-button
                            :href="route('inclusive-radar.loans.show', $loan)"
                            variant="info"
                        >
                            <i class="fas fa-eye"></i> Ver
                        </x-buttons.link-button>

                        <x-buttons.link-button :href="route('inclusive-radar.loans.edit', $loan)" variant="warning">
                            <i class="fas fa-edit"></i> Editar
                        </x-buttons.link-button>

                        <form action="{{ route('inclusive-radar.loans.destroy', $loan) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <x-buttons.submit-button variant="danger" onclick="return confirm('Deseja excluir?')">
                                <i class="fas fa-trash-alt"></i> Excluir
                            </x-buttons.submit-button>
                        </form>
                    </x-table.actions>
                </x-table.td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center text-muted py-4">Nenhum empréstimo registrado.</td>
            </tr>
        @endforelse
    </x-table.table>
@endsection
