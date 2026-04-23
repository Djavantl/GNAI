<x-table.table
    :headers="[
        ['label' => 'Título',          'responsive' => false],
        ['label' => 'Tipo',            'responsive' => true],
        ['label' => 'Semestre',        'responsive' => true],
        ['label' => 'Tamanho',         'responsive' => true],
        ['label' => 'Data de Upload',  'responsive' => true],
        ['label' => 'Ações',           'responsive' => false],
    ]"
    :records="$documents"
>
    @forelse($documents as $document)
        <tr>
            <x-table.td :responsive="false">
                <span class="fw-bold text-purple-dark">
                    {{ $document->title }}
                </span>
                <br>
                <small class="text-muted">
                    {{ $document->original_name }}
                </small>
            </x-table.td>

            <x-table.td>{{ $document->type->label() }}</x-table.td>

            <x-table.td>{{ $document->semester->label }}</x-table.td>

            <x-table.td>{{ number_format($document->file_size / 1024 / 1024, 2) }} MB</x-table.td>

            <x-table.td>{{ $document->created_at->format('d/m/Y H:i') }}</x-table.td>

            <x-table.td :responsive="false">
                <x-table.actions>
                    @canany(['student-document.view', 'student-document.update', 'student-document.delete'])
                        @can('student-document.view')
                            <x-buttons.link-button
                                :href="route('specialized-educational-support.student-documents.view', $document)"
                                target="_blank"
                                variant="info"
                                title="Ver arquivo"
                            >
                                <i class="fas fa-eye"></i> Ver
                            </x-buttons.link-button>

                            <x-buttons.link-button
                                :href="route('specialized-educational-support.student-documents.download', $document)"
                                variant="secondary"
                                title="Baixar arquivo"
                            >
                                <i class="fas fa-download"></i> Baixar
                            </x-buttons.link-button>
                        @endcan

                        @can('student-document.update')
                            <x-buttons.link-button
                                :href="route('specialized-educational-support.student-documents.edit', $document)"
                                variant="warning"
                                title="Editar arquivo"
                            >
                                <i class="fas fa-edit"></i> Editar
                            </x-buttons.link-button>
                        @endcan

                        @can('student-document.delete')
                            <form action="{{ route('specialized-educational-support.student-documents.destroy', $document) }}"
                                  method="POST"
                                  class="d-inline">
                                <x-buttons.submit-button
                                    type="button"
                                    variant="danger"
                                    data-bs-toggle="modal"
                                    data-bs-target="#globalConfirmActionModal"
                                    data-confirm-title="Excluir Documento"
                                    data-confirm-message="Deseja excluir este documento permanentemente?"
                                    data-confirm-action="{{ route('specialized-educational-support.student-documents.destroy', $document) }}"
                                    data-confirm-method="DELETE"
                                    data-confirm-submit-text="Confirmar Exclusao"
                                    data-confirm-variant="danger"
                                    title="Excluir arquivo"
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
            <td colspan="6" class="text-center text-muted py-4">
                Nenhum documento do aluno encontrado.
            </td>
        </tr>
    @endforelse
</x-table.table>
