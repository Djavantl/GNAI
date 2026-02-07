document.querySelectorAll('.image-uploader').forEach(wrapper => {
    const input = wrapper.querySelector('input[type="file"]');
    const previewContainer = wrapper.querySelector('.preview-container');

    // DataTransfer para manter todos os arquivos selecionados
    const dt = new DataTransfer();

    input.addEventListener('change', function() {
        Array.from(this.files).forEach((file) => {
            // Adiciona o arquivo ao DataTransfer
            dt.items.add(file);

            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.classList.add('position-relative', 'd-inline-block');
                div.style.width = '70px';
                div.style.height = '70px';
                div.setAttribute('role', 'listitem');

                // Cria Blob URL para abrir em nova aba
                const blob = new Blob([file], { type: file.type });
                const blobUrl = URL.createObjectURL(blob);

                div.innerHTML = `
                    <a href="${blobUrl}" target="_blank" aria-label="Visualizar ${file.name}">
                        <img src="${e.target.result}" alt="Pré-visualização da imagem ${file.name}"
                             class="rounded border" style="width: 100%; height: 100%; object-fit: cover;">
                    </a>
                    <button type="button" class="remove-image-btn" aria-label="Remover ${file.name}"
                            style="position:absolute; top:-5px; right:-5px; background:#ff4d4f; color:white; border-radius:50%; width:18px; height:18px; display:flex; align-items:center; justify-content:center; border:none; cursor:pointer;">
                        &times;
                    </button>
                `;

                previewContainer.appendChild(div);

                // Remover imagem
                div.querySelector('.remove-image-btn').addEventListener('click', () => {
                    // Remove do DataTransfer
                    const files = Array.from(dt.files);
                    const index = files.findIndex(f => f.name === file.name && f.size === file.size && f.type === file.type);
                    if (index > -1) dt.items.remove(index);

                    // Atualiza input.files
                    input.files = dt.files;

                    // Remove preview
                    div.remove();

                    // Libera Blob URL
                    URL.revokeObjectURL(blobUrl);
                });

                // Atualiza input.files sempre
                input.files = dt.files;
            };
            reader.readAsDataURL(file);
        });

        // Limpa o input para permitir selecionar arquivos repetidos
        this.value = '';
    });
});
