<x-table.table :headers="['Título', 'Tipo', 'Semestre', 'Versão', 'Tamanho', 'Data de Upload', 'Ações']">
    @forelse($documents as $document)
        <tr>
            <x-table.td>
                <span class="fw-bold text-purple-dark">{{ $document->title }}</span>
                <br>
                <small class="text-muted">{{ $document->original_name }}</small>
            </x-table.td>
            
            <x-table.td>   
                    {{ $document->type->label() }}
            </x-table.td>

            <x-table.td>{{ $document->semester->label }}</x-table.td>

            <x-table.td>
                v{{ $document->version }}
            </x-table.td>

            <x-table.td>
                {{ number_format($document->file_size / 1024 / 1024, 2) }} MB
            </x-table.td>

            <x-table.td>
                {{ $document->created_at->format('d/m/Y H:i') }}
            </x-table.td>

            <x-table.td>
                <x-table.actions>
                    {{-- 1. BOTÃO VER (Visualização no Navegador ou Google Docs) --}}
                    @php
                        $extension = pathinfo($document->file_path, PATHINFO_EXTENSION);
                        $isViewable = in_array(strtolower($extension), ['pdf', 'jpg', 'jpeg', 'png']);
                        
                        // Se for PDF/Imagem, abre direto. Se for DOCX, usa o visualizador do Google.
                        $viewUrl = $isViewable 
                            ? Storage::disk('local')->url($document->file_path) 
                            : "https://docs.google.com/gview?url=" . Storage::disk('public')->url($document->file_path) . "&embedded=true";
                    @endphp

                    <x-buttons.link-button
                        :href="route('specialized-educational-support.student-documents.view', $document)" {{-- Rota interna segura --}}
                        target="_blank"
                        variant="info"
                        title="Ver Arquivo"
                    >
                        <i class="fas fa-eye" aria-hidden="true"></i>
                    </x-buttons.link-button>

                    {{-- 2. BOTÃO BAIXAR (Download forçado) --}}
                    <x-buttons.link-button
                        :href="route('specialized-educational-support.student-documents.download', $document)"
                        variant="secondary"
                        title="Baixar Arquivo"
                    >
                        <i class="fas fa-download" aria-hidden="true"></i>
                    </x-buttons.link-button>

                    {{-- 3. BOTÃO EDITAR --}}
                    <x-buttons.link-button
                        :href="route('specialized-educational-support.student-documents.edit', $document)"
                        variant="warning"
                        title="Editar Arquivo"
                    >
                        <i class="fas fa-edit" aria-hidden="true"></i>
                    </x-buttons.link-button>

                    {{-- 4. BOTÃO EXCLUIR --}}
                    <form action="{{ route('specialized-educational-support.student-documents.destroy', $document) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <x-buttons.submit-button
                            variant="danger"
                            onclick="return confirm('Deseja excluir este documento permanentemente?')"
                            title="Excluir Arquivo"
                        >
                            <i class="fas fa-trash" aria-hidden="true"></i>
                        </x-buttons.submit-button>
                    </form>
                </x-table.actions>
            </x-table.td>
        </tr>
    @empty
        <tr>
            <td colspan="7" class="text-center text-muted fw-bold py-5">
                <i class="fas fa-folder-open d-block mb-2" style="font-size: 2.5rem;"></i>
                Nenhum documento do aluno encontrado.
            </td>
        </tr>
    @endforelse
</x-table.table>