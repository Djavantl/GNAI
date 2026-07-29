<x-table.table
    :headers="[
        ['label' => 'Arquivo ZIP', 'responsive' => false],
        ['label' => 'Status',      'responsive' => true],
        ['label' => 'Tamanho',     'responsive' => true],
        ['label' => 'Criado em',   'responsive' => true],
        ['label' => 'Responsável', 'responsive' => true],
        ['label' => 'Ações',       'responsive' => false],
    ]"
    :records="$backups"
>
    @forelse($backups as $backup)
        <tr>
            <x-table.td :responsive="false">
                <a href="{{ route('backup.backups.show', $backup->id) }}"
                   class="fw-medium text-decoration-none text-dark">
                    <i class="fas fa-file-archive text-warning me-1"></i>
                    {{ $backup->file_name }}
                </a>
            </x-table.td>

            <x-table.td>
                @php
                    $status = $backup->status;
                @endphp

                <span class="text-{{ $status?->color() ?? 'secondary' }} fw-bold text-uppercase"
                      style="font-size: 0.85rem;">
                    {{ $status?->label() ?? 'Status desconhecido' }}
                </span>
            </x-table.td>

            <x-table.td>{{ $backup->size }}</x-table.td>

            <x-table.td>{{ $backup->created_at->format('d/m/Y H:i') }}</x-table.td>

            <x-table.td>{{ $backup->user->name ?? 'Sistema' }}</x-table.td>

            <x-table.td :responsive="false">
                <x-table.actions>
                    @canany(['backup.download', 'backup.restore', 'backup.show', 'backup.destroy'])
                        @can('backup.download')
                            <x-buttons.link-button
                                :href="route('backup.backups.download', $backup->id)"
                                variant="success"
                            >
                                <i class="fas fa-download"></i> Baixar
                            </x-buttons.link-button>
                        @endcan

                        @can('backup.restore')
                            @if($backup->status?->allowsRestore())
                                <x-buttons.submit-button
                                    type="button"
                                    variant="warning"
                                    data-bs-toggle="modal"
                                    data-bs-target="#backupActionModal"
                                    data-confirm-title="Restaurar Backup"
                                    data-confirm-message="O backup {{ $backup->file_name }} sera usado para restaurar o banco e os arquivos de midia do sistema. Esta operacao vai sobrescrever o ambiente atual."
                                    data-confirm-action="{{ route('backup.backups.restore', $backup->id) }}"
                                    data-confirm-method="POST"
                                    data-confirm-submit-text="Confirmar Restauracao"
                                    data-confirm-variant="warning"
                                    data-confirm-template="#restoreBackupPasswordTemplate"
                                >
                                    <i class="fas fa-history"></i> Restaurar
                                </x-buttons.submit-button>
                            @endif
                        @endcan

                        @can('backup.show')
                            <x-buttons.link-button
                                :href="route('backup.backups.show', $backup->id)"
                                variant="info"
                            >
                                <i class="fas fa-eye"></i> Ver
                            </x-buttons.link-button>
                        @endcan

                        @can('backup.destroy')
                            <x-buttons.submit-button
                                type="button"
                                variant="danger"
                                data-bs-toggle="modal"
                                data-bs-target="#backupActionModal"
                                data-confirm-title="Excluir Backup"
                                data-confirm-message="O arquivo {{ $backup->file_name }} sera apagado do servidor e o registro sera removido do historico."
                                data-confirm-action="{{ route('backup.backups.destroy', $backup->id) }}"
                                data-confirm-method="DELETE"
                                data-confirm-submit-text="Confirmar Exclusao"
                                data-confirm-variant="danger"
                            >
                                <i class="fas fa-trash-alt"></i> Excluir
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
            <td colspan="6" class="text-center text-muted py-4">
                Nenhum backup encontrado no histórico.
            </td>
        </tr>
    @endforelse
</x-table.table>
