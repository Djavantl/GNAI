import { useEffect, useId, useRef } from 'react';
import type { GoogleMapProps } from './GoogleMap.types';
import { loadGoogleMaps, mountGoogleMap } from './GoogleMap.runtime';
import './GoogleMap.css';

export default function GoogleMap({ variant = 'basic', center, polygon, apiKey }: GoogleMapProps): JSX.Element {
  const id = useId().replace(/:/g, '');
  const ref = useRef<HTMLDivElement>(null);
  useEffect(() => {
    const element = ref.current;
    if (!element) return;
    let disposed = false;
    let map: ReturnType<typeof mountGoogleMap> | undefined;
    let observer: ResizeObserver | undefined;
    void loadGoogleMaps(apiKey)
      .then((): void => {
        if (disposed) return;
        map = mountGoogleMap(element, { variant, center, polygon });
        let width = element.clientWidth;
        observer = new ResizeObserver((): void => {
          if (width !== element.clientWidth) {
            width = element.clientWidth;
            map?.resize();
          }
        });
        observer.observe(element);
        element.dataset.mapReady = 'true';
      })
      .catch((error: unknown): void => {
        if (!disposed) console.error('Google map initialization failed', error);
      });
    return (): void => {
      disposed = true;
      observer?.disconnect();
      map?.destroy();
      element.replaceChildren();
      delete element.dataset.mapReady;
    };
  }, [variant, center, polygon, apiKey]);
  return <div id={`google-map-${id}`} ref={ref} className="googleMap" />;
}
