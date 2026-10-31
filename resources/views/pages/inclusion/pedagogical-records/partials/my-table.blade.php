<x-table.table :headers="[
    ['label' => 'Data', 'responsive' => false],
    'Aluno',
    'Duração',
    'Presença',
    ['label' => 'Ações', 'responsive' => false],
]" :records="$pedagogicalRecords">
    @forelse($pedagogicalRecords as $record)
        @php
            $session = $record->attendanceSession;
            $student = $session?->students?->first();
        @endphp

        <tr>
            <x-table.td :responsive="false">
                <span class="fw-bold text-purple-dark">
                    {{ $session?->session_date?->format('d/m/Y') ?? '—' }}
                </span>
            </x-table.td>

            <x-table.td>
                {{ $student?->person?->name ?? 'Aluno não informado' }}
            </x-table.td>

            <x-table.td>
                {{ $record->duration ?? '—' }}
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

            <x-table.td :responsive="false">
                <x-table.actions>
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
