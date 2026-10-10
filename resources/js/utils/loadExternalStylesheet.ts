export function loadExternalStylesheet(href: string): Promise<void> {
  return new Promise((resolve: () => void, reject: (reason?: unknown) => void) => {
    const existing = document.querySelector(`link[href="${href}"]`) as HTMLLinkElement | null;

    if (existing) {
      if (existing.dataset.loaded === 'true' || existing.sheet) {
        resolve();
        return;
      }

      existing.addEventListener('load', () => resolve(), { once: true });
      existing.addEventListener('error', (event: Event) => reject(event), { once: true });
      return;
    }

    const link = document.createElement('link');
    link.href = href;
    link.rel = 'stylesheet';
    link.addEventListener(
      'load',
      () => {
        link.dataset.loaded = 'true';
        resolve();
      },
      { once: true }
    );
    link.addEventListener('error', (event: Event) => reject(event), { once: true });
    document.head.appendChild(link);
  });
}
