import { router } from '@inertiajs/react';
import { useEffect, useRef, useState } from 'react';
import type { ActiveVisit, PendingVisit } from '@inertiajs/core';
import './AppLoader.css';

const fadeDurationMs = 180;
const scrollLockClass = 'app-loader-scroll-lock';

type LoaderPhase = 'hidden' | 'visible' | 'closing';

export default function AppLoader(): React.JSX.Element | null {
  const [phase, setPhase] = useState<LoaderPhase>('hidden');
  const activeVisits = useRef(new Set<PendingVisit>());
  const closeTimer = useRef<number | null>(null);

  useEffect(() => {
    function clearCloseTimer(): void {
      if (closeTimer.current === null) {
        return;
      }

      window.clearTimeout(closeTimer.current);
      closeTimer.current = null;
    }

    function unlockPage(): void {
      document.documentElement.classList.remove(scrollLockClass);
      document.body.classList.remove(scrollLockClass);
    }

    function closeLoader(): void {
      activeVisits.current.clear();
      setPhase('closing');
      clearCloseTimer();
      closeTimer.current = window.setTimeout(() => {
        setPhase('hidden');
        unlockPage();
        closeTimer.current = null;
      }, fadeDurationMs);
    }

    const stopStartListener = router.on('start', (event) => {
      const { visit } = event.detail;
      const changesPage =
        visit.method !== 'get' ||
        visit.url.pathname !== window.location.pathname;

      if (visit.prefetch || visit.async || !changesPage) {
        return;
      }

      clearCloseTimer();
      activeVisits.current.add(visit);
      document.documentElement.classList.add(scrollLockClass);
      document.body.classList.add(scrollLockClass);
      setPhase('visible');
    });

    const stopFinishListener = router.on('finish', (event) => {
      const visit = event.detail.visit as ActiveVisit;

      if (!activeVisits.current.delete(visit)) {
        return;
      }

      if (activeVisits.current.size === 0) {
        closeLoader();
      }
    });

    const stopCancelListener = router.on('cancel', () => {
      if (activeVisits.current.size > 0) {
        closeLoader();
      }
    });

    return () => {
      stopStartListener();
      stopFinishListener();
      stopCancelListener();
      clearCloseTimer();
      activeVisits.current.clear();
      unlockPage();
    };
  }, []);

  if (phase === 'hidden') {
    return null;
  }

  return (
    <div
      className={`animationload appLoader${phase === 'closing' ? ' appLoader--hidden' : ''}`}
      role="status"
      aria-live="polite"
      aria-label="Carregando página"
    >
      <div className="loader" aria-hidden="true" />
    </div>
  );
}
