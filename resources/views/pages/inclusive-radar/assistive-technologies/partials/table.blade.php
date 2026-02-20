<x-table.table :headers="['Nome', 'Tipo', 'Natureza', 'Estoque', 'Status', 'Ações']" :records="$assistiveTechnologies">
    @forelse($assistiveTechnologies as $tech)
        <tr>
            {{-- NOME --}}
            <x-table.td>{{ $tech->name }}</x-table.td>

            {{-- TIPO --}}
            <x-table.td>{{ $tech->type?->name ?? 'Geral' }}</x-table.td>

            {{-- NATUREZA --}}
            <x-table.td>{{ $tech->type?->is_digital ? 'Digital' : 'Físico' }}</x-table.td>

            {{-- ESTOQUE --}}
            <x-table.td>
                @if($tech->type?->is_digital)
                    <span class="text-info fw-bold">Ilimitado</span>
                @else
                    <span class="{{ $tech->quantity_available > 0 ? 'text-success' : 'text-danger' }} fw-medium">
                        {{ $tech->quantity_available ?? 0 }}
                    </span>
                    <span class="text-muted">/ {{ $tech->quantity ?? 0 }}</span>
                @endif
            </x-table.td>

            {{-- STATUS --}}
            <x-table.td>
                @php
                    $isUnavailable = !$tech->type?->is_digital && ($tech->quantity_available <= 0);
                    $color = $isUnavailable ? 'danger' : ($tech->is_active ? 'success' : 'secondary');
                    $label = $isUnavailable ? 'Esgotado' : ($tech->is_active ? 'Ativo' : 'Inativo');
                @endphp
                <span class="text-{{ $color }} fw-bold">{{ $label }}</span>
            </x-table.td>

            {{-- AÇÕES --}}
            <x-table.td>
                <x-table.actions>
                    <x-buttons.link-button
                        :href="route('inclusive-radar.assistive-technologies.show', $tech)"
                        variant="info"
                    >
                        <i class="fas fa-eye"></i> Ver
                    </x-buttons.link-button>

                    <form action="{{ route('inclusive-radar.assistive-technologies.destroy', $tech) }}"
                          method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <x-buttons.submit-button
                            variant="danger"
                            onclick="return confirm('Deseja remover esta tecnologia?')"
                        >
                            <i class="fas fa-trash-alt"></i> Excluir
                        </x-buttons.submit-button>
                    </form>
                </x-table.actions>
            </x-table.td>
        </tr>
    @empty
        <tr>
            <td colspan="6" class="text-center text-muted py-4">Nenhuma tecnologia cadastrada.</td>
        </tr>
    @endforelse
</x-table.table>
