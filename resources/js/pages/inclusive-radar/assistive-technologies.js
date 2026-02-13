document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('type_id');
    const dynamicAttrContainer = document.getElementById('dynamic-attributes-container');
    const dynamicAttrList = document.getElementById('dynamic-attributes');
    const assetContainer = document.getElementById('asset_code_container');
    const qtyContainer = document.getElementById('quantity_container');
    const qtyInput = document.getElementById('quantity_input');

    const currentValues = window.currentAttributeValues || {};

    function handleTypeChange() {
        if (!typeSelect || typeSelect.selectedIndex === -1) return;

        const selected = typeSelect.options[typeSelect.selectedIndex];
        if (!selected) return;

        const isDigital = selected.dataset.digital === '1';

        if (assetContainer) assetContainer.style.opacity = isDigital ? '0.5' : '1';
        if (qtyContainer) qtyContainer.style.opacity = isDigital ? '0.5' : '1';

        if (qtyInput) {
            if (isDigital) {
                qtyInput.value = '';
                qtyInput.disabled = true;
            } else {
                qtyInput.disabled = false;
                if (!qtyInput.value) qtyInput.value = 1;
            }
        }
    }

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

                        div.innerHTML = `
                            <label class="form-label fw-bold text-purple-dark">
                                ${attr.label} ${attr.is_required ? '*' : ''}
                            </label>
                            <input type="${attr.field_type === 'integer' ? 'number' : 'text'}"
                                   name="attributes[${attr.id}]"
                                   value="${savedValue}"
                                   ${attr.is_required ? 'required' : ''}
                                   class="form-control custom-input"
                                   placeholder="Digite ${attr.label.toLowerCase()}">
                        `;
                        dynamicAttrList.appendChild(div);
                    });
                } else {
                    dynamicAttrContainer.style.display = 'none';
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                if (dynamicAttrContainer) dynamicAttrContainer.style.display = 'none';
            });
    }

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
