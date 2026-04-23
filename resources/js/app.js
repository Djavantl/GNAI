import './bootstrap';
import './pages/messages.js';
import './pages/inclusive-radar/type-attributes.js';
import './pages/specialized-educational-support/session.js';
import './components/search-filter.js';
import './components/confirm-action-modal.js';
import './utils/cpf.js';
import './utils/phone.js';
import './components/collapsible-section';
import './components/highContrast';

// App principal - Sidebar, Navbar e Dropdowns
class App {
    constructor() {
        this.init();
    }

    init() {
        this.initSidebar();
        this.initActiveMenu();
        this.initNavbarScrollHide(); // Novo: esconde navbar no scroll mobile
        // Não chame initDropdowns manualmente – o Bootstrap já faz isso
    }

    // ==================== SIDEBAR ====================
    initSidebar() {
        this.sidebar = document.querySelector('.sidebar');
        this.toggleBtn = document.querySelector('#sidebarToggle');
        this.overlay = null;

        if (!this.sidebar) return;

        // Botão hambúrguer
        if (this.toggleBtn) {
            this.toggleBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.toggleSidebar();
            });
        }

        this.createOverlayElement();

        // Fecha ao clicar no overlay
        if (this.overlay) {
            this.overlay.addEventListener('click', () => this.closeSidebar());
        }

        // Fecha ao clicar fora (mobile)
        document.addEventListener('click', (e) => {
            const isMobile = window.innerWidth <= 865;
            if (!isMobile) return;

            const isOpen = this.sidebar.classList.contains('show');
            const insideSidebar = this.sidebar.contains(e.target);
            const clickedToggle = this.toggleBtn?.contains(e.target);

            if (isOpen && !insideSidebar && !clickedToggle) {
                this.closeSidebar();
            }
        });

        this.handleResize();
        window.addEventListener('resize', () => this.handleResize());
    }

// ==================== OVERLAY ====================
    createOverlayElement() {
        let overlay = document.querySelector('.sidebar-overlay');

        if (!overlay) {
            overlay = document.createElement('div');
            overlay.className = 'sidebar-overlay';
            document.body.appendChild(overlay);
        }

        this.overlay = overlay;
        this.overlay.classList.remove('show');
        this.overlay.style.display = 'none';
    }

// ==================== TOGGLE ====================
    toggleSidebar() {
        const isMobile = window.innerWidth <= 865;

        // 🔥 DESKTOP
        if (!isMobile) {
            document.body.classList.toggle('sidebar-collapsed');
            return;
        }

        // 🔥 MOBILE
        const isOpen = this.sidebar.classList.contains('show');

        if (!isOpen) {
            this.openSidebarMobile();
        } else {
            this.closeSidebar();
        }
    }

// ==================== OPEN MOBILE ====================
    openSidebarMobile() {
        this.sidebar.classList.add('show');

        if (this.overlay) {
            this.overlay.style.display = 'block';
            this.overlay.offsetHeight; // força reflow
            this.overlay.classList.add('show');
        }

        document.body.classList.add('sidebar-mobile-open');
        document.body.style.overflow = 'hidden';
    }

// ==================== CLOSE ====================
    closeSidebar() {
        this.sidebar.classList.remove('show');

        if (this.overlay) {
            this.overlay.classList.remove('show');

            setTimeout(() => {
                if (!this.sidebar.classList.contains('show')) {
                    this.overlay.style.display = 'none';
                }
            }, 300);
        }

        document.body.classList.remove('sidebar-mobile-open');
        document.body.style.overflow = '';
    }

// ==================== RESIZE ====================
    handleResize() {
        const isMobile = window.innerWidth <= 865;

        if (!isMobile) {
            // Saiu do mobile → limpa estado mobile
            this.closeSidebar();
        } else {
            // Entrou no mobile → remove estado desktop
            document.body.classList.remove('sidebar-collapsed');
        }
    }

    // ==================== MENU ATIVO ====================
    initActiveMenu() {
        const currentPath = window.location.pathname;
        const menuItems = document.querySelectorAll('.sidebar-menu a');

        menuItems.forEach(item => {
            const href = item.getAttribute('href');
            if (href && href !== '#' && currentPath.includes(href.replace(/^\//, ''))) {
                item.classList.add('active');
                // Expande grupo se existir
                const parentGroup = item.closest('.menu-group');
                if (parentGroup) parentGroup.classList.add('expanded');
            }
        });
    }

    // ==================== NAVBAR SCROLL HIDE (mobile) ====================
    initNavbarScrollHide() {
        const navbar = document.querySelector('.navbar-custom');
        if (!navbar) return;

        let lastScroll = 0;
        let ticking = false;
        const isMobile = () => window.innerWidth <= 865;

        const handleScroll = () => {
            if (!isMobile()) {
                navbar.classList.remove('navbar-hidden');
                return;
            }

            // Se algum dropdown do Bootstrap estiver aberto, não esconde a navbar
            const openDropdown = document.querySelector('.dropdown-menu.show');
            if (openDropdown) {
                navbar.classList.remove('navbar-hidden');
                lastScroll = window.scrollY;
                return;
            }

            const currentScroll = window.scrollY;
            if (currentScroll > lastScroll && currentScroll > 10) {
                navbar.classList.add('navbar-hidden');
            } else {
                navbar.classList.remove('navbar-hidden');
            }
            lastScroll = currentScroll;
        };

        window.addEventListener('scroll', () => {
            if (!ticking) {
                requestAnimationFrame(() => {
                    handleScroll();
                    ticking = false;
                });
                ticking = true;
            }
        });

        // Ao abrir/fechar dropdowns do Bootstrap, reavalia o estado da navbar
        document.querySelectorAll('.dropdown-toggle').forEach(btn => {
            btn.addEventListener('shown.bs.dropdown', () => {
                navbar.classList.remove('navbar-hidden');
            });
            btn.addEventListener('hidden.bs.dropdown', () => {
                handleScroll(); // reaparece se estiver no topo
            });
        });
    }
}

// ==================== NOTIFICAÇÕES (leitura assíncrona) ====================
document.addEventListener('click', function(e) {
    const item = e.target.closest('.notify-item');
    if (!item) return;

    e.preventDefault();
    const id = item.dataset.id;
    const url = item.getAttribute('href');

    if (id) {
        axios.post(`/notifications/${id}/read`)
            .then(() => {
                // Atualiza contador
                const badge = document.getElementById('notif-count');
                if (badge) {
                    let count = parseInt(badge.textContent);
                    if (!isNaN(count) && count > 0) {
                        count--;
                        if (count === 0) badge.remove();
                        else badge.textContent = count;
                    }
                }
                if (url && url !== '#') window.location.href = url;
            })
            .catch(() => {
                if (url && url !== '#') window.location.href = url;
            });
    } else {
        if (url && url !== '#') window.location.href = url;
    }
});

// ==================== INICIALIZAÇÃO ====================
document.addEventListener('DOMContentLoaded', () => {
    window.app = new App();

    // Ano atual no footer
    const yearElement = document.querySelector('[data-year]');
    if (yearElement) yearElement.textContent = new Date().getFullYear();

    // Bootstrap tooltips & popovers
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(el => new bootstrap.Tooltip(el));

    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(el => new bootstrap.Popover(el));
});

// Função global para compatibilidade com onclick (caso exista)
window.toggleSidebar = () => {
    if (window.app) window.app.toggleSidebar();
};
