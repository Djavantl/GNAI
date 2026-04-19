<x-table.table
    :headers="[
        ['label' => 'Item',              'responsive' => false],
        ['label' => 'Beneficiário',      'responsive' => true],
        ['label' => 'Data Solicitação',  'responsive' => true],
        ['label' => 'Status',            'responsive' => true],
        ['label' => 'Usuário',           'responsive' => true],
        ['label' => 'Ações',             'responsive' => false],
    ]"
    :records="$waitlists"
>
    @forelse($waitlists as $waitlist)
        <tr>
            <x-table.td :responsive="false">
                @php
                    $resourceRoute = match($waitlist->waitlistable_type) {
                        'assistive_technology'            => route('inclusive-radar.assistive-technologies.show', $waitlist->waitlistable_id),
                        'accessible_educational_material' => route('inclusive-radar.accessible-educational-materials.show', $waitlist->waitlistable_id),
                        default                           => '#',
                    };
                @endphp

                <a href="{{ $resourceRoute }}" class="text-purple-dark text-decoration-none" target="_blank">
                    {{ $waitlist->waitlistable->name ?? ($waitlist->waitlistable->title ?? 'Item Removido') }}
                </a>
            </x-table.td>

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

            <x-table.td>{{ $waitlist->requested_at->format('d/m/Y') }}</x-table.td>

            <x-table.td>
                @php
                    $currentStatus = \App\Enums\InclusiveRadar\WaitlistStatus::tryFrom($waitlist->status);
                    $statusColor = $currentStatus?->color() ?? 'secondary';
                @endphp

                <span class="text-{{ $statusColor }} fw-bold text-uppercase" style="font-size: 0.85rem;">
                    {{ $currentStatus?->label() ?? $waitlist->status }}
                </span>
            </x-table.td>

            <x-table.td>
                {{ $waitlist->user->name ?? '—' }}
            </x-table.td>

            <x-table.td :responsive="false">
                <x-table.actions>
                    @canany(['waitlist.show', 'waitlist.destroy'])
                        @can('waitlist.show')
                            <x-buttons.link-button
                                :href="route('inclusive-radar.waitlists.show', $waitlist)"
                                variant="info"
                            >
                                <i class="fas fa-eye"></i> Ver
                            </x-buttons.link-button>
                        @endcan

                        @can('waitlist.destroy')
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
                        @endcan
                    @else
                        <span class="text-purple-light">Nenhuma ação</span>
                    @endcanany
                </x-table.actions>
            </x-table.td>
        </tr>
    @empty
        <tr>
            <td colspan="6" class="text-center text-muted py-4">Nenhuma solicitação registrada.</td>
        </tr>
    @endforelse
</x-table.table>
