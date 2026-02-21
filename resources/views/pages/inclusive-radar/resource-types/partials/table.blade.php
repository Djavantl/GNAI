<x-table.table :headers="['Nome do Tipo', 'Natureza', 'Finalidade', 'Status', 'Ações']" :records="$resourceTypes">
    @forelse($resourceTypes as $type)
        <tr>
            {{-- NOME --}}
            <x-table.td>{{ $type->name ?? 'N/A' }}</x-table.td>

            {{-- NATUREZA --}}
            <x-table.td>
                {{ $type->is_digital ? 'Digital' : 'Físico' }}
            </x-table.td>

            {{-- FINALIDADE --}}
            <x-table.td>
                @php
                    $apps = [];
                    if($type->for_assistive_technology) $apps[] = 'Tecnologia Assistiva';
                    if($type->for_educational_material) $apps[] = 'Materiais Pedagógicos';
                @endphp
                {{ count($apps) > 0 ? implode(' / ', $apps) : 'N/A' }}
            </x-table.td>

            {{-- STATUS --}}
            <x-table.td>
                @php
                    $statusColor = $type->is_active ? 'success' : 'secondary';
                    $statusLabel = $type->is_active ? 'Ativo' : 'Inativo';
                @endphp
                <span class="text-{{ $statusColor }} fw-bold">
                    {{ $statusLabel }}
                </span>
            </x-table.td>

            {{-- AÇÕES --}}
            <x-table.td>
                <x-table.actions>
                    <x-buttons.link-button
                        :href="route('inclusive-radar.resource-types.show', $type)"
                        variant="info"
                    >
                        <i class="fas fa-eye"></i> Ver
                    </x-buttons.link-button>

                    <form action="{{ route('inclusive-radar.resource-types.destroy', $type) }}"
                          method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <x-buttons.submit-button
                            variant="danger"
                            onclick="return confirm('Tem certeza que deseja remover este tipo?')"
                        >
                            <i class="fas fa-trash-alt"></i> Excluir
                        </x-buttons.submit-button>
                    </form>
                </x-table.actions>
            </x-table.td>
        </tr>
    @empty
        <tr>
            <td colspan="5" class="text-center text-muted py-4">Nenhum tipo de recurso cadastrado.</td>
        </tr>
    @endforelse
</x-table.table>
