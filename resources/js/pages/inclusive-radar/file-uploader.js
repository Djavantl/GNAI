document.querySelectorAll('.file-uploader').forEach(wrapper => {
    const input = wrapper.querySelector('input[type="file"]');
    const filesContainer = document.createElement('div');
    filesContainer.classList.add('selected-files', 'd-flex', 'flex-column', 'gap-1', 'mb-2');
    wrapper.insertBefore(filesContainer, wrapper.querySelector('.link-button'));

    // DataTransfer para manter todos os arquivos selecionados
    const dt = new DataTransfer();

    input.addEventListener('change', function() {
        Array.from(this.files).forEach(file => {
            // Adiciona o arquivo ao DataTransfer
            dt.items.add(file);

            // Cria item visual do arquivo
            const fileItem = document.createElement('div');
            fileItem.classList.add('d-flex', 'align-items-center', 'justify-content-between', 'p-2', 'border', 'rounded', 'bg-light');

            fileItem.innerHTML = `
                <span><i class="fas fa-file-alt me-2"></i> ${file.name}</span>
                <button type="button" class="remove-file-btn btn btn-sm btn-danger">&times;</button>
            `;

            // Evento para remover arquivo
            fileItem.querySelector('.remove-file-btn').addEventListener('click', () => {
                // Remove do DataTransfer
                const files = Array.from(dt.files);
                const index = files.findIndex(f => f.name === file.name && f.size === file.size && f.type === file.type);
                if (index > -1) dt.items.remove(index);

                // Atualiza input.files
                input.files = dt.files;

                // Remove item da lista
                fileItem.remove();
            });

            filesContainer.appendChild(fileItem);

            // Atualiza input.files sempre
            input.files = dt.files;
        });

        // Limpa input para permitir selecionar arquivos repetidos
        this.value = '';
    });
});
