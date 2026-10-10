import { useEffect, type RefObject } from 'react';
import { loadExternalScript } from '@/utils/loadExternalScript';
import { loadExternalStylesheet } from '@/utils/loadExternalStylesheet';
import type { IonRangeSliderItem } from './ComponentsRangeSliders.types';

interface JQueryCollection {
  ionRangeSlider?: (options?: Record<string, unknown>) => JQueryCollection;
  slider?: (options?: Record<string, unknown> | string) => JQueryCollection;
  data: (key: string) => unknown;
  each: (callback: (index: number, element: Element) => void) => JQueryCollection;
}

interface JQueryRuntime {
  (element: Element | string): JQueryCollection;
  fn?: {
    ionRangeSlider?: unknown;
    slider?: unknown;
  };
}

interface SliderWindow extends Window {
  jQuery?: JQueryRuntime;
  $?: JQueryRuntime;
}

const rangeSliderStylesheets: string[] = [
  '/assets/gnai/plugins/ion-rangeslider/ion.rangeSlider.css',
  '/assets/gnai/plugins/ion-rangeslider/ion.rangeSlider.skinFlat.css',
  '/assets/gnai/plugins/seiyria-bootstrap-slider/dist/css/bootstrap-slider.min.css'
];

function getJQueryRuntime(): JQueryRuntime | null {
  const runtimeWindow = window as SliderWindow;
  return runtimeWindow.jQuery ?? runtimeWindow.$ ?? null;
}

function parseBootstrapSliderValue(value: number | [number, number]): string {
  return Array.isArray(value) ? `[${value[0]},${value[1]}]` : String(value);
}

export function getBootstrapSliderInputAttributes(slider: {
  min: number;
  max: number;
  value: number | [number, number];
  orientation?: 'horizontal' | 'vertical';
  selection?: 'before' | 'after' | 'none';
  step?: number;
  tooltip?: 'show' | 'hide' | 'always';
}): Record<string, string | number> {
  const attributes: Record<string, string | number> = {
    'data-plugin': 'range-slider',
    'data-slider-orientation': slider.orientation ?? 'horizontal',
    'data-slider-min': slider.min,
    'data-slider-max': slider.max,
    'data-slider-value': parseBootstrapSliderValue(slider.value)
  };

  if (slider.selection) {
    attributes['data-slider-selection'] = slider.selection;
  }

  if (slider.step !== undefined) {
    attributes['data-slider-step'] = slider.step;
  }

  if (slider.tooltip) {
    attributes['data-slider-tooltip'] = slider.tooltip;
  }

  return attributes;
}

export function useRangeSlidersRuntime(rootRef: RefObject<HTMLElement>, ionSliders: IonRangeSliderItem[]): void {
  useEffect(() => {
    let cancelled = false;

    async function initializeSliders(): Promise<void> {
      await Promise.all(rangeSliderStylesheets.map((href: string) => loadExternalStylesheet(href)));
      await loadExternalScript('/assets/gnai/js/jquery.min.js');
      await loadExternalScript('/assets/gnai/plugins/ion-rangeslider/ion.rangeSlider.min.js');
      await loadExternalScript('/assets/gnai/plugins/seiyria-bootstrap-slider/dist/bootstrap-slider.min.js');

      if (cancelled || !rootRef.current) {
        return;
      }

      const runtime = getJQueryRuntime();

      if (!runtime) {
        return;
      }

      ionSliders.forEach((slider: IonRangeSliderItem) => {
        const input = rootRef.current?.querySelector(`#${slider.id}`);

        if (input && runtime.fn?.ionRangeSlider && !runtime(input).data('ionRangeSlider')) {
          runtime(input).ionRangeSlider?.(slider.options);
        }
      });

      if (runtime.fn?.slider) {
        rootRef.current.querySelectorAll('[data-plugin="range-slider"]').forEach((input: Element) => {
          const collection = runtime(input);

          if (!collection.data('slider')) {
            collection.slider?.({});
          }
        });
      }
    }

    void initializeSliders();

    return () => {
      cancelled = true;

      const runtime = getJQueryRuntime();

      if (!runtime || !rootRef.current) {
        return;
      }

      ionSliders.forEach((slider: IonRangeSliderItem) => {
        const input = rootRef.current?.querySelector(`#${slider.id}`);
        const instance = input ? runtime(input).data('ionRangeSlider') : null;

        if (instance && typeof (instance as { destroy?: () => void }).destroy === 'function') {
          (instance as { destroy: () => void }).destroy();
        }
      });

      if (runtime.fn?.slider) {
        rootRef.current.querySelectorAll('[data-plugin="range-slider"]').forEach((input: Element) => {
          const collection = runtime(input);

          if (collection.data('slider')) {
            try {
              collection.slider?.('destroy');
            } catch {
              // The legacy plugin may throw if a partial initialization was already removed by React strict mode.
            }
          }
        });
      }
    };
  }, [ionSliders, rootRef]);
}
