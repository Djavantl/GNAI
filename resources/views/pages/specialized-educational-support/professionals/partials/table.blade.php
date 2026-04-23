<x-table.table
    :headers="[
        ['label' => 'Nome',   'responsive' => false],
        ['label' => 'Email',  'responsive' => true],
        ['label' => 'Cargo',  'responsive' => true],
        ['label' => 'Status', 'responsive' => true],
        ['label' => 'Ações',  'responsive' => false],
    ]"
    :records="$professionals"
    aria-label="Tabela de profissionais do suporte educacional"
>
    @forelse($professionals as $professional)
        <tr>
            <x-table.td :responsive="false">
                <div class="name-with-photo">
                    <img src="{{ $professional->person->photo_url }}"
                         class="avatar-table"
                         alt="Foto de {{ $professional->person->name }}">
                    <span class="fw-bold text-purple-dark">
                    {{ $professional->person->name }}
                </span>
                </div>
            </x-table.td>

            <x-table.td>
                {{ $professional->person->email }}
            </x-table.td>

            <x-table.td>
                {{ $professional->position->name }}
            </x-table.td>

            <x-table.td>
                @php
                    $statusColor = $professional->status === 'active' ? 'success' : 'danger';
                    $statusLabel = $professional->status === 'active' ? 'Ativo' : 'Inativo';
                @endphp

                <span class="text-{{ $statusColor }} fw-bold text-uppercase"
                      style="font-size: 0.85rem;"
                      aria-label="Status: {{ $statusLabel }}">
                {{ $statusLabel }}
            </span>
            </x-table.td>

            <x-table.td :responsive="false">
                <x-table.actions>
                    @canany(['professional.view', 'professional.delete'])

                        @can('professional.view')
                            <x-buttons.link-button
                                :href="route('specialized-educational-support.professionals.show', $professional)"
                                variant="info"
                                aria-label="Visualizar prontuário de {{ $professional->person->name }}"
                            >
                                <i class="fas fa-eye" aria-hidden="true"></i> Ver
                            </x-buttons.link-button>
                        @endcan

                        @can('professional.delete')
                            <x-buttons.submit-button
                                type="button"
                                variant="danger"
                                data-bs-toggle="modal"
                                data-bs-target="#globalConfirmActionModal"
                                data-confirm-title="Excluir Profissional"
                                data-confirm-message="Deseja remover este profissional?"
                                data-confirm-action="{{ route('specialized-educational-support.professionals.destroy', $professional) }}"
                                data-confirm-method="DELETE"
                                data-confirm-submit-text="Confirmar Exclusao"
                                data-confirm-variant="danger"
                                aria-label="Excluir profissional {{ $professional->person->name }} do sistema"
                            >
                                    <i class="fas fa-trash" aria-hidden="true"></i> Excluir
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
            <td colspan="5" class="text-center text-muted py-4">
                Nenhum profissional encontrado.
            </td>
        </tr>
    @endforelse
</x-table.table>
