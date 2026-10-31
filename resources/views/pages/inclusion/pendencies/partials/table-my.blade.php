<x-table.table :headers="[
    ['label' => 'Título', 'responsive' => false],
    'Prioridade',
    'Vencimento',
    'Status',
    ['label' => 'Ações', 'responsive' => false],
]"
:records="$pendencies">
    @forelse($pendencies as $pendency)
        <tr>
            <x-table.td :responsive="false">
                <strong>{{ $pendency->title }}</strong>
            </x-table.td>

            <x-table.td>
                <span class="text-{{ $pendency->priority->color() }} fw-bold">
                    {{ $pendency->priority->label() }}
                </span>
            </x-table.td>

            <x-table.td>
                {{ $pendency->due_date
                    ? \Carbon\Carbon::parse($pendency->due_date)->format('d/m/Y')
                    : '—'
                }}
            </x-table.td>

            <x-table.td>
                @if($pendency->is_completed)
                    <span class="text-success fw-bold">
                        <i class="fas fa-check-circle me-1"></i> Concluída
                    </span>
                @else
                    <span class="text-danger fw-bold">
                        <i class="fas fa-clock me-1"></i> Pendente
                    </span>
                @endif
            </x-table.td>

            <x-table.td :responsive="false">
                <x-table.actions>
                    @can('pendency.view')
                    <x-buttons.link-button
                        :href="route('specialized-educational-support.pendencies.show', $pendency)"
                        variant="info"
                    >
                        <i class="fas fa-eye"></i>Ver
                    </x-buttons.link-button>
                    @endcan
                </x-table.actions>
            </x-table.td>
        </tr>
    @empty
        <tr>
            <td colspan="5" class="text-center text-muted py-5">
                <i class="fas fa-folder-open d-block mb-2" style="font-size: 2.5rem;"></i>
                Nenhuma pendência encontrada para você.
            </td>
        </tr>
    @endforelse
</x-table.table>
