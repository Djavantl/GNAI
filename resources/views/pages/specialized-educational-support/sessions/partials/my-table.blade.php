<x-table.table :headers="[
    ['label' => 'Data', 'responsive' => false],
    'Aluno',
    'Tipo',
    'Status',
    ['label' => 'Ações', 'responsive' => false],
]"
:records="$sessions">
@forelse($sessions as $session)
    <tr>
        <x-table.td :responsive="false">{{ $session->session_date->format('d/m/Y') }}</x-table.td>

        <x-table.td>
            @forelse($session->students ?? [] as $student)
                <div>{{ $student->person->name }}</div>
            @empty
                <span class="text-muted">Sem alunos</span>
            @endforelse
        </x-table.td>

        <x-table.td>{{ \App\Domains\SpecializedEducationalSupport\Domain\Enums\SessionType::labelFor($session->type) }}</x-table.td>

        <x-table.td>
            @php($statusColor = \App\Domains\SpecializedEducationalSupport\Domain\Enums\SessionStatus::colorFor($session->status))
            <span class="text-{{ $statusColor }} fw-bold">
                {{ \App\Domains\SpecializedEducationalSupport\Domain\Enums\SessionStatus::labelFor($session->status) }}
            </span>
        </x-table.td>

        <x-table.td :responsive="false">
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
        <td colspan="5" class="text-center text-muted fw-bold py-5">
            <i class="fas fa-folder-open d-block mb-2" style="font-size: 2.5rem;"></i>
            Nenhum agendamento encontrado.
        </td>
    </tr>
@endforelse
</x-table.table>
