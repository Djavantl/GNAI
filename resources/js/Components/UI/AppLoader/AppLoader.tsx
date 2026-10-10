import { useEffect } from 'react';
import { appPageTransitionReadyEvent, appPageTransitionStartEvent } from '@/utils/appPageTransition';
import './AppLoader.css';

const loaderFadeDelayMs = 300;
const overlayFadeDelayMs = 600;
const overlayFadeMs = 600;
const loaderScrollLockClass = 'app-loader-scroll-lock';
const initialLoaderId = 'initial-app-loader';
const routeLoaderId = 'route-app-loader';

declare global {
  interface Window {
    __initialAppLoader?: {
      markContentReady: () => void;
      complete: () => void;
    };
  }
}

function createLoaderElement(id: string = initialLoaderId): HTMLDivElement {
  const overlay = document.createElement('div');
  overlay.id = id;
  overlay.className = 'animationload initial-app-loader';
  overlay.setAttribute('aria-hidden', 'true');

  const loader = document.createElement('div');
  loader.className = 'loader';
  overlay.appendChild(loader);
  document.body.prepend(overlay);

  return overlay;
}

function addScrollLock(): void {
  document.documentElement.classList.add(loaderScrollLockClass);
  document.body.classList.add(loaderScrollLockClass);
}

function removeScrollLock(): void {
  if (document.getElementById(initialLoaderId) || document.getElementById(routeLoaderId)) return;
  document.documentElement.classList.remove(loaderScrollLockClass);
  document.body.classList.remove(loaderScrollLockClass);
}

export default function AppLoader(): null {
  useEffect(() => {
    let routeOverlay: HTMLDivElement | null = null;
    let routeStartedAt = 0;
    let routeReady = false;
    let routeClosing = false;
    let routeScrollLocked = false;
    let initialScrollLocked = false;
    let fallbackInitialOverlay: HTMLDivElement | null = null;
    const routeTimers: number[] = [];
    const initialTimers: number[] = [];

    function clearTimers(timers: number[]): void {
      while (timers.length > 0) {
        const timer = timers.pop();

        if (timer !== undefined) {
          window.clearTimeout(timer);
        }
      }
    }

    function completeRouteLoader(): void {
      const shouldRemoveScrollLock = routeScrollLocked;
      routeOverlay?.remove();
      routeOverlay = null;
      routeReady = false;
      routeClosing = false;
      routeScrollLocked = false;
      clearTimers(routeTimers);

      if (shouldRemoveScrollLock) {
        removeScrollLock();
      }
    }

    function closeRouteLoaderWhenReady(): void {
      if (!routeOverlay || !routeReady || routeClosing) {
        return;
      }

      routeClosing = true;
      const closingOverlay = routeOverlay;
      void document.fonts.ready.then(() => {
        if (routeOverlay !== closingOverlay) return;
        const elapsed = performance.now() - routeStartedAt;
        const fadeDelay = Math.max(0, overlayFadeDelayMs - elapsed);

        routeTimers.push(window.setTimeout(() => {
          closingOverlay.classList.add('appLoader--hidden');
          routeTimers.push(window.setTimeout(completeRouteLoader, overlayFadeMs));
        }, fadeDelay));
      });
    }

    function startRouteLoader(): void {
      completeRouteLoader();

      routeOverlay = createLoaderElement(routeLoaderId);
      routeStartedAt = performance.now();
      routeReady = false;
      routeClosing = false;
      addScrollLock();
      routeScrollLocked = true;
      routeOverlay.classList.add('appLoader', 'appLoader--route');

      const loader = routeOverlay.querySelector('.loader');
      routeTimers.push(window.setTimeout(() => {
        loader?.classList.add('appLoader__loader--hidden');
      }, loaderFadeDelayMs));
    }

    function markRouteReady(): void {
      routeReady = true;
      closeRouteLoaderWhenReady();
    }

    if (window.__initialAppLoader) {
      window.__initialAppLoader.markContentReady();
    } else {
      const overlay = createLoaderElement(initialLoaderId);
      fallbackInitialOverlay = overlay;
      const loader = overlay.querySelector('.loader');

      addScrollLock();
      initialScrollLocked = true;
      overlay.classList.add('appLoader');
      const startedAt = performance.now();

      function completeInitialLoader(): void {
        overlay.remove();
        fallbackInitialOverlay = null;
        if (initialScrollLocked) {
          removeScrollLock();
          initialScrollLocked = false;
        }
      }

      initialTimers.push(window.setTimeout(() => {
        loader?.classList.add('appLoader__loader--hidden');
      }, loaderFadeDelayMs));

      void document.fonts.ready.then(() => {
        if (!initialScrollLocked) return;
        const fadeDelay = Math.max(0, overlayFadeDelayMs - (performance.now() - startedAt));
        initialTimers.push(window.setTimeout(() => {
          overlay.classList.add('appLoader--hidden');
          initialTimers.push(window.setTimeout(completeInitialLoader, overlayFadeMs));
        }, fadeDelay));
      });
    }

    window.addEventListener(appPageTransitionStartEvent, startRouteLoader);
    window.addEventListener(appPageTransitionReadyEvent, markRouteReady);

    return () => {
      window.removeEventListener(appPageTransitionStartEvent, startRouteLoader);
      window.removeEventListener(appPageTransitionReadyEvent, markRouteReady);
      clearTimers(initialTimers);
      completeRouteLoader();

      if (initialScrollLocked) {
        fallbackInitialOverlay?.remove();
        removeScrollLock();
        initialScrollLocked = false;
      }
    };
  }, []);

  return null;
}
