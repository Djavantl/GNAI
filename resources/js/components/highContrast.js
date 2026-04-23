const HC_KEY = 'gnai-high-contrast';
const HC_CLASS = 'high-contrast';

function applyHighContrast(enabled) {
    document.body.classList.toggle(HC_CLASS, enabled);
    const btn = document.getElementById('highContrastToggle');
    if (btn) {
        btn.setAttribute('aria-pressed', String(enabled));
        btn.title = enabled ? 'Desativar alto contraste' : 'Ativar alto contraste';
        btn.setAttribute('aria-label', enabled ? 'Desativar alto contraste' : 'Ativar alto contraste');
    }
}

function initHighContrast() {
    const saved = localStorage.getItem(HC_KEY) === 'true';
    applyHighContrast(saved);

    const btn = document.getElementById('highContrastToggle');
    if (!btn) return;

    btn.addEventListener('click', () => {
        const enabled = !document.body.classList.contains(HC_CLASS);
        localStorage.setItem(HC_KEY, String(enabled));
        applyHighContrast(enabled);
    });
}

document.addEventListener('DOMContentLoaded', initHighContrast);
