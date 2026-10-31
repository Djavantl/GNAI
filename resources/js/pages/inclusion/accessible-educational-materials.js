// Lógica dos Campos Digitais (Sua lógica original intacta)
function toggleAssetCodeField() {
    const select = document.querySelector('[name="is_digital"]');
    if (!select) return;

    const isDigital = select.value == "1";
    const assetCodeContainer = document.getElementById('asset_code_container');
    const assetCodeInput = assetCodeContainer?.querySelector('input[name="asset_code"]');

    if (isDigital) {
        if (assetCodeContainer) assetCodeContainer.style.display = 'none';
        if (assetCodeInput) assetCodeInput.value = '';
    } else {
        if (assetCodeContainer) assetCodeContainer.style.display = 'block';
    }
}

// Lógica de Redirecionamento da Vistoria
function initInspectionRedirects() {
    const timeline = document.querySelector('.history-timeline');
    if (!timeline) return;

    // Clique no Card
    timeline.onclick = (e) => {
        const card = e.target.closest('.cursor-pointer');
        if (card && card.dataset.url) {
            window.location.href = card.dataset.url;
        }
    };

    // Acessibilidade via Teclado (Enter ou Espaço)
    timeline.onkeydown = (e) => {
        const card = e.target.closest('.cursor-pointer');
        if (card && (e.key === 'Enter' || e.key === ' ')) {
            e.preventDefault();
            window.location.href = card.dataset.url;
        }
    };
}

// Inicialização Geral
document.addEventListener('DOMContentLoaded', function () {
    // Inicializa campos digitais
    toggleAssetCodeField();
    const selectDigital = document.querySelector('[name="is_digital"]');
    if (selectDigital) {
        selectDigital.addEventListener('change', toggleAssetCodeField);
    }

    // Inicializa redirecionamentos de vistoria
    initInspectionRedirects();
});
