<x-table.table
    :headers="[
        ['label' => 'Responsável',              'responsive' => false],
        ['label' => 'CPF',                'responsive' => true],
        ['label' => 'Vínculo',                  'responsive' => true],
        ['label' => 'Contato',                  'responsive' => true],
        ['label' => 'Ações',                    'responsive' => false],
    ]"
    :records="$guardians"
>
    @forelse($guardians as $guardian)
        <tr>
            <x-table.td :responsive="false">
                <div class="d-flex align-items-center gap-2">
                    <img src="{{ $guardian->person->photo_url }}"
                         class="rounded-circle"
                         style="width:36px;height:36px;object-fit:cover;"
                         alt="Foto de {{ $guardian->person->name }}">
                    <span>{{ $guardian->person->name }}</span>
                </div>
            </x-table.td>

            <x-table.td>{{ $guardian->person->document ?? '—' }}</x-table.td>

            <x-table.td>{{ $guardian->relationshipLabel() }}</x-table.td>

            <x-table.td>{{ $guardian->person->email ?? $guardian->person->phone ?? '—' }}</x-table.td>

            <x-table.td :responsive="false">
                <x-table.actions>
                    @canany(['guardian.view', 'guardian.delete'])
                        @can('guardian.view')
                            <x-buttons.link-button
                                :href="route('specialized-educational-support.guardians.show', $guardian)"
                                variant="info"
                            >
                                <i class="fas fa-eye"></i> Ver
                            </x-buttons.link-button>
                        @endcan

                        @can('guardian.delete')
                            <x-buttons.submit-button
                                type="button"
                                variant="danger"
                                data-bs-toggle="modal"
                                data-bs-target="#globalConfirmActionModal"
                                data-confirm-title="Excluir Responsável"
                                data-confirm-message="Deseja remover este vínculo de responsabilidade? Esta ação não pode ser desfeita."
                                data-confirm-action="{{ route('specialized-educational-support.guardians.destroy', [$student, $guardian]) }}"
                                data-confirm-method="DELETE"
                                data-confirm-submit-text="Confirmar Exclusão"
                                data-confirm-variant="danger"
                            >
                                <i class="fas fa-trash"></i> Excluir
                            </x-buttons.submit-button>
                        @endcan
                    @else
                        <span class="text-muted small">Sem ações</span>
                    @endcanany
                </x-table.actions>
            </x-table.td>
        </tr>
    @empty
        <tr>
            <td colspan="5" class="text-center text-muted py-4">
                Nenhum responsável encontrado para este aluno.
            </td>
        </tr>
    @endforelse
</x-table.table>
