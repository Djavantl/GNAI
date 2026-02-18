import './bootstrap';
import './pages/messages.js';
import './pages/inclusive-radar/type-attributes.js';

// App principal - Sidebar e Navbar
class App {
    constructor() {
        this.init();
    }

    init() {
        this.initSidebar();
        this.initActiveMenu();
        this.initMobileBehavior();
        this.initDropdowns();
    }

    // Inicializar sidebar
    initSidebar() {
        this.sidebar = document.querySelector('.sidebar');
        this.navbarToggler = document.querySelector('.navbar-toggler');

        if (this.navbarToggler) {
            this.navbarToggler.addEventListener('click', () => this.toggleSidebar());
        }

        // Fechar sidebar ao clicar fora (em mobile)
        document.addEventListener('click', (e) => {
            if (window.innerWidth < 769 &&
                this.sidebar.classList.contains('show') &&
                !this.sidebar.contains(e.target) &&
                !this.navbarToggler.contains(e.target)) {
                this.sidebar.classList.remove('show');
            }
        });
    }

    // Alternar sidebar (mobile)
    toggleSidebar() {
        this.sidebar.classList.toggle('show');

        // Adicionar overlay em mobile
        if (window.innerWidth < 769) {
            if (this.sidebar.classList.contains('show')) {
                this.addOverlay();
            } else {
                this.removeOverlay();
            }
        }
    }

    // Adicionar overlay em mobile
    addOverlay() {
        if (!document.querySelector('.sidebar-overlay')) {
            const overlay = document.createElement('div');
            overlay.className = 'sidebar-overlay';
            overlay.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0,0,0,0.5);
                z-index: 999;
                display: block;
            `;
            overlay.addEventListener('click', () => this.toggleSidebar());
            document.body.appendChild(overlay);
            document.body.style.overflow = 'hidden';
        }
    }

    // Remover overlay
    removeOverlay() {
        const overlay = document.querySelector('.sidebar-overlay');
        if (overlay) {
            overlay.remove();
            document.body.style.overflow = '';
        }
    }

    // Marcar item ativo no menu
    initActiveMenu() {
        const currentPath = window.location.pathname;
        const menuItems = document.querySelectorAll('.sidebar-menu a');

        menuItems.forEach(item => {
            const href = item.getAttribute('href');
            if (href && currentPath.includes(href.replace('/', ''))) {
                item.classList.add('active');

                // Expandir grupo se existir
                const parentGroup = item.closest('.menu-group');
                if (parentGroup) {
                    parentGroup.classList.add('expanded');
                }
            }
        });
    }

    // // Comportamento mobile
    // initMobileBehavior() {
    //     // Fechar sidebar ao clicar em um link em mobile
    //     if (window.innerWidth < 769) {
    //         const menuLinks = document.querySelectorAll('.sidebar-menu a');
    //         menuLinks.forEach(link => {
    //             link.addEventListener('click', () => {
    //                 this.sidebar.classList.remove('show');
    //                 this.removeOverlay();
    //             });
    //         });
    //     }
    //
    //     // Ajustar altura do conteúdo
    //     this.adjustContentHeight();
    //     window.addEventListener('resize', () => this.adjustContentHeight());
    // }

    // // Ajustar altura do conteúdo
    // adjustContentHeight() {
    //     const navbarHeight = document.querySelector('.navbar-custom').offsetHeight;
    //     const mainContent = document.querySelector('.main-content');
    //
    //     if (mainContent) {
    //         if (window.innerWidth >= 769) {
    //             mainContent.style.marginTop = navbarHeight + 'px';
    //         } else {
    //             mainContent.style.marginTop = '0';
    //         }
    //     }
    // }

    // Inicializar dropdowns
    initDropdowns() {
        const dropdowns = document.querySelectorAll('.dropdown-toggle');
        dropdowns.forEach(dropdown => {
            dropdown.addEventListener('click', function(e) {
                if (window.innerWidth < 769) {
                    e.preventDefault();
                    const menu = this.nextElementSibling;
                    menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
                }
            });
        });

        // Fechar dropdowns ao clicar fora
        document.addEventListener('click', (e) => {
            if (!e.target.matches('.dropdown-toggle')) {
                document.querySelectorAll('.dropdown-menu').forEach(menu => {
                    if (window.innerWidth < 769) {
                        menu.style.display = 'none';
                    }
                });
            }
        });
    }

    // Método para mostrar notificações
    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} alert-dismissible fade show`;
        notification.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        const container = document.querySelector('.main-content');
        if (container) {
            container.insertBefore(notification, container.firstChild);

            // Auto-remover após 5 segundos
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 5000);
        }
    }
}

// Inicializar app quando o DOM estiver carregado
document.addEventListener('DOMContentLoaded', () => {
    window.app = new App();

    // Adicionar ano atual no footer se existir
    const yearElement = document.querySelector('[data-year]');
    if (yearElement) {
        yearElement.textContent = new Date().getFullYear();
    }

    // Tooltips do Bootstrap
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Popovers do Bootstrap
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
});

// Funções globais
function toggleSidebar() {
    if (window.app) {
        window.app.toggleSidebar();
    }
}

// Exportar para uso global
window.toggleSidebar = toggleSidebar;
