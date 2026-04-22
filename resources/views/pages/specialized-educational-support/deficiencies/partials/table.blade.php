<x-table.table
    :headers="[
        ['label' => 'Nome / CID', 'responsive' => false],
        ['label' => 'Status',            'responsive' => true],
        ['label' => 'Ações',             'responsive' => false],
    ]"
    :records="$deficiencies"
>
    @forelse($deficiencies as $item)
        <tr>
            <x-table.td :responsive="false">
                <strong>{{ $item->name }}</strong><br>
                <small class="text-muted">{{ $item->cid_code ?? 'S/ CID' }}</small>
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
                    @canany(['deficiency.view', 'deficiency.update', 'deficiency.delete'])

                        @can('deficiency.view')
                            <x-buttons.link-button
                                :href="route('specialized-educational-support.deficiencies.show', $item)"
                                variant="info"
                            >
                                <i class="fas fa-eye"></i> Ver
                            </x-buttons.link-button>
                        @endcan

                        @can('deficiency.update')
                            <form action="{{ route('specialized-educational-support.deficiencies.deactivate', $item) }}"
                                  method="POST"
                                  class="d-inline">
                                @csrf
                                @method('PATCH')
                                <x-buttons.submit-button variant="secondary">
                                    <i class="fas fa-check"></i> Ativar/Desativar
                                </x-buttons.submit-button>
                            </form>
                        @endcan

                        @can('deficiency.delete')
                            <form action="{{ route('specialized-educational-support.deficiencies.destroy', $item) }}"
                                  method="POST"
                                  class="d-inline">
                                @csrf
                                @method('DELETE')
                                <x-buttons.submit-button
                                    variant="danger"
                                    onclick="return confirm('Deseja excluir este registro?')"
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
                Nenhum Perfil encontrado.
            </td>
        </tr>
    @endforelse
</x-table.table>
