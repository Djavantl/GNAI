import type { PageDefinition } from './Pages.types';
export const pages: Record<string, PageDefinition> = {
  'page-starter.html': { key: 'page-starter', kind: 'starter', standalone: false },
  'page-login.html': { key: 'page-login', kind: 'account', account: 'login', standalone: true },
  'page-login-v2.html': { key: 'page-login-v2', kind: 'account', account: 'login-v2', standalone: true },
  'page-register.html': { key: 'page-register', kind: 'account', account: 'register', standalone: true },
  'page-register-v2.html': { key: 'page-register-v2', kind: 'account', account: 'register-v2', standalone: true },
  'page-signup-signin.html': { key: 'page-signup-signin', kind: 'account', account: 'signup-signin', standalone: true },
  'page-recoverpw.html': { key: 'page-recoverpw', kind: 'account', account: 'recoverpw', standalone: true },
  'page-lock-screen.html': { key: 'page-lock-screen', kind: 'account', account: 'lock-screen', standalone: true },
  'page-400.html': { key: 'page-400', kind: 'error', code: '400', standalone: true },
  'page-403.html': { key: 'page-403', kind: 'error', code: '403', standalone: true },
  'page-404.html': { key: 'page-404', kind: 'error', code: '404', standalone: true },
  'page-404_alt.html': { key: 'page-404-alt', kind: 'error', code: '404', standalone: false },
  'page-500.html': { key: 'page-500', kind: 'error', code: '500', standalone: true },
  'page-503.html': { key: 'page-503', kind: 'error', code: '503', standalone: true },
};
export const starterCopy = { title: 'Página em branco', section: 'Páginas', brand: 'Interface' };
