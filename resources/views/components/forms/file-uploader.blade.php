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
    // 1. Mantém os arquivos selecionados temporariamente no estado do JS
    window.selectedFiles_{{ $name }} = [];

    /**
     * Sincroniza o array de arquivos do JavaScript com o input HTML real.
     * Sem isso, o Laravel não recebe os arquivos no request.
     */
    function syncInputFiles(listId) {
        const input = document.getElementById('input-' + listId);
        const dataTransfer = new DataTransfer();

        window.selectedFiles_{{ $name }}.forEach(file => {
            dataTransfer.items.add(file);
        });

        // Injeta a lista de arquivos de volta no input que será enviado pelo formulário
        input.files = dataTransfer.files;
    }

    function handleFileSelection(input, listId) {
        const files = Array.from(input.files);

        // Se NÃO for múltiplo, limpa a lista anterior antes de adicionar o novo
        if (!{{ $multiple ? 'true' : 'false' }}) {
            window.selectedFiles_{{ $name }} = [];
        }

        // Adiciona os novos arquivos selecionados à nossa lista global
        files.forEach(file => {
            window.selectedFiles_{{ $name }}.push(file);
        });

        // 1. Sincroniza o input real com a lista (importante!)
        syncInputFiles(listId);

        // 2. Atualiza a interface visual para o usuário
        updateFileListPreview(listId);
    }

    function updateFileListPreview(listId) {
        const container = document.getElementById('list-' + listId);
        container.innerHTML = '';

        window.selectedFiles_{{ $name }}.forEach((file, index) => {
            const div = document.createElement('div');
            div.className = 'd-flex gap-2 mb-1 align-items-center animate__animated animate__fadeIn';

            div.innerHTML = `
                <div class="flex-grow-1 p-2 bg-light border rounded text-truncate">
                    <i class="fas fa-file-upload me-2 text-primary"></i>
                    <small>${file.name}</small>
                    <span class="badge bg-secondary ms-2">${(file.size / 1024).toFixed(1)} KB</span>
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeSelectedFile(${index}, '${listId}')">
                    <i class="fas fa-trash"></i>
                </button>
            `;

            container.appendChild(div);
        });
    }

    function removeSelectedFile(index, listId) {
        // Remove do array do JavaScript
        window.selectedFiles_{{ $name }}.splice(index, 1);

        // Atualiza o input real e a interface
        syncInputFiles(listId);
        updateFileListPreview(listId);
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE de arquivos já existentes no servidor (Edit)
    |--------------------------------------------------------------------------
    */
    function deleteFile(button) {
        const url = button.getAttribute('data-url');
        const token = button.getAttribute('data-token');

        // Desabilita o botão para evitar cliques múltiplos
        button.disabled = true;

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
                    const item = button.closest('.existing-file-item');
                    item.classList.add('animate__animated', 'animate__fadeOutRight');
                    setTimeout(() => item.remove(), 500);
                } else {
                    return response.json().then(data => {
                        throw new Error(data.message || 'Erro ao remover arquivo');
                    });
                }
            })
            .catch(error => {
                alert(error.message);
                button.disabled = false;
            });

        return false;
    }
</script>
