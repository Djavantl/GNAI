@props([
    'name' => 'file',
    'label' => 'Escolher Arquivo',
    'existingFiles' => [],
    'accept' => '*/*',
    'multiple' => false,
    'deleteRoute' => null,
    'training' => null,
])

<div class="mb-3 file-uploader">
    <label class="form-label fw-bold text-purple-dark">{{ $label }}</label>

    {{-- Input real escondido --}}
    <input type="file"
           id="input-{{ $name }}"
           name="{{ $name }}{{ $multiple ? '[]' : '' }}"
           @if($multiple) multiple @endif
           accept="{{ $accept }}"
           class="d-none"
           onchange="handleFileSelection(this, '{{ $name }}')">

    {{-- 1. ARQUIVOS JÁ SALVOS --}}
    <div class="existing-files-container d-flex flex-column gap-2 mb-2">
        @foreach($existingFiles as $file)
            <div class="d-flex gap-2 mb-2 existing-file-item">
                <div class="flex-grow-1">
                    <div class="form-control bg-light d-flex align-items-center justify-content-between" style="height: calc(1.5em + 0.75rem + 2px);">
                        <a href="{{ asset('storage/' . $file->path) }}" target="_blank" class="text-decoration-none text-primary text-truncate">
                            <i class="fas fa-file-alt me-2 text-secondary"></i>
                            {{ $file->original_name ?? basename($file->path) }}
                        </a>
                        <small class="text-muted ms-2">(Salvo)</small>
                    </div>
                </div>

                @if($deleteRoute && $training && $file->id)
                    <button type="button"
                            class="btn btn-outline-danger btn-delete-file"
                            data-url="{{ route($deleteRoute, [$training->id, $file->id]) }}"
                            data-file-id="{{ $file->id }}"
                            data-token="{{ csrf_token() }}"
                            onclick="return confirm('Tem certeza que deseja remover este arquivo permanentemente?') && deleteFile(this)">
                        <i class="fas fa-trash"></i>
                    </button>
                @endif
            </div>
        @endforeach
    </div>

    {{-- 2. LISTA DE NOVOS ARQUIVOS SELECIONADOS --}}
    <div id="list-{{ $name }}" class="mb-2 d-flex flex-column gap-1"></div>

    {{-- 3. BOTÃO DE AÇÃO --}}
    <div class="d-flex align-items-center gap-2 mt-2">
        <x-buttons.link-button
            href="javascript:void(0)"
            onclick="document.getElementById('input-{{ $name }}').click()"
            variant="primary"
            class="btn-sm"
        >
            <i class="fas fa-upload me-1"></i> {{ $multiple ? 'Selecionar Arquivos' : 'Selecionar Arquivo' }}
        </x-buttons.link-button>

        <div id="help-{{ $name }}" class="text-muted" style="font-size: 0.75rem;">
            Tipos: {{ $accept }}
        </div>
    </div>
</div>

<script>
    // Mantém os arquivos selecionados temporariamente
    window.selectedFiles_{{ $name }} = [];

    function handleFileSelection(input, listId) {
        const files = Array.from(input.files);

        // Adiciona arquivos à lista global
        window.selectedFiles_{{ $name }} = window.selectedFiles_{{ $name }}.concat(files);

        // Atualiza preview
        updateFileListPreview(listId);

        // Limpa input para permitir re-seleção
        input.value = '';
    }

    function updateFileListPreview(listId) {
        const container = document.getElementById('list-' + listId);
        container.innerHTML = '';

        window.selectedFiles_{{ $name }}.forEach((file, index) => {
            const div = document.createElement('div');
            div.className = 'd-flex gap-2 mb-1 align-items-center';

            div.innerHTML = `
                <span class="flex-grow-1 text-truncate">${file.name}</span>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeSelectedFile(${index}, '${listId}')">
                    <i class="fas fa-trash"></i>
                </button>
            `;

            container.appendChild(div);
        });
    }

    function removeSelectedFile(index, listId) {
        window.selectedFiles_{{ $name }}.splice(index, 1);
        updateFileListPreview(listId);
    }

    // DELETE de arquivos existentes
    function deleteFile(button) {
        const url = button.getAttribute('data-url');
        const token = button.getAttribute('data-token');

        fetch(url, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
            .then(response => {
                if (response.ok) {
                    button.closest('.existing-file-item').remove();
                    showSuccessMessage('Arquivo removido com sucesso!');
                } else {
                    return response.json().then(data => { throw new Error(data.message || 'Erro ao remover arquivo'); });
                }
            })
            .catch(error => alert(error.message));

        return false;
    }

    function showSuccessMessage(message) {
        alert(message);
    }
</script>
