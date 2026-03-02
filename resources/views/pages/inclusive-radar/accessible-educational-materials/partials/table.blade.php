<x-table.table
    :headers="['Nome', 'Natureza', 'Estoque', 'Status', 'Ações']"
    :records="$materials"
>
    @forelse($materials as $material)
        <tr>
            {{-- NOME --}}
            <x-table.td>{{ $material->name }}</x-table.td>

            {{-- NATUREZA --}}
            <x-table.td>
                {{ $material->is_digital ? 'Digital' : 'Físico' }}
            </x-table.td>

            {{-- ESTOQUE --}}
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

            {{-- STATUS --}}
            <x-table.td>
                @php
                    $isUnavailable = !$material->is_digital && (($material->quantity_available ?? 0) <= 0);
                    $stColor = $isUnavailable ? 'danger' : ($material->is_active ? 'success' : 'secondary');
                    $stLabel = $isUnavailable ? 'Esgotado' : ($material->is_active ? 'Ativo' : 'Inativo');
                @endphp

                {{-- Padronização TA: Texto colorido, negrito e em caixa alta --}}
                <span class="text-{{ $stColor }} fw-bold text-uppercase" style="font-size: 0.85rem;">
                    {{ $stLabel }}
                </span>
            </x-table.td>

            {{-- AÇÕES --}}
            <x-table.td>
                <x-table.actions>
                    {{-- Botão Ver --}}
                    <x-buttons.link-button
                        :href="route('inclusive-radar.accessible-educational-materials.show', $material)"
                        variant="info"
                    >
                        <i class="fas fa-eye"></i> Ver
                    </x-buttons.link-button>

                    {{-- Botão Excluir --}}
                    <form action="{{ route('inclusive-radar.accessible-educational-materials.destroy', $material) }}"
                          method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <x-buttons.submit-button
                            variant="danger"
                            onclick="return confirm('Deseja remover este material?')"
                        >
                            <i class="fas fa-trash-alt"></i> Excluir
                        </x-buttons.submit-button>
                    </form>
                </x-table.actions>
            </x-table.td>
        </tr>
    @empty
        <tr>
            <td colspan="6" class="text-center text-muted py-4">
                Nenhum material pedagógico cadastrado.
            </td>
        </tr>
    @endforelse
</x-table.table>
