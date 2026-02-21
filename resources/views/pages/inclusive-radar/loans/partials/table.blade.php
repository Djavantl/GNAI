<x-table.table :headers="['Item', 'Beneficiário', 'Prazo Entrega', 'Status', 'Usuário', 'Ações']">
    @forelse($loans as $loan)
        <tr>
            {{-- ITEM --}}
            <x-table.td>{{ $loan->loanable->name ?? ($loan->loanable->title ?? 'Item Removido') }}</x-table.td>

            {{-- BENEFICIÁRIO --}}
            <x-table.td>
                @if($loan->student)
                    {{ $loan->student->person->name }}
                    <small class="text-muted d-block">Matrícula: {{ $loan->student->registration }}</small>
                @elseif($loan->professional)
                    {{ $loan->professional->person->name }}
                @else
                    <span class="text-muted">Não informado</span>
                @endif
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
                    $currentStatus = $loan->status instanceof \App\Enums\InclusiveRadar\LoanStatus
                        ? $loan->status
                        : \App\Enums\InclusiveRadar\LoanStatus::tryFrom($loan->status);

                    $isOverdue = ($currentStatus === \App\Enums\InclusiveRadar\LoanStatus::ACTIVE && $loan->due_date->isPast());

                    $statusLabel = $isOverdue ? 'Em Atraso' : ($currentStatus?->label() ?? $loan->status);
                    $statusColor = $isOverdue ? 'danger' : $currentStatus->color();
                @endphp

                <span class="badge bg-{{ $statusColor }}-subtle text-{{ $statusColor }}-emphasis border px-2">
                    {{ $statusLabel }}
                </span>
            </x-table.td>

            {{-- USUÁRIO RESPONSÁVEL --}}
            <x-table.td>
                {{ $loan->user->name ?? '—' }}
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
                </x-table.actions>
            </x-table.td>
        </tr>
    @empty
        <tr>
            <td colspan="6" class="text-center text-muted py-4">Nenhum empréstimo registrado.</td>
        </tr>
    @endforelse
</x-table.table>
