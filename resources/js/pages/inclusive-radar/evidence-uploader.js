document.querySelectorAll('.evidence-uploader').forEach(wrapper => {
    const input = wrapper.querySelector('input[type="file"]');
    const previewContainer = wrapper.querySelector('.preview-container');

    const iconFor = (file) => {
        const name = file.name.toLowerCase();

        if (file.type === 'application/pdf' || name.endsWith('.pdf')) return 'fa-file-pdf';
        if (name.match(/\.(doc|docx|odt)$/)) return 'fa-file-word';
        if (name.match(/\.(ppt|pptx|odp)$/)) return 'fa-file-powerpoint';
        if (name.match(/\.(xls|xlsx|ods|csv)$/)) return 'fa-file-excel';

        return 'fa-file-alt';
    };

    const escapeHtml = (value) => value
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');

    // DataTransfer para manter todos os arquivos selecionados
    const dt = new DataTransfer();

    input.addEventListener('change', function() {
        Array.from(this.files).forEach((file) => {
            // Adiciona o arquivo ao DataTransfer
            dt.items.add(file);

            const div = document.createElement('div');
            div.classList.add('position-relative', 'd-inline-block');
            div.style.width = '70px';
            div.style.height = '70px';
            div.setAttribute('role', 'listitem');

            // Cria Blob URL para abrir em nova aba
            const blobUrl = URL.createObjectURL(file);
            const safeName = escapeHtml(file.name);
            const preview = file.type.startsWith('image/')
                ? `<img src="${blobUrl}" alt="Pré-visualização da evidência ${safeName}"
                         class="rounded border" style="width: 100%; height: 100%; object-fit: cover;">`
                : `<div class="rounded border bg-light text-secondary d-flex flex-column align-items-center justify-content-center text-center p-1"
                         style="width: 100%; height: 100%;">
                        <i class="fas ${iconFor(file)} fa-lg mb-1" aria-hidden="true"></i>
                        <span class="small text-break" style="font-size:0.65rem;line-height:1;">${safeName}</span>
                   </div>`;

            div.innerHTML = `
                    <a href="${blobUrl}" target="_blank" aria-label="Visualizar ${safeName}">
                        ${preview}
                    </a>
                    <button type="button" class="remove-image-btn" aria-label="Remover ${safeName}"
                            style="position:absolute; top:-5px; right:-5px; background:#ff4d4f; color:white; border-radius:50%; width:18px; height:18px; display:flex; align-items:center; justify-content:center; border:none; cursor:pointer;">
                        &times;
                    </button>
                `;

            previewContainer.appendChild(div);

            // Remover evidência
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
        });

        // Mantém a seleção acumulada no input para envio do formulário.
        // Não limpamos `value` aqui porque, para previews de documentos, o processamento é síncrono
        // e limpar o input depois de atribuir `files` remove o anexo do submit.
        input.files = dt.files;
    });
});
