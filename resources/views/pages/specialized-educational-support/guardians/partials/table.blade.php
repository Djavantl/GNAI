<x-table.table
    :headers="[
        ['label' => 'Nome',                     'responsive' => false],
        ['label' => 'Documento',                'responsive' => true],
        ['label' => 'Vínculo (Parentesco)',     'responsive' => true],
        ['label' => 'Contato',                  'responsive' => true],
        ['label' => 'Ações',                    'responsive' => false],
    ]"
    :records="$guardians"
>
    @forelse($guardians as $guardian)
        <tr>
            <x-table.td :responsive="false">{{ $guardian->person->name }}</x-table.td>

            <x-table.td>{{ $guardian->person->document }}</x-table.td>

            <x-table.td>{{ ucfirst($guardian->relationship) }}</x-table.td>

            <x-table.td>{{ $guardian->person->email }}</x-table.td>

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
                            <form action="{{ route('specialized-educational-support.guardians.destroy', [$student, $guardian]) }}"
                                  method="POST"
                                  class="d-inline">
                                @csrf
                                @method('DELETE')
                                <x-buttons.submit-button
                                    variant="danger"
                                    onclick="return confirm('Deseja remover este vínculo de responsabilidade?')"
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
                Nenhum responsável do aluno encontrado.
            </td>
        </tr>
    @endforelse
</x-table.table>
