<x-table.table
    :headers="[
        ['label' => 'Nome',     'responsive' => false],
        ['label' => 'Vínculos', 'responsive' => true],
        ['label' => 'Status',   'responsive' => true],
        ['label' => 'Ações',    'responsive' => false],
    ]"
    :records="$categories"
>
    @forelse($categories as $category)
        <tr>
            <x-table.td :responsive="false">{{ $category->name }}</x-table.td>

            <x-table.td>{{ $category->barriers_count ?? $category->barriers->count() }}</x-table.td>

            <x-table.td>
                @php
                    $statusColor = $category->is_active ? 'success' : 'secondary';
                    $statusLabel = $category->is_active ? 'Ativo' : 'Inativo';
                @endphp
                <span class="text-{{ $statusColor }} fw-bold text-uppercase" style="font-size: 0.85rem;">
                    {{ $statusLabel }}
                </span>
            </x-table.td>

            <x-table.td :responsive="false">
                <x-table.actions>
                    @canany(['barrier-category.show', 'barrier-category.destroy'])
                        @can('barrier-category.show')
                            <x-buttons.link-button
                                :href="route('inclusive-radar.barrier-categories.show', $category)"
                                variant="info"
                            >
                                <i class="fas fa-eye"></i> Ver
                            </x-buttons.link-button>
                        @endcan
                        @can('barrier-category.destroy')
                            <form action="{{ route('inclusive-radar.barrier-categories.destroy', $category) }}"
                                  method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <x-buttons.submit-button
                                    variant="danger"
                                    onclick="return confirm('Tem certeza que deseja excluir?')"
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
            <td colspan="4" class="text-center text-muted py-4">Nenhuma categoria encontrada.</td>
        </tr>
    @endforelse
</x-table.table>

<div class="mt-4">
    {{ $categories->links() }}
</div>
