document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('type_id');
    const dynamicAttrContainer = document.getElementById('dynamic-attributes-container');
    const dynamicAttrList = document.getElementById('dynamic-attributes');
    const assetContainer = document.getElementById('asset_code_container');
    const qtyContainer = document.getElementById('quantity_container');
    const qtyInput = document.getElementById('quantity_input');

    const currentValues = window.currentAttributeValues || {};

    /**
     * Gerencia a aparência e acessibilidade dos campos fixos
     * baseados no tipo de recurso (Digital vs Físico)
     */
    function handleTypeChange() {
        if (!typeSelect || typeSelect.selectedIndex === -1) return;

        const selected = typeSelect.options[typeSelect.selectedIndex];
        if (!selected) return;

        const isDigital = selected.dataset.digital === '1';

        // Ajuste visual e de acessibilidade para o container de Patrimônio
        if (assetContainer) {
            assetContainer.style.opacity = isDigital ? '0.5' : '1';
            assetContainer.setAttribute('aria-hidden', isDigital ? 'true' : 'false');
        }

        // Ajuste visual e de acessibilidade para o container de Quantidade
        if (qtyContainer) {
            qtyContainer.style.opacity = isDigital ? '0.5' : '1';
        }

        if (qtyInput) {
            if (isDigital) {
                qtyInput.value = '';
                qtyInput.disabled = true;
                qtyInput.setAttribute('aria-disabled', 'true');
            } else {
                qtyInput.disabled = false;
                qtyInput.setAttribute('aria-disabled', 'false');
                if (!qtyInput.value) qtyInput.value = 1;
            }
        }
    }

    /**
     * Busca e renderiza os atributos dinâmicos via API
     */
    function loadAttributes(typeId, isInitialLoad = false) {
        if (!typeId) {
            if (dynamicAttrContainer) dynamicAttrContainer.style.display = 'none';
            return;
        }

        if (dynamicAttrList) {
            dynamicAttrList.innerHTML = '<div class="col-12 text-muted fst-italic mb-3 px-3">Buscando especificações técnicas...</div>';
        }

        if (dynamicAttrContainer) dynamicAttrContainer.style.display = 'block';

        fetch(`/inclusive-radar/admin/resource-types/${typeId}/attributes`)
            .then(res => res.json())
            .then(data => {
                if (!dynamicAttrList) return;

                dynamicAttrList.innerHTML = '';

                if (data && data.length > 0) {
                    data.forEach(attr => {
                        const div = document.createElement('div');
                        div.className = "col-md-6 mb-3 px-4";

                        const savedValue = currentValues[attr.id] ?? '';
                        const fieldId = `attr_dynamic_${attr.id}`;

                        const labelText = attr.label;
                        const isRequired = attr.is_required;

                        // Mantemos o placeholder pois ele ajuda visualmente,
                        // mas removemos o title que gerava o balão redundante.
                        const placeholderText = isRequired
                            ? `Digite ${labelText.toLowerCase()}`
                            : `${labelText} (opcional)`;

                        div.innerHTML = `
                            <label for="${fieldId}" class="form-label fw-bold text-purple-dark">
                                ${labelText} ${isRequired ? '<span class="text-danger">*</span>' : ''}
                            </label>
                            <input type="${attr.field_type === 'integer' ? 'number' : 'text'}"
                                   name="attributes[${attr.id}]"
                                   id="${fieldId}"
                                   value="${savedValue}"
                                   ${isRequired ? 'required' : ''}
                                   class="form-control custom-input"
                                   placeholder="${placeholderText}"
                                   aria-label="${labelText}">
                        `;
                        dynamicAttrList.appendChild(div);
                    });
                } else {
                    if (dynamicAttrContainer) dynamicAttrContainer.style.display = 'none';
                }
            })
            .catch(error => {
                console.error('Erro ao carregar atributos dinâmicos:', error);
                if (dynamicAttrContainer) dynamicAttrContainer.style.display = 'none';
            });
    }

    // Event Listeners
    if (typeSelect) {
        typeSelect.addEventListener('change', (e) => {
            handleTypeChange();
            loadAttributes(e.target.value, false);
        });

        if (typeSelect.value) {
            handleTypeChange();
            loadAttributes(typeSelect.value, true);
        }
    }
});
