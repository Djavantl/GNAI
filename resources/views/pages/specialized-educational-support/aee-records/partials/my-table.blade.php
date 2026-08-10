<x-table.table :headers="[
    ['label' => 'Data', 'responsive' => false],
    'Alunos',
    'Duração',
    ['label' => 'Ações', 'responsive' => false],
]" :records="$aeeRecords">
    @forelse($aeeRecords as $record)
        <tr>
            <x-table.td :responsive="false">
                <span class="fw-bold text-purple-dark">
                    {{ $record->attendanceSession->session_date->format('d/m/Y') }}
                </span>
            </x-table.td>

            <x-table.td>
                @if($record->studentEvaluations->isNotEmpty())
                    @foreach($record->studentEvaluations as $evaluation)
                        <div class="mb-1">
                            {{ $evaluation->student->person->name }}
                            @if(!$evaluation->is_present)
                                <span class="badge bg-danger ms-1" style="font-size:0.6rem;">FALTA</span>
                            @endif
                        </div>
                    @endforeach
                @else
                    <span class="text-muted small">Nenhum aluno avaliado</span>
                @endif
            </x-table.td>

            <x-table.td>
                {{ $record->duration ?? '—' }}
            </x-table.td>

            <x-table.td :responsive="false">
                <x-table.actions>
                    <x-buttons.link-button
                        :href="route('specialized-educational-support.aee-records.show', $record)"
                        variant="info"
                        class="btn-sm"
                    >
                        <i class="fas fa-eye"></i> Ver
                    </x-buttons.link-button>
                </x-table.actions>
            </x-table.td>
        </tr>
    @empty
        <tr>
            <td colspan="4" class="text-center text-muted fw-bold py-5">
                <i class="fas fa-folder-open d-block mb-2" style="font-size: 2.5rem;"></i>
                Nenhum registro de atendimento encontrado.
            </td>
        </tr>
    @endforelse
</x-table.table>
