document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('loanable_type');
    const itemSelect = document.getElementById('loanable_id');
    const loanData = window.loanData;

    function updateItems() {
        if (!typeSelect || !itemSelect || !loanData) return;

        const selectedType = typeSelect.value;

        if (!selectedType) {
            itemSelect.innerHTML = '<option value="">Selecione o tipo primeiro</option>';
            itemSelect.disabled = true;
            return;
        }

        itemSelect.disabled = false;
        itemSelect.innerHTML = '<option value="">-- Selecione o item --</option>';

        const availableItems = loanData.items[selectedType] || [];

        // FILTRO DIGITAL: digitais sempre aparecem, físicos só se quantity_available > 0
        const filteredItems = availableItems.filter(item => {
            if (item.is_digital) return true;
            return (item.quantity_available ?? 0) > 0;
        });

        if (filteredItems.length > 0) {
            filteredItems.forEach(item => {
                const option = document.createElement('option');
                option.value = String(item.id);
                const displayName = item.name || item.title || item.description || 'Item sem identificação';
                const assetCode = item.asset_code || 'S/N';
                option.text = `${displayName} (${assetCode})`;

                if (String(item.id) === String(loanData.oldId)) {
                    option.selected = true;
                }

                itemSelect.appendChild(option);
            });
        } else {
            itemSelect.innerHTML = '<option value="">Nenhum item disponível para este tipo</option>';
        }
    }

    if (typeSelect) {
        typeSelect.addEventListener('change', updateItems);
        updateItems(); // Inicializa
    }

    // --- BLOQUEIO STUDENT / PROFESSIONAL ---
    const studentSelect = document.getElementById('student_id');
    const professionalSelect = document.getElementById('professional_id');

    function setupToggle(primary, secondary) {
        if (!primary || !secondary) return;

        primary.addEventListener('change', () => {
            if (primary.value) {
                secondary.value = '';
                secondary.disabled = true;
                secondary.parentElement.style.opacity = '0.6';
            } else {
                secondary.disabled = false;
                secondary.parentElement.style.opacity = '1';
            }
        });
    }

    setupToggle(studentSelect, professionalSelect);
    setupToggle(professionalSelect, studentSelect);

    if (studentSelect?.value) professionalSelect.disabled = true;
    if (professionalSelect?.value) studentSelect.disabled = true;
});
