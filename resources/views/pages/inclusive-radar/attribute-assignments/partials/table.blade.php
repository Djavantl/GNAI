<x-table.table :headers="['Nome do Tipo', 'Utilizada em', 'Natureza', 'Status', 'Ações']" :records="$groupedAssignments">
    @forelse($groupedAssignments as $type)
        <tr>
            {{-- NOME DO TIPO --}}
            <x-table.td>
                <span class="fw-bold">{{ $type->name }}</span>
            </x-table.td>

            {{-- UTILIZADA EM (Finalidade) --}}
            <x-table.td>
                @php
                    $apps = [];
                    if($type->for_assistive_technology) $apps[] = 'TA';
                    if($type->for_educational_material) $apps[] = 'Material';
                @endphp
                {{ count($apps) > 0 ? implode(' / ', $apps) : '---' }}
            </x-table.td>

            {{-- NATUREZA (Digital) --}}
            <x-table.td>
                <span class="{{ $type->is_digital}}">
                    {{ $type->is_digital ? 'Digital' : 'Físico' }}
                </span>
            </x-table.td>

            {{-- STATUS (Ativo) --}}
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
                        :href="route('inclusive-radar.type-attribute-assignments.show', ['assignment' => $type->id])"
                        variant="info"
                    >
                        <i class="fas fa-eye"></i> Ver
                    </x-buttons.link-button>

                    <form action="{{ route('inclusive-radar.type-attribute-assignments.destroy', ['assignment' => $type->id]) }}"
                          method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <x-buttons.submit-button
                            variant="danger"
                            onclick="return confirm('Isso removerá TODOS os atributos vinculados ao tipo {{ $type->name }}. Continuar?')"
                        >
                            <i class="fas fa-trash-alt"></i> Excluir
                        </x-buttons.submit-button>
                    </form>
                </x-table.actions>
            </x-table.td>
        </tr>
    @empty
        <tr>
            <td colspan="5" class="text-center text-muted py-4">Nenhum vínculo encontrado.</td>
        </tr>
    @endforelse
</x-table.table>
