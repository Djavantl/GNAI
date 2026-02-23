<section id="documents" class="mb-5 bg-soft-info rounded shadow-sm">

    <x-forms.section title="Documentos do Aluno" class="m-0" />

    <div class="pb-3 ps-3 pe-3">

        <div class="table-responsive">
            <x-table.table :headers="['Título', 'Tipo', 'Upload', '']">
                
                @forelse($student->documents as $document)
                    <tr>

                        {{-- TÍTULO --}}
                        <x-table.td>
                            <span class="fw-bold text-purple-dark">{{ $document->title }}</span>
                            <br>
                            <small class="text-muted">{{ $document->original_name }}</small>
                        </x-table.td>

                        {{-- TIPO --}}
                        <x-table.td>
                            {{ $document->type->label() }}
                        </x-table.td>

                        {{-- DATA --}}
                        <x-table.td>
                            {{ $document->created_at->format('d/m/Y') }}
                        </x-table.td>

                        {{-- AÇÕES --}}
                        <x-table.td>
                            <x-table.actions>

                                <x-buttons.link-button
                                    :href="route('specialized-educational-support.student-documents.view', $document)"
                                    target="_blank"
                                    variant="info"
                                    class="btn-sm"
                                >
                                    <i class="fas fa-eye"></i>
                                </x-buttons.link-button>

                            </x-table.actions>
                        </x-table.td>

                    </tr>

                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            Nenhum documento registrado.
                        </td>
                    </tr>
                @endforelse

            </x-table.table>
        </div>

        {{-- BOTÃO GERENCIAR --}}
        <div class="d-flex justify-content-end align-items-center gap-2 mt-4 pt-3 border-top">
            <x-buttons.link-button
                :href="route('specialized-educational-support.student-documents.index', $student)"
                variant="warning"
                class="btn-sm">
                <i class="fas fa-folder-open"></i> Gerenciar Documentos
            </x-buttons.link-button>
        </div>

    </div>
</section>
