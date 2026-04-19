<x-table.table
    :headers="[
        ['label' => 'Data',               'responsive' => false],
        ['label' => 'Versão',             'responsive' => true],
        ['label' => 'Tipo de Avaliação',  'responsive' => true],
        ['label' => 'Status',             'responsive' => true],
        ['label' => 'Ações',              'responsive' => false],
    ]"
    :records="$contexts"
>
    @forelse($contexts as $context)
        <tr>
            <x-table.td :responsive="false">
                <strong>{{ $context->created_at->format('d/m/Y') }}</strong>
            </x-table.td>

            <x-table.td>
                <strong>V{{ $context->version }}</strong>
            </x-table.td>

            <x-table.td>
                @php
                    $evaluationTypes = [
                        'initial' => 'Avaliação inicial',
                        'periodic_review' => 'Revisão periódica',
                        'pei_review' => 'Revisão PEI',
                        'specific_demand' => 'Demanda específica'
                    ];
                @endphp

                <span class="fw-bold text-uppercase" style="font-size: 0.85rem;">
                    {{ $evaluationTypes[$context->evaluation_type] ?? $context->evaluation_type }}
                </span>
            </x-table.td>

            <x-table.td>
                @php
                    $statusColor = $context->is_current ? 'success' : 'secondary';
                    $statusLabel = $context->is_current ? 'Atual' : 'Histórico';
                @endphp

                <span class="text-{{ $statusColor }} fw-bold text-uppercase" style="font-size: 0.85rem;">
                    {{ $statusLabel }}
                </span>
            </x-table.td>

            <x-table.td :responsive="false">
                <x-table.actions>
                    @canany(['student-context.view', 'student-context.delete'])
                        @can('student-context.view')
                            <x-buttons.link-button
                                :href="route('specialized-educational-support.student-context.show', $context)"
                                variant="info"
                            >
                                <i class="fas fa-eye"></i> Ver
                            </x-buttons.link-button>
                        @endcan

                        @can('student-context.delete')
                            <form action="{{ route('specialized-educational-support.student-context.destroy', $context->id) }}"
                                  method="POST"
                                  class="d-inline">
                                @csrf
                                @method('DELETE')
                                <x-buttons.submit-button
                                    variant="danger"
                                    onclick="return confirm('Excluir este registro permanentemente?')"
                                >
                                    <i class="fas fa-trash"></i> Excluir
                                </x-buttons.submit-button>
                            </form>
                        @endcan
                    @else
                        <span class="text-purple-light">Nenhuma ação</span>
                    @endcanany
                </x-table.actions>
            </x-table.td>
        </tr>
    @empty
        <tr>
            <td colspan="5" class="text-center text-muted py-4">
                Nenhum contexto do aluno encontrado.
            </td>
        </tr>
    @endforelse
</x-table.table>
