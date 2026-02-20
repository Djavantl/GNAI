<x-table.table :headers="['Item', 'Beneficiário', 'Data Solicitação', 'Status', 'Usuário', 'Ações']">
    @forelse($waitlists as $waitlist)
        <tr>
            {{-- ITEM --}}
            <x-table.td>
                {{ $waitlist->waitlistable->name ?? ($waitlist->waitlistable->title ?? 'Item Removido') }}
            </x-table.td>

            {{-- BENEFICIÁRIO --}}
            <x-table.td>
                @if($waitlist->student)
                    {{ $waitlist->student->person->name }}
                    <small class="text-muted d-block">Matrícula: {{ $waitlist->student->registration }}</small>
                @elseif($waitlist->professional)
                    {{ $waitlist->professional->person->name }}
                @else
                    <span class="text-muted">Não informado</span>
                @endif
            </x-table.td>

            {{-- DATA SOLICITAÇÃO --}}
            <x-table.td>{{ $waitlist->requested_at->format('d/m/Y') }}</x-table.td>

            {{-- STATUS --}}
            <x-table.td>
                @php
                    $currentStatus = \App\Enums\InclusiveRadar\WaitlistStatus::tryFrom($waitlist->status);

                    $statusLabel = $currentStatus?->label() ?? $waitlist->status;

                    $statusColor = match($currentStatus) {
                        \App\Enums\InclusiveRadar\WaitlistStatus::WAITING   => 'primary',
                        \App\Enums\InclusiveRadar\WaitlistStatus::NOTIFIED  => 'info',
                        \App\Enums\InclusiveRadar\WaitlistStatus::FULFILLED => 'success',
                        \App\Enums\InclusiveRadar\WaitlistStatus::CANCELLED => 'secondary',
                        default => 'secondary',
                    };
                @endphp

                <span class="text-{{ $statusColor }} fw-bold">{{ $statusLabel }}</span>
            </x-table.td>

            {{-- USUÁRIO RESPONSÁVEL --}}
            <x-table.td>
                {{ $waitlist->user->name ?? '—' }}
            </x-table.td>

            {{-- AÇÕES --}}
            <x-table.td>
                <x-table.actions>
                    <x-buttons.link-button
                        :href="route('inclusive-radar.waitlists.show', $waitlist)"
                        variant="info"
                    >
                        <i class="fas fa-eye"></i> Ver
                    </x-buttons.link-button>

                    <form action="{{ route('inclusive-radar.waitlists.destroy', $waitlist) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <x-buttons.submit-button
                            variant="danger"
                            onclick="return confirm('Deseja excluir esta solicitação?')"
                        >
                            <i class="fas fa-trash-alt"></i> Excluir
                        </x-buttons.submit-button>
                    </form>
                </x-table.actions>
            </x-table.td>
        </tr>
    @empty
        <tr>
            <td colspan="6" class="text-center text-muted py-4">Nenhuma solicitação registrada.</td>
        </tr>
    @endforelse
</x-table.table>
