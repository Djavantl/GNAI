document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('loanable_type');
    const itemSelect = document.getElementById('loanable_id');
    const loanData = window.loanData;

    function updateItems() {
        if (!typeSelect || !itemSelect || !loanData) return;

        const selectedType = typeSelect.value;

        // Se não houver tipo selecionado, desabilita o segundo e limpa
        if (!selectedType) {
            itemSelect.innerHTML = '<option value="">Selecione o tipo primeiro</option>';
            itemSelect.disabled = true;
            return;
        }

        itemSelect.disabled = false;
        itemSelect.innerHTML = '<option value="">-- Selecione o item --</option>';

        const availableItems = loanData.items[selectedType];

        if (availableItems && availableItems.length > 0) {
            availableItems.forEach(item => {
                const option = document.createElement('option');
                option.value = item.id.toString();
                const displayName = item.name || item.title || item.description || 'Item sem identificação';
                const assetCode = item.asset_code || 'S/N';
                option.text = `${displayName} (${assetCode})`;

                if (option.value === loanData.oldId) {
                    option.selected = true;
                }
                itemSelect.appendChild(option);
            });

            // Opcional: Anunciar via console ou log de acessibilidade que a lista foi carregada
            console.log(`Carregados ${availableItems.length} itens.`);
        } else {
            itemSelect.innerHTML = '<option value="">Nenhum item disponível para este tipo</option>';
        }
    }

    if (typeSelect) {
        typeSelect.addEventListener('change', updateItems);
        // Inicializa (importante para o 'old input' do Laravel)
        updateItems();
    }

    // --- SEÇÃO 2: Bloqueio Acessível ---
    const studentSelect = document.getElementById('student_id');
    const professionalSelect = document.getElementById('professional_id');

    function setupToggle(primary, secondary) {
        if (!primary || !secondary) return;

        primary.addEventListener('change', () => {
            if (primary.value) {
                secondary.value = '';
                secondary.disabled = true;
                // Adicionamos uma dica visual/acessível
                secondary.parentElement.style.opacity = '0.6';
            } else {
                secondary.disabled = false;
                secondary.parentElement.style.opacity = '1';
            }
        });
    }

    setupToggle(studentSelect, professionalSelect);
    setupToggle(professionalSelect, studentSelect);

    // Inicialização do estado de bloqueio
    if (studentSelect?.value) professionalSelect.disabled = true;
    if (professionalSelect?.value) studentSelect.disabled = true;
});
