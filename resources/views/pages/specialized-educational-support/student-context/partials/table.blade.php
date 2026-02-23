<x-table.table :headers="['Data', 'Versão','Tipo de Avaliação', 'Status', 'Ações']">
        @forelse($contexts as $context)
            <tr class="{{ $context->is_current ? 'table-success' : '' }}">
                <x-table.td>
                    <strong>{{ $context->created_at->format('d/m/Y') }}</strong>
                </x-table.td>

                <x-table.td >
                   <strong> v{{ $context->version }}</strong>
                </x-table.td>

                <x-table.td>
                    @php
                        $evaluationTypes = [
                            'initial' => 'Avaliação Inicial',
                            'periodic_review' => 'Revisão Periódica',
                            'pei_review' => 'Revisão PEI',
                            'specific_demand' => 'Demanda Específica'
                        ];
                    @endphp
                    <span class="text-uppercase fw-bold small">{{ $evaluationTypes[$context->evaluation_type] ?? $context->evaluation_type }}</span>
                </x-table.td>

                <x-table.td >
                    @if($context->is_current)
                        <span class="text-success">ATUAL</span>
                    @else
                        <span class="text-muted">HISTÓRICO</span>
                    @endif
                </x-table.td>

                <x-table.td>
                    <x-table.actions>
                        <x-buttons.link-button
                            :href="route('specialized-educational-support.student-context.show', $context)"
                            variant="info"
                        >
                            <i class="fas fa-eye" aria-hidden="true"></i> Ver
                        </x-buttons.link-button>

                        <form action="{{ route('specialized-educational-support.student-context.destroy', $context->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <x-buttons.submit-button variant="danger" onclick="return confirm('Excluir este registro permanentemente?')">
                                <i class="fas fa-trash" aria-hidden="true"></i>Excluir
                            </x-buttons.submit-button>
                        </form>
                    </x-table.actions>
                </x-table.td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center text-muted py-5">
                    <i class="fas fa-folder-open d-block mb-2" style="font-size: 2rem;"></i>
                    Nenhum contexto registrado para este aluno.
                </td>
            </tr>
        @endforelse
    </x-table.table>