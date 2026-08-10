<section id="pedagogical-records" class="mb-5 rounded shadow-sm">

    <x-forms.section title="Atendimentos Pedagógicos" class="m-0" />

    <div class="pb-3 ps-3 pe-3">
        <div class="table-responsive">
            <x-table.table :headers="[
                ['label' => 'Data', 'responsive' => false],
                'Profissional',
                'Duração',
                'Presença',
                'Com responsáveis',
                ['label' => '', 'responsive' => false],
            ]">
                @forelse($pedagogicalRecords as $record)
                    @php
                        $attendanceSession = $record->attendanceSession;
                        $professional = $attendanceSession?->professional;
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
                            {{ $record->duration }}
                        </x-table.td>

                        <x-table.td>
                            @if($record->is_present)
                                <span class="badge bg-success">
                                    <i class="fas fa-check-circle me-1"></i> Presente
                                </span>
                            @else
                                <span class="badge bg-danger">
                                    <i class="fas fa-times-circle me-1"></i> Ausente
                                </span>
                            @endif
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
                                        <i class="fas fa-eye"></i>
                                    </x-buttons.link-button>
                                @endcan
                            </x-table.actions>
                        </x-table.td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted fw-bold py-5">
                            <i class="fas fa-folder-open d-block mb-2" style="font-size: 2.5rem;"></i>
                            Nenhum atendimento pedagógico encontrado.
                        </td>
                    </tr>
                @endforelse
            </x-table.table>
        </div>

        @canany(['pedagogical-record.view-all', 'pedagogical-record.view-own'])
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mt-4 pt-3 border-top">
                <small class="text-muted">
                    São exibidos até 5 registros mais recentes. Para consultar todos, clique em “Gerenciar Registros”.
                </small>
                <div class="d-flex flex-wrap gap-2">
                    @can('pedagogical-record.view')
                        <x-buttons.link-button
                            :href="route('specialized-educational-support.students.pedagogical-records.pdf', $student)"
                            variant="secondary"
                            class="btn-sm"
                            target="_blank"
                            label="Gerar histórico de atendimentos pedagógicos em PDF"
                        >
                            <i class="fas fa-file-pdf"></i> Histórico em PDF
                        </x-buttons.link-button>
                    @endcan

                    <x-buttons.link-button
                        :href="route('specialized-educational-support.students.pedagogical-records.index', $student)"
                        variant="warning"
                        class="btn-sm"
                    >
                        <i class="fas fa-folder-open"></i> Gerenciar Registros
                    </x-buttons.link-button>
                </div>
            </div>
        @endcanany
    </div>
</section>
