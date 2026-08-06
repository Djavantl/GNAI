<x-table.table :headers="[
    ['label' => 'Data', 'responsive' => false],
    ['label' => 'Horário', 'responsive' => false],
    'Estudantes',
    'Profissional responsável',
    ['label' => 'Ações', 'responsive' => false],
]" :records="$aeeRecords">
    @forelse($aeeRecords as $record)
        @php($session = $record->attendanceSession)
        <tr>
            <x-table.td :responsive="false">
                <span class="fw-bold text-purple-dark">{{ $session?->session_date?->format('d/m/Y') ?? '—' }}</span>
            </x-table.td>
            <x-table.td :responsive="false">
                {{ $session?->start_time ? substr($session->start_time, 0, 5) : '—' }}
                @if($session?->end_time) – {{ substr($session->end_time, 0, 5) }} @endif
            </x-table.td>
            <x-table.td>
                @forelse($record->studentEvaluations as $evaluation)
                    <div class="mb-1">
                        {{ $evaluation->student?->person?->name ?? 'Estudante não informado' }}
                        <span class="badge bg-{{ $evaluation->is_present ? 'success' : 'danger' }} ms-1">
                            {{ $evaluation->is_present ? 'Presente' : 'Ausente' }}
                        </span>
                    </div>
                @empty
                    <span class="text-muted small">Nenhum estudante avaliado</span>
                @endforelse
            </x-table.td>
            <x-table.td>{{ $session?->professional?->person?->name ?? 'Não informado' }}</x-table.td>
            <x-table.td :responsive="false">
                <x-table.actions>
                    @can('aee-record.view')
                        <x-buttons.link-button :href="route('specialized-educational-support.aee-records.show', $record)" variant="info" class="btn-sm">
                            <i class="fas fa-eye"></i> Ver
                        </x-buttons.link-button>
                        <x-buttons.link-button
                            :href="route('specialized-educational-support.aee-records.pdf', $record)"
                            variant="secondary"
                            class="btn-sm"
                            target="_blank"
                        >
                            <i class="fas fa-file-pdf"></i> PDF
                        </x-buttons.link-button>
                    @endcan
                </x-table.actions>
            </x-table.td>
        </tr>
    @empty
        <tr><td colspan="5" class="text-center text-muted fw-bold py-5">Nenhum atendimento especializado encontrado.</td></tr>
    @endforelse
</x-table.table>
