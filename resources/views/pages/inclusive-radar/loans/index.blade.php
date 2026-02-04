@extends('layouts.master')

@section('title', 'Empréstimos de Recursos')

@section('content')
    <x-messages.toast />

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
    <x-table.table :headers="['Item', 'Tipo', 'Beneficiário', 'Data Saída', 'Prazo Entrega', 'Status', 'Ações']">
        @forelse($loans as $loan)
            <tr>
                {{-- ITEM --}}
                <x-table.td>
                    {{ $loan->loanable->name ?? ($loan->loanable->title ?? 'Item Removido') }}
                </x-table.td>

                {{-- TIPO: Coluna separada conforme solicitado --}}
                <x-table.td>
                    {{ $loan->loanable_type === 'App\Models\InclusiveRadar\AssistiveTechnology' ? 'Tecnologia' : 'Material' }}
                </x-table.td>

                {{-- BENEFICIÁRIO --}}
                <x-table.td>
                    {{ $loan->student->person->name }}
                    <small class="text-muted d-block">Matrícula: {{ $loan->student->registration }}</small>
                </x-table.td>

                {{-- DATA SAÍDA --}}
                <x-table.td>
                    {{ $loan->loan_date->format('d/m/Y H:i') }}
                </x-table.td>

                {{-- PRAZO ENTREGA --}}
                <x-table.td>
                    <span class="{{ $loan->status === 'active' && $loan->due_date->isPast() ? 'text-danger fw-bold' : '' }}">
                        {{ $loan->due_date->format('d/m/Y') }}
                    </span>
                </x-table.td>

                {{-- STATUS --}}
                <x-table.td>
                    @php
                        $statusMap = [
                            'active'   => ['label' => 'Ativo', 'color' => 'success'],
                            'returned' => ['label' => 'Devolvido', 'color' => 'secondary'],
                            'late'     => ['label' => 'Devolvido (Atraso)', 'color' => 'warning'],
                            'damaged'  => ['label' => 'Com Avaria', 'color' => 'dark'],
                        ];

                        $currentStatus = $statusMap[$loan->status] ?? ['label' => $loan->status, 'color' => 'secondary'];

                        if ($loan->status === 'active' && $loan->due_date->isPast()) {
                            $currentStatus = ['label' => 'Em Atraso', 'color' => 'danger'];
                        }
                    @endphp

                    <span class="text-{{ $currentStatus['color'] }} fw-bold">
                        {{ $currentStatus['label'] }}
                    </span>
                </x-table.td>

                {{-- AÇÕES --}}
                <x-table.td>
                    <x-table.actions>
                        @if($loan->status === 'active')
                            <form action="{{ route('inclusive-radar.loans.return', $loan) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <x-buttons.submit-button variant="success" onclick="return confirm('Confirmar a devolução?')">
                                    Devolver
                                </x-buttons.submit-button>
                            </form>
                        @endif

                        <x-buttons.link-button :href="route('inclusive-radar.loans.edit', $loan)" variant="warning">
                            Editar
                        </x-buttons.link-button>

                        <form action="{{ route('inclusive-radar.loans.destroy', $loan) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <x-buttons.submit-button variant="danger" onclick="return confirm('Deseja excluir?')">
                                Excluir
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
