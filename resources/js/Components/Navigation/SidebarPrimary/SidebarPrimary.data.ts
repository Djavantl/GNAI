import type { SidebarEntry } from './SidebarPrimary.types';
import { multiLevelMenu } from './SidebarPrimary.multilevel.data';

export const sidebarEntries: SidebarEntry[] = [
  { key: 'navigation-title', title: 'Navegação' },
  {
    key: 'dashboard',
    label: 'Dashboard',
    href: '#',
    iconClass: 'ti-home',
    children: [
      { key: 'dashboard-1', label: 'Dashboard 1', href: 'index.html' },
      { key: 'dashboard-2', label: 'Dashboard 2', href: 'dashboard_2.html' },
      { key: 'dashboard-3', label: 'Dashboard 3', href: 'dashboard_3.html' }
    ]
  },
  {
    key: 'ui-kit',
    label: 'UI Kit',
    href: '#',
    iconClass: 'ti-paint-bucket',
    children: [
      { key: 'ui-buttons', label: 'Botões', href: 'ui-buttons.html' },
      { key: 'ui-panels', label: 'Painéis', href: 'ui-panels.html' },
      { key: 'ui-portlets', label: 'Portlets', href: 'ui-portlets.html' },
      { key: 'ui-checkbox-radio', label: 'Checkboxes e rádios', href: 'ui-checkbox-radio.html' },
      { key: 'ui-tabs', label: 'Tabs', href: 'ui-tabs.html' },
      { key: 'ui-modals', label: 'Modais', href: 'ui-modals.html' },
      { key: 'ui-progressbars', label: 'Barras de progresso', href: 'ui-progressbars.html' },
      { key: 'ui-notification', label: 'Notificações', href: 'ui-notification.html' },
      { key: 'ui-images', label: 'Imagens', href: 'ui-images.html' },
      { key: 'ui-carousel', label: 'Carousel', href: 'ui-carousel.html' },
      { key: 'ui-bootstrap', label: 'Interface Bootstrap', href: 'ui-bootstrap.html' },
      { key: 'ui-typography', label: 'Tipografia', href: 'ui-typography.html' }
    ]
  },
  {
    key: 'components',
    label: 'Componentes',
    href: '#',
    iconClass: 'ti-light-bulb',
    labelBadge: { text: '6', variant: 'primary' },
    children: [
      { key: 'components-grid', label: 'Grade', href: 'components-grid.html' },
      { key: 'components-widgets', label: 'Widgets', href: 'components-widgets.html' },
      { key: 'components-nestable', label: 'Lista aninhável', href: 'components-nestable-list.html' },
      { key: 'components-range-sliders', label: 'Sliders de intervalo', href: 'components-range-sliders.html' },
      { key: 'components-animation', label: 'Animação', href: 'components-animation.html' },
      { key: 'components-sweet-alert', label: 'Alertas', href: 'components-sweet-alert.html' }
    ]
  },
  {
    key: 'icons',
    label: 'Ícones',
    href: '#',
    iconClass: 'ti-spray',
    children: [
      { key: 'icons-glyphicons', label: 'Glyphicons', href: 'icons-glyphicons.html' },
      { key: 'icons-materialdesign', label: 'Material Design', href: 'icons-materialdesign.html' },
      { key: 'icons-ionicons', label: 'Ícones Ion', href: 'icons-ionicons.html' },
      { key: 'icons-fontawesome', label: 'Font Awesome', href: 'icons-fontawesome.html' },
      { key: 'icons-themifyicon', label: 'Ícones Themify', href: 'icons-themifyicon.html' },
      { key: 'icons-simple-line', label: 'Ícones Simple Line', href: 'icons-simple-line.html' },
      { key: 'icons-weather', label: 'Ícones do clima', href: 'icons-weather.html' }
    ]
  },
  {
    key: 'forms',
    label: 'Formulários',
    href: '#',
    iconClass: 'ti-pencil-alt',
    children: [
      { key: 'form-elements', label: 'Elementos gerais', href: 'form-elements.html' },
      { key: 'form-advanced', label: 'Formulário avançado', href: 'form-advanced.html' },
      { key: 'form-validation', label: 'Validação de formulário', href: 'form-validation.html' },
      { key: 'form-pickers', label: 'Seletores', href: 'form-pickers.html' },
      { key: 'form-wizard', label: 'Assistente de formulário', href: 'form-wizard.html' },
      { key: 'form-mask', label: 'Máscaras de formulário', href: 'form-mask.html' },
      { key: 'form-summernote', label: 'Summernote', href: 'form-summernote.html' },
      { key: 'form-wysiwig', label: 'Editores Wysiwig', href: 'form-wysiwig.html' },
      { key: 'form-uploads', label: 'Upload múltiplo de arquivos', href: 'form-uploads.html' },
      { key: 'form-xeditable', label: 'X-editable', href: 'form-xeditable.html' },
      { key: 'form-image-crop', label: 'Recorte de imagem', href: 'form-image-crop.html' }
    ]
  },
  {
    key: 'tables',
    label: 'Tabelas',
    href: '#',
    iconClass: 'ti-menu-alt',
    children: [
      { key: 'tables-basic', label: 'Tabelas básicas', href: 'tables-basic.html' },
      { key: 'tables-datatable', label: 'Tabela de dados', href: 'tables-datatable.html' },
      { key: 'tables-editable', label: 'Tabela editável', href: 'tables-editable.html' },
      { key: 'tables-responsive', label: 'Tabela responsiva', href: 'tables-responsive.html' },
      { key: 'tables-foo-tables', label: 'FooTable', href: 'tables-foo-tables.html' },
      { key: 'tables-bootstrap', label: 'Tabelas Bootstrap', href: 'tables-bootstrap.html' }
    ]
  },
  {
    key: 'charts',
    label: 'Gráficos',
    href: '#',
    iconClass: 'ti-bar-chart',
    labelBadge: { text: '10', variant: 'pink' },
    children: [
      { key: 'chart-flot', label: 'Gráfico Flot', href: 'chart-flot.html' },
      { key: 'chart-morris', label: 'Gráfico Morris', href: 'chart-morris.html' },
      { key: 'chart-chartjs', label: 'Chartjs', href: 'chart-chartjs.html' },
      { key: 'chart-peity', label: 'Gráficos Peity', href: 'chart-peity.html' },
      { key: 'chart-chartist', label: 'Gráficos Chartist', href: 'chart-chartist.html' },
      { key: 'chart-c3', label: 'Gráficos C3', href: 'chart-c3.html' },
      { key: 'chart-nvd3', label: 'Gráficos Nvd3', href: 'chart-nvd3.html' },
      { key: 'chart-sparkline', label: 'Gráficos Sparkline', href: 'chart-sparkline.html' },
      { key: 'chart-radial', label: 'Gráficos radiais', href: 'chart-radial.html' },
      { key: 'chart-other', label: 'Outros gráficos', href: 'chart-other.html' }
    ]
  },
  {
    key: 'maps',
    label: 'Mapas',
    href: '#',
    iconClass: 'ti-location-pin',
    children: [
      { key: 'map-google', label: 'Mapa Google', href: 'map-google.html' },
      { key: 'map-vector', label: 'Mapa vetorial', href: 'map-vector.html' }
    ]
  },
  { key: 'more-title', title: 'Mais' },
  {
    key: 'pages',
    label: 'Páginas',
    href: '#',
    iconClass: 'ti-files',
    children: [
      { key: 'page-starter', label: 'Página inicial', href: 'page-starter.html' },
      { key: 'page-login', label: 'Login', href: 'page-login.html' },
      { key: 'page-login-v2', label: 'Login v2', href: 'page-login-v2.html' },
      { key: 'page-register', label: 'Cadastro', href: 'page-register.html' },
      { key: 'page-register-v2', label: 'Cadastro v2', href: 'page-register-v2.html' },
      { key: 'page-signup-signin', label: 'Entrar - Cadastrar', href: 'page-signup-signin.html' },
      { key: 'page-recoverpw', label: 'Recuperar senha', href: 'page-recoverpw.html' },
      { key: 'page-lock-screen', label: 'Tela de bloqueio', href: 'page-lock-screen.html' },
      { key: 'page-400', label: 'Erro 400', href: 'page-400.html' },
      { key: 'page-403', label: 'Erro 403', href: 'page-403.html' },
      { key: 'page-404', label: 'Erro 404', href: 'page-404.html' },
      { key: 'page-404-alt', label: 'Erro 404-alt', href: 'page-404_alt.html' },
      { key: 'page-500', label: 'Erro 500', href: 'page-500.html' },
      { key: 'page-503', label: 'Erro 503', href: 'page-503.html' }
    ]
  },
  {
    key: 'extras',
    label: 'Extras',
    href: '#',
    iconClass: 'ti-gift',
    children: [
      { key: 'extra-profile', label: 'Perfil', href: 'extra-profile.html' },
      { key: 'extra-timeline', label: 'Linha do tempo', href: 'extra-timeline.html' },
      { key: 'extra-sitemap', label: 'Mapa do site', href: 'extra-sitemap.html' },
      { key: 'extra-invoice', label: 'Fatura', href: 'extra-invoice.html' },
      { key: 'extra-email-template', label: 'Modelo de e-mail', href: 'extra-email-template.html' },
      { key: 'extra-maintenance', label: 'Manutenção', href: 'extra-maintenance.html' },
      { key: 'extra-coming-soon', label: 'Em breve', href: 'extra-coming-soon.html' },
      { key: 'extra-faq', label: 'FAQ', href: 'extra-faq.html' },
      { key: 'extra-search-result', label: 'Resultado de busca', href: 'extra-search-result.html' },
      { key: 'extra-gallery', label: 'Galeria', href: 'extra-gallery.html' },
      { key: 'extra-pricing', label: 'Preços', href: 'extra-pricing.html' }
    ]
  },
  {
    key: 'apps',
    label: 'Aplicativos',
    href: '#',
    iconClass: 'ti-crown',
    labelBadge: { text: '3', variant: 'success' },
    children: [
      { key: 'apps-inbox', label: 'E-mail', href: 'apps-inbox.html' },
      { key: 'apps-calendar', label: 'Calendário', href: 'apps-calendar.html' },
      { key: 'apps-contact', label: 'Contato', href: 'apps-contact.html' }
    ]
  },
  {
    key: 'layouts',
    label: 'Layouts',
    href: '#',
    iconClass: 'ti-widget',
    children: [
      { key: 'layout-leftbar-2', label: 'Barra lateral com usuário', href: 'layout-leftbar_2.html' },
      { key: 'layout-menu-collapsed', label: 'Menu recolhido', href: 'layout-menu-collapsed.html' },
      { key: 'layout-menu-small', label: 'Menu pequeno', href: 'layout-menu-small.html' },
      { key: 'layout-header-2', label: 'Estilo do cabeçalho', href: 'layout-header_2.html' }
    ]
  },
  multiLevelMenu,
  { key: 'extra-title', title: 'Extra' },
  {
    key: 'crm',
    label: 'CRM',
    href: '#',
    iconClass: 'ti-user',
    children: [
      { key: 'crm-dashboard', label: 'Dashboard', href: 'crm-dashboard.html' },
      { key: 'crm-contact', label: 'Contatos', href: 'crm-contact.html' },
      { key: 'crm-opportunities', label: 'Oportunidades', href: 'crm-opportunities.html' },
      { key: 'crm-leads', label: 'Leads', href: 'crm-leads.html' },
      { key: 'crm-customers', label: 'Clientes', href: 'crm-customers.html' }
    ]
  },
  {
    key: 'ecommerce',
    label: 'eCommerce',
    href: '#',
    iconClass: 'ti-shopping-cart',
    children: [
      { key: 'ecommerce-dashboard', label: 'Dashboard', href: 'ecommerce-dashboard.html' },
      { key: 'ecommerce-products', label: 'Produtos', href: 'ecommerce-products.html' },
      { key: 'ecommerce-product-detail', label: 'Detalhe do produto', href: 'ecommerce-product-detail.html' },
      { key: 'ecommerce-orders', label: 'Pedidos', href: 'ecommerce-orders.html' },
      { key: 'ecommerce-sellers', label: 'Vendedores', href: 'ecommerce-sellers.html' }
    ]
  }
];
