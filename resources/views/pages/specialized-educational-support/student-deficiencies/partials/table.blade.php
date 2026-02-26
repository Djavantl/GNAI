<x-table.table :headers="['Deficiência', 'Severidade', 'Recursos de Apoio', 'Ações']">
        @forelse($deficiencies as $deficiency)
            <tr>
                <x-table.td>
                    <strong>{{ $deficiency->deficiency->name }}</strong>
                </x-table.td>

                <x-table.td>
                    @php
                        $severityLabels = ['mild' => 'Leve', 'moderate' => 'Moderada', 'severe' => 'Severa'];
                        $severityColors = ['mild' => 'success', 'moderate' => 'warning', 'severe' => 'danger'];
                    @endphp
                    @if($deficiency->severity)
                        <span class="text-{{ $severityColors[$deficiency->severity] }} text-uppercase fw-bold">
                            {{ $severityLabels[$deficiency->severity] }}
                        </span>
                    @else
                        <span class="text-muted small">Não informada</span>
                    @endif
                </x-table.td>

                <x-table.td>
                    @if($deficiency->uses_support_resources)
                        <span class="text-success fw-bold">SIM</span>
                    @else
                        <span class="text-dark fw-bold">NÃO</span>
                    @endif
                </x-table.td>

                <x-table.td>
                    <x-table.actions>
                        <x-buttons.link-button
                            :href="route('specialized-educational-support.student-deficiencies.show', [$student, $deficiency])"
                            variant="info"
                        >
                            <i class="fas fa-eye" aria-hidden="true"></i> Ver
                        </x-buttons.link-button>

                        <form action="{{ route('specialized-educational-support.student-deficiencies.destroy', [$student, $deficiency]) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <x-buttons.submit-button
                                variant="danger"
                                onclick="return confirm('Deseja remover esta deficiência do registro do aluno?')"
                            >
                                <i class="fas fa-trash" aria-hidden="true"></i> Excluir
                            </x-buttons.submit-button>
                        </form>
                    </x-table.actions>
                </x-table.td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center text-muted fw-bold py-5">
                    <i class="fas fa-folder-open d-block mb-2" style="font-size: 2.5rem;"></i>
                    Nenhuma deficiência do aluno encontrada.
                </td>
            </tr>
        @endforelse
    </x-table.table>