<x-table.table :headers="['Data', 'Aluno', 'Tipo', 'Status', 'Ações']"
:records="$sessions">
@forelse($sessions as $session)
    <tr>
        <x-table.td>{{ $session->session_date->format('d/m/Y') }}</x-table.td>

        <x-table.td>
            @forelse($session->students ?? [] as $student)
                <div>{{ $student->person->name }}</div>
            @empty
                <span class="text-muted">Sem alunos</span>
            @endforelse
        </x-table.td>

        <x-table.td>
            @php
                $typeValue = strtolower($session->type);

                $typeLabel = match ($typeValue) {
                    'individual' => 'Individual',
                    'group' => 'Grupo',
                    default => ucfirst($session->type)
                };
            @endphp

            {{ $typeLabel }}
        </x-table.td>

        <x-table.td>
            @php
                $statusValue = strtolower($session->status);
                $statusColor = match($statusValue) {
                    'agendada', 'agendado', 'scheduled' => 'warning',
                    'realizada', 'realizado', 'completed' => 'success',
                    'cancelada', 'cancelled', 'cancelado', 'canceled' => 'danger',
                    default => 'warning'
                };
            @endphp

            <span class="text-{{ $statusColor }} fw-bold">
                {{ ucfirst($session->status) }}
            </span>
        </x-table.td>

        <x-table.td>
            <x-table.actions>
                @can('session.view')
                <x-buttons.link-button
                    :href="route('specialized-educational-support.sessions.show', $session)"
                    variant="info"
                >
                    <i class="fas fa-eye" aria-hidden="true"></i> Ver
                </x-buttons.link-button>
                @endcan
                @can('session.delete')
                <form action="{{ route('specialized-educational-support.sessions.destroy', $session) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <x-buttons.submit-button
                        variant="danger"
                        onclick="return confirm('Mover para lixeira?')"
                    >
                        <i class="fas fa-trash" aria-hidden="true"></i> Excluir
                    </x-buttons.submit-button>
                </form>
                @endcan
            </x-table.actions>
        </x-table.td>
    </tr>
@empty
    <tr>
        <td colspan="5" class="text-center text-muted fw-bold py-5">
            <i class="fas fa-folder-open d-block mb-2" style="font-size: 2.5rem;"></i>
            Nenhuma sessão encontrada.
        </td>
    </tr>
@endforelse
</x-table.table>