<section id="pedagogical-records" class="mb-5 rounded shadow-sm">

    <x-forms.section title="Atendimentos Pedagógicos" class="m-0" />

    <div class="pb-3 ps-3 pe-3">
        <div class="table-responsive">
            <x-table.table :headers="[
                ['label' => 'Data', 'responsive' => false],
                'Profissional',
                'Duração',
                'Presença',
                'Resumo',
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
                            {{ \Illuminate\Support\Str::limit(strip_tags($record->is_present ? $record->pedagogical_record : $record->absence_reason), 100) }}
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

                                    <x-buttons.link-button
                                        :href="route('specialized-educational-support.pedagogical-records.pdf', $record)"
                                        variant="secondary"
                                        class="btn-sm"
                                        target="_blank"
                                    >
                                        <i class="fas fa-file-pdf"></i>
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

        <div class="mt-3">
            {{ $pedagogicalRecords->links() }}
        </div>
    </div>
</section>
