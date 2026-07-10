<div class="table-responsive">
    <x-table.table :headers="[
        ['label' => 'Data', 'responsive' => false],
        'Profissional',
        'Duração',
        'Presença',
        ['label' => 'Ações', 'responsive' => false],
    ]">
        @forelse($sessionEvaluations as $evaluation)
            @php
                $sessionRecord = $evaluation->sessionRecord;
                $attendanceSession = $sessionRecord?->attendanceSession;
                $professional = $attendanceSession?->professional;
                $canManageEvaluation = auth()->user()?->professional?->id === $attendanceSession?->professional_id;
            @endphp

            <tr>
                <x-table.td :responsive="false">
                    <span class="fw-bold text-purple-dark">
                        {{ $attendanceSession?->session_date?->format('d/m/Y') ?? '—' }}
                    </span>
                </x-table.td>

                <x-table.td>
                    {{ $professional?->person?->name ?? 'Não informado' }}
                </x-table.td>

                <x-table.td>
                    {{ $sessionRecord?->duration ?? '—' }}
                </x-table.td>

                <x-table.td>
                    @if($evaluation->is_present)
                        <span class="badge bg-success">
                            <i class="fas fa-check-circle me-1"></i> Presente
                        </span>
                    @else
                        <span class="badge bg-danger">
                            <i class="fas fa-times-circle me-1"></i> Ausente
                        </span>
                    @endif
                </x-table.td>

                <x-table.td :responsive="false">
                    <x-table.actions>
                        @can('session-record.view')
                            <x-buttons.link-button
                                :href="route('specialized-educational-support.students.session-records.show', [$student, $evaluation])"
                                variant="info"
                                class="btn-sm">
                                <i class="fas fa-eye"></i> Ver
                            </x-buttons.link-button>
                        @endcan

                        @can('session-record.update')
                        @if($canManageEvaluation)
                            <x-buttons.link-button
                                :href="route('specialized-educational-support.students.session-records.edit', [$student, $evaluation])"
                                variant="warning"
                                class="btn-sm">
                                <i class="fas fa-edit"></i> Editar
                            </x-buttons.link-button>
                        @endif
                        @endcan

                        @can('session-record.delete')
                        @if($canManageEvaluation)
                            <x-buttons.submit-button
                                type="button"
                                variant="danger"
                                class="btn-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#globalConfirmActionModal"
                                data-confirm-title="Excluir Registro do Aluno"
                                data-confirm-message="Excluir apenas o registro deste aluno neste atendimento AEE?"
                                data-confirm-action="{{ route('specialized-educational-support.students.session-records.destroy', [$student, $evaluation]) }}"
                                data-confirm-method="DELETE"
                                data-confirm-submit-text="Confirmar Exclusao"
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
                <td colspan="5" class="text-center text-muted fw-bold py-5">
                    <i class="fas fa-folder-open d-block mb-2" style="font-size: 2.5rem;"></i>
                    Nenhum registro encontrado.
                </td>
            </tr>
        @endforelse
    </x-table.table>
</div>

<div class="mt-3 px-3 pb-3">
    {{ $sessionEvaluations->links() }}
</div>
