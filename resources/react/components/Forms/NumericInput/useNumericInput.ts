import { useEffect, useRef, type RefObject } from 'react';
import { loadExternalScript } from '@/utils/loadExternalScript';
import type { NumericFormat } from './NumericInput.types';

interface NumericCollection {
  autoNumeric: (method: string, options?: Record<string, string | number>) => unknown;
  on: (events: string, handler: () => void) => NumericCollection;
  off: (events: string) => NumericCollection;
}
interface NumericRuntime { (element: HTMLInputElement): NumericCollection; }

export function useNumericInput(ref: RefObject<HTMLInputElement | null>, format: NumericFormat, onValueChange?: (value: string, rawValue: string) => void): void {
  const callback = useRef(onValueChange);
  callback.current = onValueChange;
  const { groupSeparator = ',', decimalSeparator = '.', currencySymbol = '', symbolPosition = 'prefix', groupSize = 3, min = -9999999999999.99, max = 9999999999999.99, negativeBrackets = false } = format;
  useEffect(() => {
    let cancelled = false;
    let collection: NumericCollection | undefined;
    async function initialize(): Promise<void> {
      await loadExternalScript('/assets/react/js/jquery.min.js');
      await loadExternalScript('/assets/react/plugins/autoNumeric/autoNumeric.js');
      if (cancelled || !ref.current) return;
      const input = ref.current;
      const runtime = (window as Window & { jQuery?: NumericRuntime }).jQuery;
      if (!runtime) throw new Error('jQuery indisponível.');
      collection = runtime(input);
      collection.autoNumeric('init', { aSep: groupSeparator, aDec: decimalSeparator, aSign: currencySymbol, pSign: symbolPosition === 'suffix' ? 's' : 'p', dGroup: groupSize, vMin: String(min), vMax: String(max), ...(negativeBrackets ? { nBracket: '(,)' } : {}) });
      collection.on('keyup.numericInput change.numericInput blur.numericInput', () => callback.current?.(input.value, String(collection?.autoNumeric('get') ?? '')));
    }
    void initialize().catch((error: unknown) => console.error('Não foi possível carregar o formatador numérico.', error));
    return () => {
      cancelled = true;
      collection?.off('.numericInput');
      collection?.autoNumeric('destroy');
    };
  }, [ref, groupSeparator, decimalSeparator, currencySymbol, symbolPosition, groupSize, min, max, negativeBrackets]);
}
