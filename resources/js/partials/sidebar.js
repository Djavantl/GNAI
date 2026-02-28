const body = document.body;
const sidebar = document.querySelector('.sidebar');
const toggleBtn = document.getElementById('sidebarToggle');

let isAnimating = false;
const ANIMATION_TIME = 300;

/* ===== BOTÃO HAMBURGUER ===== */
toggleBtn.addEventListener('click', () => {
    if (isAnimating) return;

    isAnimating = true;
    sidebar.classList.add('animating');

    body.classList.toggle('sidebar-collapsed');

    setTimeout(() => {
        sidebar.classList.remove('animating');
        isAnimating = false;
    }, ANIMATION_TIME);
});

/* ===== HOVER EXPANDE TEMPORARIAMENTE ===== */
sidebar.addEventListener('mouseenter', () => {
    if (!body.classList.contains('sidebar-collapsed')) return;
    if (isAnimating) return;

    sidebar.classList.add('hover-open');
    body.classList.remove('sidebar-collapsed');
});

sidebar.addEventListener('mouseleave', () => {
    if (!sidebar.classList.contains('hover-open')) return;

    sidebar.classList.remove('hover-open');
    body.classList.add('sidebar-collapsed');
});

const STORAGE_KEY = 'sidebar-scroll-position';

/* ===== SALVAR POSIÇÃO ===== */
function saveSidebarScroll() {
    if (!sidebar) return;
    localStorage.setItem(STORAGE_KEY, sidebar.scrollTop);
}

/* salva ao clicar em links internos */
document.querySelectorAll('.sidebar a').forEach(link => {
    link.addEventListener('click', saveSidebarScroll);
});

/* salva ao sair da página (reload, navegação etc.) */
window.addEventListener('beforeunload', saveSidebarScroll);


/* ===== RESTAURAR POSIÇÃO ===== */
window.addEventListener('load', () => {
    if (!sidebar) return;

    const saved = localStorage.getItem(STORAGE_KEY);
    if (!saved) return;

    const target = parseInt(saved, 10);

    /* rolagem suave até a posição */
    smoothScrollSidebar(target, 400);
});


/* ===== FUNÇÃO DE ROLAGEM SUAVE ===== */
function smoothScrollSidebar(target, duration) {
    const start = sidebar.scrollTop;
    const change = target - start;
    const startTime = performance.now();

    function animateScroll(currentTime) {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);

        /* easing suave */
        const ease = progress < 0.5
            ? 2 * progress * progress
            : 1 - Math.pow(-2 * progress + 2, 2) / 2;

        sidebar.scrollTop = start + change * ease;

        if (progress < 1) {
            requestAnimationFrame(animateScroll);
        }
    }

    requestAnimationFrame(animateScroll);
}