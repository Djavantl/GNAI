import { useEffect, useRef } from 'react';
import { loadExternalScript } from '@/utils/loadExternalScript';
import type { VectorMapProps } from './VectorMap.types';
import './VectorMap.css';

interface VectorMapInstance {
  updateSize: () => void;
  remove: () => void;
}
interface VectorMapRuntime {
  jQuery: (element: HTMLElement) => unknown;
  jvm: { Map: new (options: Record<string, unknown>) => VectorMapInstance };
}

export default function VectorMap({
  map,
  source,
  color,
  height = 400,
  markers,
  markerStyle
}: VectorMapProps): JSX.Element {
  const ref = useRef<HTMLDivElement>(null);
  useEffect(() => {
    const element = ref.current;
    if (!element) return;
    let disposed = false;
    let instance: VectorMapInstance | undefined;
    let observer: ResizeObserver | undefined;
    void (async (): Promise<void> => {
      await loadExternalScript('/assets/gnai/js/jquery.min.js');
      await loadExternalScript('/assets/gnai/plugins/jvectormap/jquery-jvectormap-2.0.2.min.js');
      await loadExternalScript(`/assets/gnai/plugins/jvectormap/${source}`);
      if (disposed) return;
      const runtime = window as unknown as VectorMapRuntime;
      instance = new runtime.jvm.Map({
        container: runtime.jQuery(element),
        map,
        backgroundColor: 'transparent',
        regionStyle: { initial: { fill: color } },
        ...(markers
          ? {
              markers,
              markerStyle,
              scaleColors: ['#ea6c9c', '#ea6c9c'],
              normalizeFunction: 'polynomial',
              hoverOpacity: 0.7,
              hoverColor: false
            }
          : {})
      });
      observer = new ResizeObserver((): void => instance?.updateSize());
      observer.observe(element);
      element.dataset.mapReady = 'true';
    })().catch((error: unknown): void => {
      if (!disposed) console.error('Vector map initialization failed', error);
    });
    return (): void => {
      disposed = true;
      observer?.disconnect();
      instance?.remove();
      delete element.dataset.mapReady;
    };
  }, [map, source, color, markers, markerStyle]);
  return <div ref={ref} className="vectorMap" style={{ height }} />;
}
