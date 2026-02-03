document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('loanable_type');
    const itemSelect = document.getElementById('loanable_id');

    // Pegamos os dados da "Ponte" que criamos no HTML
    const loanData = window.loanData;

    function updateItems() {
        if (!typeSelect || !itemSelect || !loanData) return;

        const selectedType = typeSelect.value;
        itemSelect.innerHTML = '<option value="">-- Selecione o item --</option>';

        const availableItems = loanData.items[selectedType];

        if (availableItems) {
            availableItems.forEach(item => {
                const option = document.createElement('option');
                option.value = item.id.toString(); // Força string para evitar o erro de type coercion

                const displayName = item.name || item.title || item.description || 'Item sem identificação';
                const assetCode = item.asset_code || 'S/N';

                option.text = `${displayName} (${assetCode})`;

                // Compara usando a variável que veio da ponte
                if (option.value === loanData.oldId) {
                    option.selected = true;
                }

                itemSelect.appendChild(option);
            });
        }
    }

    if (typeSelect) {
        typeSelect.addEventListener('change', updateItems);

        // Se já tiver algo selecionado (erro de validação), popula os itens
        if (typeSelect.value) {
            updateItems();
        }
    }
});
