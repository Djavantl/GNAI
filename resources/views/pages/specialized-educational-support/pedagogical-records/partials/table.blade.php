<x-table.table :headers="[
    ['label' => 'Data', 'responsive' => false],
    ['label' => 'Curso', 'responsive' => false],
    'Estudante',
    'Profissional responsável',
    'Com responsáveis',
    ['label' => 'Ações', 'responsive' => false],
]" :records="$pedagogicalRecords">
    @forelse($pedagogicalRecords as $record)
        @php
            $session = $record->attendanceSession;
            $student = $session?->students?->first();
        @endphp
        <tr>
            <x-table.td :responsive="false">
                <span class="fw-bold text-purple-dark">{{ $session?->session_date?->format('d/m/Y') ?? '—' }}</span>
            </x-table.td>
            <x-table.td :responsive="false">
                @if($student?->currentCourse?->course)
                    {{ $student->currentCourse->course->name }}
                @else
                    <span class="text-muted">Sem curso</span>
                @endif
            </x-table.td>
            <x-table.td>
                {{ $student?->person?->name ?? 'Estudante não informado' }}
                <span class="badge bg-{{ $record->is_present ? 'success' : 'danger' }} ms-1">
                    {{ $record->is_present ? 'Presente' : 'Ausente' }}
                </span>
            </x-table.td>
            <x-table.td>{{ $session?->professional?->person?->name ?? 'Não informado' }}</x-table.td>
            <x-table.td>
                <span class="badge bg-{{ $record->guardians->isNotEmpty() ? 'success' : 'secondary' }}">
                    {{ $record->guardians->isNotEmpty() ? 'Sim' : 'Não' }}
                </span>
            </x-table.td>
            <x-table.td :responsive="false">
                <x-table.actions>
                    @can('pedagogical-record.view')
                        <x-buttons.link-button :href="route('specialized-educational-support.pedagogical-records.show', $record)" variant="info" class="btn-sm">
                            <i class="fas fa-eye"></i> Ver
                        </x-buttons.link-button>
                        <x-buttons.link-button :href="route('specialized-educational-support.pedagogical-records.pdf', $record)" variant="secondary" class="btn-sm" target="_blank">
                            <i class="fas fa-file-pdf"></i> PDF
                        </x-buttons.link-button>
                    @endcan
                </x-table.actions>
            </x-table.td>
        </tr>
    @empty
        <tr><td colspan="6" class="text-center text-muted fw-bold py-5">Nenhum atendimento pedagógico encontrado.</td></tr>
    @endforelse
</x-table.table>
