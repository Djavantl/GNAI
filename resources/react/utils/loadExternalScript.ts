export function loadExternalScript(src: string): Promise<void> {
  return new Promise((resolve: () => void, reject: (reason?: unknown) => void) => {
    const existing = document.querySelector(`script[src="${src}"]`) as HTMLScriptElement | null;

    if (existing) {
      if (existing.dataset.loaded === 'true') {
        resolve();
        return;
      }

      existing.addEventListener('load', () => resolve(), { once: true });
      existing.addEventListener('error', (event: Event) => reject(event), { once: true });
      return;
    }

    const script = document.createElement('script');
    script.src = src;
    script.async = true;
    script.addEventListener(
      'load',
      () => {
        script.dataset.loaded = 'true';
        resolve();
      },
      { once: true }
    );
    script.addEventListener('error', (event: Event) => reject(event), { once: true });
    document.body.appendChild(script);
  });
}
