<x-table.table :headers="['Nome do Status', 'Aplicabilidade', 'Regra de Empréstimo', 'Status', 'Ações']" :records="$resourceStatuses">
    @forelse($resourceStatuses as $resourceStatus)
        <tr>
            {{-- NOME --}}
            <x-table.td>
                <span class="fw-bold">{{ $resourceStatus->name ?? 'N/A' }}</span>
            </x-table.td>

            {{-- APLICABILIDADE --}}
            <x-table.td>
                @php
                    $apps = [];
                    if($resourceStatus->for_assistive_technology) $apps[] = 'Tecnologia';
                    if($resourceStatus->for_educational_material) $apps[] = 'Material';
                @endphp
                {{ count($apps) > 0 ? implode(' / ', $apps) : 'N/A' }}
            </x-table.td>

            {{-- REGRA DE EMPRÉSTIMO --}}
            <x-table.td>
                @if($resourceStatus->blocks_loan)
                    <span class="text-danger fw-bold">Bloqueia Empréstimo</span>
                @else
                    <span class="text-success fw-bold">Liberado para Uso</span>
                @endif
            </x-table.td>

            {{-- ATIVO/INATIVO --}}
            <x-table.td>
                @php
                    $statusColor = $resourceStatus->is_active ? 'success' : 'secondary';
                    $statusLabel = $resourceStatus->is_active ? 'Ativo' : 'Inativo';
                @endphp
                <span class="text-{{ $statusColor }} fw-bold">
                    {{ $statusLabel }}
                </span>
            </x-table.td>

            {{-- AÇÕES --}}
            <x-table.td>
                <x-table.actions>
                    <x-buttons.link-button
                        :href="route('inclusive-radar.resource-statuses.show', $resourceStatus)"
                        variant="info"
                    >
                        <i class="fas fa-eye"></i> Ver
                    </x-buttons.link-button>
                </x-table.actions>
            </x-table.td>
        </tr>
    @empty
        <tr>
            <td colspan="5" class="text-center text-muted py-4">Nenhum status configurado.</td>
        </tr>
    @endforelse
</x-table.table>
