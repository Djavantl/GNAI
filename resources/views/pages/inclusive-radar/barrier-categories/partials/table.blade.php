<x-table.table :headers="['Nome', 'Vínculos', 'Status', 'Ações']" :records="$categories">
    @forelse($categories as $category)
        <tr>
            {{-- NOME --}}
            <x-table.td>{{ $category->name }}</x-table.td>

            {{-- VÍNCULOS --}}
            <x-table.td>
                {{ $category->barriers_count ?? $category->barriers->count() }}
            </x-table.td>

            {{-- STATUS --}}
            <x-table.td>
                @php
                    $statusColor = $category->is_active ? 'success' : 'secondary';
                    $statusLabel = $category->is_active ? 'Ativo' : 'Inativo';
                @endphp
                <span class="text-{{ $statusColor }} fw-bold">
                    {{ $statusLabel }}
                </span>
            </x-table.td>

            {{-- AÇÕES --}}
            <x-table.td>
                <x-table.actions>
                    <x-buttons.link-button
                        :href="route('inclusive-radar.barrier-categories.show', $category)"
                        variant="info"
                    >
                        <i class="fas fa-eye"></i> Ver
                    </x-buttons.link-button>

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
