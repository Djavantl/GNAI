import { useEffect, type RefObject } from 'react';

interface JQueryCollection {
  parsley?: (options?: Record<string, unknown>) => unknown;
}

interface JQueryRuntime {
  (selectorOrElement: string | Element): JQueryCollection;
  fn?: {
    parsley?: unknown;
  };
}

interface FormValidationWindow extends Window {
  jQuery?: JQueryRuntime;
  $?: JQueryRuntime;
}

const REQUIRED_SCRIPTS = [
  '/assets/react/js/jquery.min.js',
  '/assets/react/plugins/parsleyjs/dist/parsley.min.js',
  '/assets/react/plugins/parsleyjs/src/i18n/pt-br.js'
];

function getJQueryRuntime(): JQueryRuntime | null {
  const runtimeWindow = window as FormValidationWindow;
  return runtimeWindow.jQuery ?? runtimeWindow.$ ?? null;
}

function loadScript(src: string): Promise<void> {
  return new Promise((resolve, reject) => {
    const existingScript = document.querySelector<HTMLScriptElement>(`script[src="${src}"]`);

    if (existingScript?.dataset.loaded === 'true') {
      resolve();
      return;
    }

    if (existingScript) {
      existingScript.addEventListener('load', () => resolve(), { once: true });
      existingScript.addEventListener('error', () => reject(new Error(`Unable to load ${src}`)), { once: true });
      return;
    }

    const script = document.createElement('script');
    script.src = src;
    script.async = false;
    script.addEventListener('load', () => {
      script.dataset.loaded = 'true';
      resolve();
    }, { once: true });
    script.addEventListener('error', () => reject(new Error(`Unable to load ${src}`)), { once: true });
    document.body.appendChild(script);
  });
}

async function ensureValidationScripts(): Promise<JQueryRuntime | null> {
  for (const scriptSrc of REQUIRED_SCRIPTS) {
    await loadScript(scriptSrc);
  }

  return getJQueryRuntime();
}

function initializeParsley(root: HTMLElement, runtime: JQueryRuntime): void {
  if (!runtime.fn?.parsley) {
    return;
  }

  root.querySelectorAll<HTMLFormElement>('form').forEach((form) => {
    if (form.dataset.formValidationReady === 'parsley') {
      return;
    }

    runtime(form).parsley?.();
    form.dataset.formValidationReady = 'parsley';
  });
}

export function useFormValidationRuntime(rootRef: RefObject<HTMLElement | null>): void {
  useEffect(() => {
    let cancelled = false;

    async function initializeValidationPlugins(): Promise<void> {
      const runtime = await ensureValidationScripts();

      if (cancelled || !runtime || !rootRef.current) {
        return;
      }

      initializeParsley(rootRef.current, runtime);
    }

    void initializeValidationPlugins();

    return () => {
      cancelled = true;
    };
  }, [rootRef]);
}
