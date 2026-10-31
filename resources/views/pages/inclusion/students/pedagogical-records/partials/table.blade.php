<x-table.table :headers="[
        ['label' => 'Data', 'responsive' => false],
        ['label' => 'Horário', 'responsive' => false],
        'Profissional',
        'Duração',
        'Presença',
        'Com responsáveis',
        ['label' => 'Ações', 'responsive' => false],
    ]" :records="$pedagogicalRecords">
        @forelse($pedagogicalRecords as $record)
            @php
                $session = $record->attendanceSession;
                $canManageRecord = (int) auth()->user()?->professional_id === (int) $session?->professional_id;
            @endphp

            <tr>
                <x-table.td :responsive="false">
                    <span class="fw-bold text-purple-dark">
                        {{ $session?->session_date?->format('d/m/Y') ?? '—' }}
                    </span>
                </x-table.td>

                <x-table.td :responsive="false">
                    {{ $session?->start_time ? substr($session->start_time, 0, 5) : '—' }}
                    @if($session?->end_time) – {{ substr($session->end_time, 0, 5) }} @endif
                </x-table.td>

                <x-table.td>{{ $session?->professional?->person?->name ?? 'Não informado' }}</x-table.td>
                <x-table.td>{{ $record->duration ?? '—' }}</x-table.td>

                <x-table.td>
                    <span class="badge bg-{{ $record->is_present ? 'success' : 'danger' }}">
                        <i class="fas fa-{{ $record->is_present ? 'check' : 'times' }}-circle me-1"></i>
                        {{ $record->is_present ? 'Presente' : 'Ausente' }}
                    </span>
                </x-table.td>

                <x-table.td>
                    <span class="badge bg-{{ $record->guardians->isNotEmpty() ? 'success' : 'secondary' }}">
                        {{ $record->guardians->isNotEmpty() ? 'Sim' : 'Não' }}
                    </span>
                </x-table.td>

                <x-table.td :responsive="false">
                    <x-table.actions>
                        @can('pedagogical-record.view')
                            <x-buttons.link-button
                                :href="route('specialized-educational-support.pedagogical-records.show', $record)"
                                variant="info"
                                class="btn-sm"
                            >
                                <i class="fas fa-eye"></i> Ver
                            </x-buttons.link-button>

                            <x-buttons.link-button
                                :href="route('specialized-educational-support.pedagogical-records.pdf', $record)"
                                variant="secondary"
                                class="btn-sm"
                                target="_blank"
                            >
                                <i class="fas fa-file-pdf"></i> PDF
                            </x-buttons.link-button>
                        @endcan

                        @can('pedagogical-record.update')
                            @if($canManageRecord)
                                <x-buttons.link-button
                                    :href="route('specialized-educational-support.pedagogical-records.edit', $record)"
                                    variant="warning"
                                    class="btn-sm"
                                >
                                    <i class="fas fa-edit"></i> Editar
                                </x-buttons.link-button>
                            @endif
                        @endcan

                        @can('pedagogical-record.delete')
                            @if($canManageRecord)
                                <x-buttons.submit-button
                                    type="button"
                                    variant="danger"
                                    class="btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#globalConfirmActionModal"
                                    data-confirm-title="Excluir Atendimento Pedagógico"
                                    data-confirm-message="Deseja excluir este atendimento pedagógico?"
                                    data-confirm-action="{{ route('specialized-educational-support.pedagogical-records.destroy', $record) }}"
                                    data-confirm-method="DELETE"
                                    data-confirm-submit-text="Confirmar Exclusão"
                                    data-confirm-variant="danger"
                                    style="display:inline-block;"
                                >
                                    <i class="fas fa-trash-alt"></i> Excluir
                                </x-buttons.submit-button>
                            @endif
                        @endcan
                    </x-table.actions>
                </x-table.td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center text-muted fw-bold py-5">
                    <i class="fas fa-folder-open d-block mb-2" style="font-size: 2.5rem;"></i>
                    Nenhum atendimento pedagógico encontrado.
                </td>
            </tr>
        @endforelse
</x-table.table>
