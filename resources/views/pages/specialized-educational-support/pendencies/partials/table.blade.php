<x-table.table :headers="['Título','Profissional','Prioridade','Vencimento','Concluída','Ações']"
:records="$pendencies">
@foreach($pendencies as $pendency)
    <tr>
        <x-table.td>{{ $pendency->title }}</x-table.td>

        <x-table.td>
            {{ $pendency->assignedProfessional?->person?->name ?? '—' }}
        </x-table.td>

        <x-table.td>
            <span class="text-{{ $pendency->priority->color() }} fw-bold">
                {{ $pendency->priority->label() }}
            </span>
        </x-table.td>

        <x-table.td>
            {{ $pendency->due_date?->format('d/m/Y') ?? '—' }}
        </x-table.td>

        <x-table.td>
            @if($pendency->is_completed)
                <span class="text-success fw-bold"><i class="fas fa-check-circle me-1"></i>Sim</span>
            @else
                <span class="text-danger fw-bold"><i class="fas fa-times-circle me-1"></i>Não</span>
            @endif
        </x-table.td>

        <x-table.td>
            <x-table.actions>
                <x-buttons.link-button :href="route('specialized-educational-support.pendencies.show', $pendency)" variant="info">
                    <i class="fas fa-eye"></i> ver
                </x-buttons.link-button>

                <form action="{{ route('specialized-educational-support.pendencies.destroy', $pendency) }}" method="POST" onsubmit="return confirm('Deseja excluir esta pendência?')">
                    @csrf
                    @method('DELETE')
                    <x-buttons.submit-button variant="danger">
                        <i class="fas fa-trash"></i> Excluir
                    </x-buttons.submit-button>
                </form>
            </x-table.actions>
        </x-table.td>
    </tr>
@endforeach
</x-table.table>

