<x-table.table :headers="[
    ['label' => 'Data', 'responsive' => false],
    'Aluno',
    'Profissional',
    'Tipo',
    'Status',
    ['label' => 'Ações', 'responsive' => false],
]"
:records="$sessions">
@forelse($sessions as $session)
    <tr>
        <x-table.td :responsive="false">{{ ($session->session_date)->format('d/m/Y') }}</x-table.td>
        <x-table.td>
            @forelse($session->students ?? [] as $student)
                <div>{{ $student->person->name }}</div>
            @empty
                <span class="text-muted">Sem alunos</span>
            @endforelse
        </x-table.td>
        <x-table.td>{{ $session->professional->person->name }}</x-table.td>
        
        <x-table.td>{{ $session->typeLabel() }}</x-table.td>

        <x-table.td>
            @php
                $statusColor = match(strtolower($session->status ?? '')) {
                    'agendada', 'agendado', 'scheduled', 'pending'    => 'warning',
                    'realizada', 'realizado', 'completed', 'confirmed' => 'success',
                    'cancelada', 'cancelado', 'cancelled', 'canceled'  => 'danger',
                    default => 'warning'
                };
            @endphp
            <span class="text-{{ $statusColor }} fw-bold">
                {{ $session->statusLabel() }}
            </span>
        </x-table.td>

        <x-table.td :responsive="false">
            <x-table.actions>
                {{-- Ver Agendamento --}}
                @can('session.view')
                <x-buttons.link-button
                    :href="route('specialized-educational-support.sessions.show', $session)"
                    variant="info"
                >
                   <i class="fas fa-eye" aria-hidden="true"></i>  Ver
                </x-buttons.link-button>
                @endcan
                @can('session.delete')
                {{-- Excluir --}}
                <x-buttons.submit-button
                    type="button"
                    variant="danger"
                    data-bs-toggle="modal"
                    data-bs-target="#globalConfirmActionModal"
                    data-confirm-title="Excluir Agendamento"
                    data-confirm-message="Mover este agendamento para a lixeira?"
                    data-confirm-action="{{ route('specialized-educational-support.sessions.destroy', $session) }}"
                    data-confirm-method="DELETE"
                    data-confirm-submit-text="Confirmar Exclusao"
                    data-confirm-variant="danger"
                >
                        <i class="fas fa-trash" aria-hidden="true"></i> Excluir
                    </x-buttons.submit-button>
                @endcan
            </x-table.actions>
        </x-table.td>
    </tr>
@empty
    <tr>
        <td colspan="6" class="text-center text-muted fw-bold py-5">
            <i class="fas fa-folder-open d-block mb-2" style="font-size: 2.5rem;"></i>
            Nenhum agendamento encontrado.
        </td>
    </tr>
@endforelse
</x-table.table>
