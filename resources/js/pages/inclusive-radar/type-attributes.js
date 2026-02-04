document.addEventListener('DOMContentLoaded', function() {
    const labelContainer = document.getElementById('input_label');
    const nameContainer = document.getElementById('input_name');

    if (labelContainer && nameContainer) {
        const labelInput = labelContainer.tagName === 'INPUT' ? labelContainer : labelContainer.querySelector('input');
        const nameInput = nameContainer.tagName === 'INPUT' ? nameContainer : nameContainer.querySelector('input');

        if (labelInput && nameInput) {
            labelInput.addEventListener('input', function() {
                let slug = this.value
                    .toLowerCase()
                    .normalize("NFD")
                    .replace(/[\u0300-\u036f]/g, "")
                    .replace(/[^a-z0-9\s_]/g, "")
                    .trim()
                    .replace(/\s+/g, '_');

                nameInput.value = slug;
            });
        }
    }
});
