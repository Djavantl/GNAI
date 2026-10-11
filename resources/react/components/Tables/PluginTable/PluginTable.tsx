import { useEffect, useRef, type ReactNode } from 'react';
import { renderToStaticMarkup } from 'react-dom/server';
import { loadExternalScript } from '@/utils/loadExternalScript';
import type { TableInitializer, TableRuntime } from './PluginTable.types';
import './PluginTable.base.css';
import './PluginTable.css';
interface PluginTableProps {
  children: ReactNode;
  scripts: readonly string[];
  initialize: TableInitializer;
  className?: string;
}
export default function PluginTable({
  children,
  scripts,
  initialize,
  className = '',
}: PluginTableProps): React.JSX.Element {
  const host = useRef<HTMLDivElement>(null);
  const markup = renderToStaticMarkup(<>{children}</>);
  useEffect(() => {
    let disposed = false;
    let cleanup: (() => void) | undefined;
    const element = host.current;
    async function mount(): Promise<void> {
      for (const src of [
        '/assets/react/js/jquery.min.js',
        '/assets/react/js/bootstrap.min.js',
        ...scripts,
      ])
        await loadExternalScript(src);
      if (disposed || !element) return;
      // The engine owns this DOM subtree; React owns its host and lifecycle.
      element.innerHTML = markup;
      const table = element.querySelector('table');
      if (table) {
        const runtime = (window as unknown as Window & { jQuery: TableRuntime })
          .jQuery;
        type Handler = {
          handler: (...args: unknown[]) => unknown;
          namespace?: string;
        };
        const events = (target: Window | Document): Record<string, Handler[]> =>
          (
            runtime as unknown as {
              _data: (
                target: unknown,
                key: string,
              ) => Record<string, Handler[]>;
            }
          )._data(target, 'events') ?? {};
        const targets = [window, document];
        const before = targets.map(
          (target) =>
            new Set(
              Object.values(events(target))
                .flat()
                .map((item) => item.handler),
            ),
        );
        const dispose = initialize(runtime, element, table);
        const added = targets.map((target, index) =>
          Object.entries(events(target)).flatMap(([type, handlers]) =>
            handlers
              .filter((item) => !before[index].has(item.handler))
              .map((item) => ({ type, ...item })),
          ),
        );
        cleanup = () => {
          dispose?.();
          targets.forEach((target, index) =>
            added[index].forEach((item) =>
              runtime(target).off(
                item.type + (item.namespace ? '.' + item.namespace : ''),
                item.handler,
              ),
            ),
          );
        };
      }
    }
    void mount().catch(console.error);
    return () => {
      disposed = true;
      cleanup?.();
      if (element) element.replaceChildren();
    };
  }, [markup, scripts, initialize]);
  return <div className={`tableSurface ${className}`} ref={host} />;
}
