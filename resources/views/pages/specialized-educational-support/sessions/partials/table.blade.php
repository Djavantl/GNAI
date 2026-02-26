<x-table.table :headers="['Data', 'Aluno', 'Profissional', 'Tipo', 'Status', 'Ações']"
:records="$sessions">
@forelse($sessions as $session)
    <tr>
        <x-table.td>{{ ($session->session_date)->format('d/m/Y') }}</x-table.td>
        <x-table.td>
            @forelse($session->students ?? [] as $student)
                <div>{{ $student->person->name }}</div>
            @empty
                <span class="text-muted">Sem alunos</span>
            @endforelse
        </x-table.td>
        <x-table.td>{{ $session->professional->person->name }}</x-table.td>
        <x-table.td>{{ $session->type }}</x-table.td>
        <x-table.td>
            @php
                $statusValue = strtolower($session->status);
                $statusColor = match($statusValue) {
                    'agendada', 'agendado' => 'warning',
                    'realizada', 'realizado' => 'success',
                    'cancelada', 'cancelled', 'cancelado' => 'danger',
                    default => 'warning'
                };
            @endphp
            <span class="text-{{ $statusColor }} fw-bold">
                {{ ucfirst($session->status) }}
            </span>
        </x-table.td>

        <x-table.td>
            <x-table.actions>
                {{-- Ver Sessão --}}
                <x-buttons.link-button
                    :href="route('specialized-educational-support.sessions.show', $session)"
                    variant="info"
                >
                   <i class="fas fa-eye" aria-hidden="true"></i>  Ver
                </x-buttons.link-button>

                {{-- Excluir --}}
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
            </x-table.actions>
        </x-table.td>
    </tr>
@empty
    <tr>
        <td colspan="6" class="text-center text-muted fw-bold py-5">
            <i class="fas fa-folder-open d-block mb-2" style="font-size: 2.5rem;"></i>
            Nenhuma sessão encontrada.
        </td>
    </tr>
@endforelse
</x-table.table>