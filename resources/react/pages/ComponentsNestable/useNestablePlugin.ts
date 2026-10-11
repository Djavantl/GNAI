import { useEffect, type RefObject } from 'react';
import { loadExternalScript } from '@/utils/loadExternalScript';

interface NestableCollection {
  nestable(options?: Record<string, unknown> | string): unknown;
}

interface NestableRuntime {
  (element: Element): NestableCollection;
}

interface NestableWindow extends Window {
  jQuery?: NestableRuntime;
  $?: NestableRuntime;
}

export interface NestablePluginHandle {
  collapseAll: () => void;
  expandAll: () => void;
}

export function getNestableRuntime(): NestableRuntime | null {
  const runtimeWindow = window as NestableWindow;
  return runtimeWindow.jQuery ?? runtimeWindow.$ ?? null;
}

export function useNestablePlugin(rootRef: RefObject<HTMLDivElement | null>, options?: { group?: number }): void {
  useEffect(() => {
    let cancelled = false;

    async function mountNestable(): Promise<void> {
      await loadExternalScript('/assets/react/js/jquery.min.js');
      await loadExternalScript('/assets/react/plugins/nestable/jquery.nestable.js');

      if (cancelled || !rootRef.current) {
        return;
      }

      const runtime = getNestableRuntime();

      if (!runtime) {
        return;
      }

      runtime(rootRef.current).nestable(options?.group === undefined ? undefined : { group: options.group });
    }

    void mountNestable();

    return () => {
      cancelled = true;
    };
  }, [options?.group, rootRef]);
}
