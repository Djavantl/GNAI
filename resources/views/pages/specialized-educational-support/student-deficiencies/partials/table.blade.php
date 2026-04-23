<x-table.table
    :headers="[
        ['label' => 'Nome',        'responsive' => false],
        ['label' => 'Severidade',         'responsive' => true],
        ['label' => 'Recursos de Apoio',  'responsive' => true],
        ['label' => 'Ações',              'responsive' => false],
    ]"
    :records="$deficiencies"
>
    @forelse($deficiencies as $deficiency)
        <tr>
            <x-table.td :responsive="false"><strong>{{ $deficiency->deficiency->name }}</strong></x-table.td>

            <x-table.td>
                @php
                    $severityLabels = [
                        'mild' => 'Leve',
                        'moderate' => 'Moderada',
                        'severe' => 'Severa'
                    ];

                    $severityColors = [
                        'mild' => 'success',
                        'moderate' => 'warning',
                        'severe' => 'danger'
                    ];
                @endphp

                @if($deficiency->severity)
                    <span class="text-{{ $severityColors[$deficiency->severity] }} fw-bold text-uppercase"
                          style="font-size: 0.85rem;">
                        {{ $severityLabels[$deficiency->severity] }}
                    </span>
                @else
                    <span class="text-muted small">Não informada</span>
                @endif
            </x-table.td>

            <x-table.td>
                @php
                    $supportColor = $deficiency->uses_support_resources ? 'success' : 'secondary';
                    $supportLabel = $deficiency->uses_support_resources ? 'Sim' : 'Não';
                @endphp

                <span class="text-{{ $supportColor }} fw-bold text-uppercase"
                      style="font-size: 0.85rem;">
                    {{ $supportLabel }}
                </span>
            </x-table.td>

            <x-table.td :responsive="false">
                <x-table.actions>
                    @canany(['student-deficiency.view', 'student-deficiency.delete'])
                        @can('student-deficiency.view')
                            <x-buttons.link-button
                                :href="route('specialized-educational-support.student-deficiencies.show', [$student, $deficiency])"
                                variant="info"
                            >
                                <i class="fas fa-eye"></i> Ver
                            </x-buttons.link-button>
                        @endcan

                        @can('student-deficiency.delete')
                            <x-buttons.submit-button
                                type="button"
                                variant="danger"
                                data-bs-toggle="modal"
                                data-bs-target="#globalConfirmActionModal"
                                data-confirm-title="Excluir Perfil de Atendimento"
                                data-confirm-message="Deseja remover este perfil de atendimento do registro do aluno?"
                                data-confirm-action="{{ route('specialized-educational-support.student-deficiencies.destroy', [$student, $deficiency]) }}"
                                data-confirm-method="DELETE"
                                data-confirm-submit-text="Confirmar Exclusao"
                                data-confirm-variant="danger"
                            >
                                    <i class="fas fa-trash"></i> Excluir
                                </x-buttons.submit-button>
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
                Nenhum perfil de atendimento do aluno encontrado.
            </td>
        </tr>
    @endforelse
</x-table.table>
