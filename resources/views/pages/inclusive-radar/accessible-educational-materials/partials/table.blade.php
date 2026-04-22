<x-table.table
    :headers="[
        ['label' => 'Nome',      'responsive' => false],
        ['label' => 'Natureza',  'responsive' => true],
        ['label' => 'Estoque',   'responsive' => true],
        ['label' => 'Status',    'responsive' => true],
        ['label' => 'Ações',     'responsive' => false],
    ]"
    :records="$materials"
>
    @forelse($materials as $material)
        <tr>
            <x-table.td :responsive="false">{{ $material->name }}</x-table.td>

            <x-table.td>{{ $material->is_digital ? 'Digital' : 'Físico' }}</x-table.td>

            <x-table.td>
                @if($material->is_digital)
                    <span class="text-info fw-bold text-uppercase" style="font-size: 0.85rem;">Ilimitado</span>
                @else
                    <span class="{{ ($material->quantity_available ?? 0) > 0 ? 'text-success' : 'text-danger' }} fw-bold">
                        {{ $material->quantity_available ?? 0 }}
                    </span>
                    <span class="text-muted">/ {{ $material->quantity ?? 0 }}</span>
                @endif
            </x-table.td>

            <x-table.td>
                @php
                    $isUnavailable = !$material->is_digital && (($material->quantity_available ?? 0) <= 0);
                    $stColor = $isUnavailable ? 'danger' : ($material->is_active ? 'success' : 'secondary');
                    $stLabel = $isUnavailable ? 'Esgotado' : ($material->is_active ? 'Ativo' : 'Inativo');
                @endphp
                <span class="text-{{ $stColor }} fw-bold text-uppercase" style="font-size: 0.85rem;">
                    {{ $stLabel }}
                </span>
            </x-table.td>

            <x-table.td :responsive="false">
                <x-table.actions>
                    @canany(['material.show', 'material.destroy'])
                        @can('material.show')
                            <x-buttons.link-button
                                :href="route('inclusive-radar.accessible-educational-materials.show', $material)"
                                variant="info"
                            >
                                <i class="fas fa-eye"></i> Ver
                            </x-buttons.link-button>
                        @endcan

                        @can('material.destroy')
                            <x-buttons.submit-button
                                type="button"
                                variant="danger"
                                data-bs-toggle="modal"
                                data-bs-target="#globalConfirmActionModal"
                                data-confirm-title="Excluir Material Pedagogico"
                                data-confirm-message="O material {{ $material->name }} sera excluido permanentemente."
                                data-confirm-action="{{ route('inclusive-radar.accessible-educational-materials.destroy', $material) }}"
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
            <td colspan="5" class="text-center text-muted py-4">
                Nenhum material pedagógico cadastrado.
            </td>
        </tr>
    @endforelse
</x-table.table>
