document.addEventListener('DOMContentLoaded', () => {

    document.querySelectorAll('.photo-upload').forEach(component => {

        const container = component.querySelector('[data-container]');
        const input = component.querySelector('[data-input]');
        const image = component.querySelector('[data-image]');
        const empty = component.querySelector('[data-empty]');
        const removeBtn = component.querySelector('[data-remove]');
        const removeFlag = component.querySelector('[data-remove-flag]');


        container.addEventListener('click', () => {
            input.click();
        });

        input.addEventListener('change', () => {
            const file = input.files[0];
            if (!file) return;

            if (!file.type.startsWith('image/')) {
                alert('Selecione uma imagem válida');
                input.value = '';
                return;
            }

            const reader = new FileReader();

           reader.onload = e => {
                image.src = e.target.result;
                image.classList.remove('d-none');
                empty.classList.add('d-none');
                removeBtn.classList.remove('d-none');

                if (removeFlag) {
                    removeFlag.value = 0;
                }
            };

            reader.readAsDataURL(file);
        });

        removeBtn.addEventListener('click', e => {
            e.stopPropagation();

            input.value = '';
            image.src = '';
            image.classList.add('d-none');
            empty.classList.remove('d-none');
            removeBtn.classList.add('d-none');

            if (removeFlag) {
                removeFlag.value = 1; // ← ISSO avisa o backend
            }
        });

    });

});
