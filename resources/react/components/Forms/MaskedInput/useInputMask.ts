import { useEffect, useRef, type RefObject } from 'react';
import { loadExternalScript } from '@/utils/loadExternalScript';

interface MaskInstance {
  tests: Array<RegExp | null>;
  buffer: string[];
  options: { placeholder: string };
}
interface MaskCollection {
  on: (events: string, handler: () => void) => MaskCollection;
  off: (events: string) => MaskCollection;
  trigger: (event: string) => MaskCollection;
  data: (name: 'inputmask') => MaskInstance | undefined;
  removeData: (name: string) => MaskCollection;
}
interface MaskRuntime { (element: HTMLInputElement): MaskCollection; }

export function useInputMask(ref: RefObject<HTMLInputElement | null>, mask: string, onValueChange?: (value: string, rawValue: string) => void): void {
  const callback = useRef(onValueChange);
  callback.current = onValueChange;
  useEffect(() => {
    let cancelled = false;
    let collection: MaskCollection | undefined;
    let timer: ReturnType<typeof setTimeout> | undefined;
    async function initialize(): Promise<void> {
      await loadExternalScript('/assets/react/js/jquery.min.js');
      await loadExternalScript('/assets/react/js/detect.js');
      await loadExternalScript('/assets/react/plugins/bootstrap-inputmask/bootstrap-inputmask.min.js');
      if (cancelled || !ref.current) return;
      const input = ref.current;
      const runtime = (window as Window & { jQuery?: MaskRuntime }).jQuery;
      if (!runtime) throw new Error('jQuery indisponível.');
      collection = runtime(input);
      // Keep the original lazy initialization on first focus via data-mask.
      collection.on('change.maskedInput input.maskedInput keyup.maskedInput', () => {
        clearTimeout(timer);
        timer = setTimeout(() => {
          const instance = collection?.data('inputmask');
          const raw = instance?.buffer.filter((character, index) => instance.tests[index] && character !== instance.options.placeholder).join('');
          callback.current?.(input.value, raw ?? input.value);
        }, 0);
      });
    }
    void initialize().catch((error: unknown) => console.error('Não foi possível carregar a máscara de entrada.', error));
    return () => {
      cancelled = true;
      clearTimeout(timer);
      collection?.trigger('unmask').off('.maskedInput').off('unmask').removeData('rawMaskFn');
      // jQuery caches data attributes; remove the old pattern when the prop changes.
      collection?.removeData('mask');
    };
  }, [ref, mask]);
}
