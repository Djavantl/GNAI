<x-table.table
    :headers="[
        ['label' => 'Item',          'responsive' => false],
        ['label' => 'Beneficiário',  'responsive' => true],
        ['label' => 'Prazo Entrega', 'responsive' => true],
        ['label' => 'Status',        'responsive' => true],
        ['label' => 'Usuário',       'responsive' => true],
        ['label' => 'Ações',         'responsive' => false],
    ]"
    :records="$loans"
>
    @forelse($loans as $loan)
        <tr>
            <x-table.td :responsive="false">{{ $loan->loanable->name ?? ($loan->loanable->title ?? 'Item Removido') }}</x-table.td>

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

            <x-table.td>
                <span class="{{ $loan->isOverdue() ? 'text-danger fw-bold' : '' }}">
                    {{ $loan->due_date->format('d/m/Y') }}
                </span>
            </x-table.td>

            <x-table.td>
                <span class="text-{{ $loan->statusColor() }} fw-bold text-uppercase" style="font-size: 0.85rem;">
                    {{ $loan->statusLabel() }}
                </span>
            </x-table.td>

            <x-table.td>{{ $loan->user->name ?? '—' }}</x-table.td>

            <x-table.td :responsive="false">
                <x-table.actions>
                    @canany(['loan.show', 'loan.destroy'])
                        @can('loan.show')
                            <x-buttons.link-button
                                :href="route('inclusive-radar.loans.show', $loan)"
                                variant="info"
                            >
                                <i class="fas fa-eye"></i> Ver
                            </x-buttons.link-button>
                        @endcan
                        @can('loan.destroy')
                            <x-buttons.submit-button
                                type="button"
                                variant="danger"
                                data-bs-toggle="modal"
                                data-bs-target="#globalConfirmActionModal"
                                data-confirm-title="Excluir Emprestimo"
                                data-confirm-message="O emprestimo #{{ $loan->id }} sera excluido permanentemente."
                                data-confirm-action="{{ route('inclusive-radar.loans.destroy', $loan) }}"
                                data-confirm-method="DELETE"
                                data-confirm-submit-text="Confirmar Exclusao"
                                data-confirm-variant="danger"
                            >
                                <i class="fas fa-trash-alt"></i> Excluir
                            </x-buttons.submit-button>
                        @endcan
                    @else
                        <span class="text-purple-light">Nenhuma ação</span>
                    @endcanany
                </x-table.actions>
            </x-table.td>
        </tr>
    @empty
        <tr>
            <td colspan="6" class="text-center text-muted py-4">Nenhum empréstimo registrado.</td>
        </tr>
    @endforelse
</x-table.table>
