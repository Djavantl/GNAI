<x-table.table
    :headers="[
        ['label' => 'Ano / Período',        'responsive' => false],
        ['label' => 'Rótulo Identificador', 'responsive' => true],
        ['label' => 'Status',               'responsive' => true],
        ['label' => 'Ações',                'responsive' => false],
    ]"
    :records="$semesters"
>
    @forelse($semesters as $semester)
        <tr class="{{ $semester->is_current ? 'table-primary' : '' }}">
            <x-table.td :responsive="false">
                <div class="fw-bold">{{ $semester->year }}</div>
                <small class="text-muted text-uppercase">
                    {{ $semester->term }}º Período
                </small>
            </x-table.td>

            <x-table.td>
                <span class="text-uppercase fw-bold">
                    {{ $semester->label }}
                </span>
            </x-table.td>

            <x-table.td>
                @php
                    $statusColor = $semester->is_current ? 'success' : 'secondary';
                    $statusLabel = $semester->is_current ? 'Semestre atual' : 'Histórico';
                @endphp

                <span class="text-{{ $statusColor }} fw-bold text-uppercase" style="font-size: 0.85rem;">
                    {{ $statusLabel }}
                </span>
            </x-table.td>

            <x-table.td :responsive="false">
                <x-table.actions>
                    @canany(['semester.view', 'semester.update', 'semester.delete'])
                        @can('semester.view')
                            <x-buttons.link-button
                                :href="route('specialized-educational-support.semesters.show', $semester)"
                                variant="info"
                            >
                                <i class="fas fa-eye"></i> Ver
                            </x-buttons.link-button>
                        @endcan

                        @if(!$semester->is_current)
                            @can('semester.update')
                                <form action="{{ route('specialized-educational-support.semesters.setCurrent', $semester) }}"
                                      method="POST"
                                      class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <x-buttons.submit-button variant="success">
                                        <i class="fas fa-check"></i> Definir atual
                                    </x-buttons.submit-button>
                                </form>
                            @endcan
                        @endif

                        @can('semester.delete')
                        @if(!$semester->is_current)
                            <x-buttons.submit-button
                                type="button"
                                variant="danger"
                                data-bs-toggle="modal"
                                data-bs-target="#globalConfirmActionModal"
                                data-confirm-title="Excluir Semestre"
                                data-confirm-message="Tem certeza que deseja excluir este semestre?"
                                data-confirm-action="{{ route('specialized-educational-support.semesters.destroy', $semester) }}"
                                data-confirm-method="DELETE"
                                data-confirm-submit-text="Confirmar Exclusao"
                                data-confirm-variant="danger"
                            >
                                    <i class="fas fa-trash"></i> Excluir
                                </x-buttons.submit-button>
                        @endif
                        @endcan
                    @else
                        <span class="text-purple-light">Nenhuma ação</span>
                    @endcanany
                </x-table.actions>
            </x-table.td>
        </tr>
    @empty
        <tr>
            <td colspan="4" class="text-center text-muted py-4">
                Nenhum semestre encontrado.
            </td>
        </tr>
    @endforelse
</x-table.table>
