 <x-table.table :headers="[
    ['label' => 'Data', 'responsive' => false],
    'Profissional',
    'Tipo',
    'Status',
    ['label' => 'Ações', 'responsive' => false],
]">
    @forelse($sessions as $session)
        <tr>
            <x-table.td :responsive="false">
                <div class="fw-bold">{{ \Carbon\Carbon::parse($session->session_date)->format('d/m/Y') }}</div>
                <small class="text-muted">{{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }}</small>
            </x-table.td>
            
            <x-table.td>{{ $session->professional->person->name }}</x-table.td>
            
            <x-table.td>
             
                {{ \App\Domains\SpecializedEducationalSupport\Domain\Enums\SessionType::labelFor($session->type) }}
                
            </x-table.td>

            <x-table.td>
                @php
                    $statusColor = \App\Domains\SpecializedEducationalSupport\Domain\Enums\SessionStatus::colorFor($session->status);
                @endphp
                <span class="text-{{ $statusColor }} fw-bold">
                    {{ \App\Domains\SpecializedEducationalSupport\Domain\Enums\SessionStatus::labelFor($session->status) }}
                </span>
            </x-table.td>

            <x-table.td :responsive="false">
                <x-table.actions>
                    {{-- Ver --}}
                    <x-buttons.link-button
                        :href="route('specialized-educational-support.sessions.show', $session->id)"
                        variant="info"
                    >
                        <i class="fas fa-eye" aria-hidden="true"></i>Ver
                    </x-buttons.link-button>

                    {{-- Excluir --}}
                    <x-buttons.submit-button
                        type="button"
                        variant="danger"
                        data-bs-toggle="modal"
                        data-bs-target="#globalConfirmActionModal"
                        data-confirm-title="Excluir Agendamento"
                        data-confirm-message="Mover este agendamento para a lixeira?"
                        data-confirm-action="{{ route('specialized-educational-support.sessions.destroy', $session->id) }}"
                        data-confirm-method="DELETE"
                        data-confirm-submit-text="Confirmar Exclusao"
                        data-confirm-variant="danger"
                    >
                           <i class="fas fa-trash" aria-hidden="true"></i> Excluir
                        </x-buttons.submit-button>
                </x-table.actions>
            </x-table.td>
        </tr>
    @empty
            <tr>
                <td colspan="5" class="text-center text-muted py-5">
                    Nenhum agendamento encontrado para este aluno.
                </td>
            </tr>
    @endforelse
    </x-table.table>
