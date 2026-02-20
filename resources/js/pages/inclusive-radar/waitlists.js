document.addEventListener('DOMContentLoaded', function () {
    const studentSelect = document.getElementById('student_id');
    const professionalSelect = document.getElementById('professional_id');
    const typeSelect = document.getElementById('waitlistable_type');
    const itemSelect = document.getElementById('waitlistable_id');
    const waitlistData = window.waitlistData;

    // --- SEÇÃO 1: Popula itens do recurso ---
    function updateItems() {
        if (!typeSelect || !itemSelect || !waitlistData) return;

        const selectedType = typeSelect.value;
        itemSelect.innerHTML = '<option value="">-- Selecione o item --</option>';

        const availableItems = waitlistData.items[selectedType];

        if (availableItems) {
            availableItems.forEach(item => {
                const option = document.createElement('option');
                option.value = item.id.toString();
                const displayName = item.name || item.title || item.description || 'Item sem identificação';
                const assetCode = item.asset_code || 'S/N';
                option.text = `${displayName} (${assetCode})`;
                if (option.value === waitlistData.oldId) option.selected = true;
                itemSelect.appendChild(option);
            });
        }
    }

    if (typeSelect) {
        typeSelect.addEventListener('change', updateItems);
        if (typeSelect.value) updateItems(); // Inicializa se já houver valor
    }

    // --- SEÇÃO 2: Bloqueia aluno/profissional mutuamente ---
    function toggleOther(selectChanged, selectToToggle) {
        selectChanged.addEventListener('change', () => {
            if (selectChanged.value) {
                selectToToggle.value = ''; // limpa o outro
                selectToToggle.disabled = true;
            } else {
                selectToToggle.disabled = false;
            }
        });
    }

    if (studentSelect && professionalSelect) {
        toggleOther(studentSelect, professionalSelect);
        toggleOther(professionalSelect, studentSelect);

        // Inicializa bloqueio caso já tenha valor
        if (studentSelect.value) professionalSelect.disabled = true;
        if (professionalSelect.value) studentSelect.disabled = true;
    }
});
