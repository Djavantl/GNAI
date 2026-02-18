@props([
    'name' => 'file',
    'label' => 'Escolher Arquivo',
    'existingFiles' => [],
    'accept' => '*/*',
    'multiple' => false,
    'deleteRoute' => null,
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
           onchange="updateFileList(this, 'list-{{ $name }}')">

    {{-- 1. ARQUIVOS JÁ SALVOS --}}
    <div class="existing-files-container d-flex flex-column gap-2 mb-2">
        @foreach($existingFiles as $file)
            <div class="d-flex gap-2 mb-2 existing-file-item">
                <div class="flex-grow-1">
                    <div class="form-control bg-light d-flex align-items-center justify-content-between" style="height: calc(1.5 em + 0.75 rem + 2 px);">
                        <a href="{{ asset('storage/' . $file->path) }}" target="_blank" class="text-decoration-none text-primary text-truncate">
                            <i class="fas fa-file-alt me-2 text-secondary"></i>
                            {{ $file->original_name ?? basename($file->path) }}
                        </a>
                        <small class="text-muted ms-2">(Salvo)</small>
                    </div>
                </div>

                @if($deleteRoute)
                    <form action="{{ route($deleteRoute, $file->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        {{-- Botão lixeira igual ao dos links --}}
                        <button type="submit"
                                class="btn btn-outline-danger"
                                onclick="return confirm('Tem certeza que deseja remover este arquivo permanentemente?')"
                                title="Remover arquivo">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                @endif
            </div>
        @endforeach
    </div>

    {{-- 2. LISTA DE NOVOS ARQUIVOS SELECIONADOS --}}
    <div id="list-{{ $name }}" class="mb-2 d-flex flex-column gap-1"></div>

    {{-- 3. BOTÃO DE AÇÃO (O ROXINHO) --}}
    <div class="d-flex align-items-center gap-2 mt-2">
        {{-- Voltando para variant="primary" para garantir a cor roxa do seu tema --}}
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
    if (typeof updateFileList !== 'function') {
        function updateFileList(input, listId) {
            const list = document.getElementById(listId);
            list.innerHTML = '';

            if (input.files.length > 0) {
                const header = document.createElement('div');
                header.className = 'fw-bold small text-success mb-1 mt-2';
                header.innerHTML = '<i class="fas fa-check-circle"></i> Novos arquivos para enviar:';
                list.appendChild(header);

                Array.from(input.files).forEach(file => {
                    const item = document.createElement('div');
                    item.className = 'small text-muted ps-3 border-start ms-2 mb-1';
                    item.innerHTML = `<i class="fas fa-paperclip me-1"></i> ${file.name} (${(file.size / 1024).toFixed(1)} KB)`;
                    list.appendChild(item);
                });
            }
        }
    }
</script>
