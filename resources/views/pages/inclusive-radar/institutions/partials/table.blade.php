<x-table.table
    :headers="['Nome', 'Localização', 'Status', 'Ações']"
    :records="$institutions"
>
    @forelse($institutions as $inst)
        <tr>
            {{-- NOME --}}
            <x-table.td>
                {{ $inst->name }}
            </x-table.td>

            {{-- LOCALIZAÇÃO --}}
            <x-table.td>
                {{ $inst->city }} - {{ $inst->state }}
            </x-table.td>

            {{-- STATUS --}}
            <x-table.td>
                @php
                    $statusColor = $inst->is_active ? 'success' : 'danger';
                    $statusLabel = $inst->is_active ? 'Ativo' : 'Inativo';
                @endphp

                <span class="text-{{ $statusColor }} fw-bold">
                    {{ $statusLabel }}
                </span>
            </x-table.td>

            {{-- AÇÕES --}}
            <x-table.td>
                <x-table.actions>
                    <x-buttons.link-button
                        :href="route('inclusive-radar.institutions.show', $inst)"
                        variant="info"
                    >
                        <i class="fas fa-eye"></i> Ver
                    </x-buttons.link-button>

                    <form action="{{ route('inclusive-radar.institutions.destroy', $inst) }}"
                          method="POST"
                          class="d-inline">
                        @csrf
                        @method('DELETE')

                        <x-buttons.submit-button
                            variant="danger"
                            onclick="return confirm('Tem certeza que deseja excluir esta instituição?')"
                        >
                            <i class="fas fa-trash-alt"></i> Excluir
                        </x-buttons.submit-button>
                    </form>
                </x-table.actions>
            </x-table.td>
        </tr>
    @empty
        <tr>
            <td colspan="4" class="text-center text-muted py-4">
                Nenhuma instituição cadastrada.
            </td>
        </tr>
    @endforelse
</x-table.table>
