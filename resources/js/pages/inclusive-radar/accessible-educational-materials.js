function toggleAssetCodeField() {
    const select = document.querySelector('[name="is_digital"]');
    const isDigital = select.value == "1";

    const assetCodeContainer = document.getElementById('asset_code_container');
    const assetCodeInput = assetCodeContainer?.querySelector('input[name="asset_code"]');

    if (isDigital) {
        // Esconde o campo e limpa o valor
        if (assetCodeContainer) assetCodeContainer.style.display = 'none';
        if (assetCodeInput) assetCodeInput.value = '';
    } else {
        // Mostra o campo
        if (assetCodeContainer) assetCodeContainer.style.display = 'block';
    }
}

document.addEventListener('DOMContentLoaded', function () {
    toggleAssetCodeField();

    const select = document.querySelector('[name="is_digital"]');
    if (select) select.addEventListener('change', toggleAssetCodeField);
});
