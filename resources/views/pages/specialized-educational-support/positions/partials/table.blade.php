<x-table.table
    :headers="[
        ['label' => 'Cargo',  'responsive' => false],
        ['label' => 'Status', 'responsive' => true],
        ['label' => 'Ações',  'responsive' => false],
    ]"
    :records="$positions"
>
    @forelse($positions as $item)
        <tr>
            <x-table.td :responsive="false">
                <strong>{{ $item->name }}</strong>
            </x-table.td>

            <x-table.td>
                @php
                    $statusColor = $item->is_active ? 'success' : 'danger';
                    $statusLabel = $item->is_active ? 'Ativo' : 'Inativo';
                @endphp

                <span class="text-{{ $statusColor }} fw-bold text-uppercase" style="font-size: 0.85rem;">
                    {{ $statusLabel }}
                </span>
            </x-table.td>

            <x-table.td :responsive="false">
                <x-table.actions>
                    @canany(['position.view', 'position.update', 'position.delete'])

                        @can('position.view')
                            <x-buttons.link-button
                                :href="route('specialized-educational-support.positions.show', $item)"
                                variant="info"
                            >
                                <i class="fas fa-eye"></i> Ver
                            </x-buttons.link-button>
                        @endcan

                        @can('position.update')
                            <form action="{{ route('specialized-educational-support.positions.deactivate', $item) }}"
                                  method="POST"
                                  class="d-inline">
                                @csrf
                                @method('PATCH')
                                <x-buttons.submit-button variant="secondary">
                                    <i class="fas fa-check"></i>
                                    {{ $item->is_active ? 'Desativar' : 'Ativar' }}
                                </x-buttons.submit-button>
                            </form>
                        @endcan

                        @can('position.delete')
                            <form action="{{ route('specialized-educational-support.positions.destroy', $item) }}"
                                  method="POST"
                                  class="d-inline">
                                @csrf
                                @method('DELETE')
                                <x-buttons.submit-button
                                    variant="danger"
                                    onclick="return confirm('Deseja excluir este cargo?')"
                                >
                                    <i class="fas fa-trash"></i> Excluir
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
            <td colspan="3" class="text-center text-muted py-4">
                Nenhum cargo encontrado.
            </td>
        </tr>
    @endforelse
</x-table.table>
