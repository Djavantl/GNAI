import { router } from '@inertiajs/react';
import { useEffect, useRef, useState } from 'react';
import './AppLoader.css';

const fadeDurationMs = 180;
const scrollLockClass = 'app-loader-scroll-lock';

type LoaderPhase = 'hidden' | 'visible' | 'closing';

export default function AppLoader(): React.JSX.Element | null {
  const [phase, setPhase] = useState<LoaderPhase>('hidden');
  const activeVisitId = useRef<string | null>(null);
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

    const stopStartListener = router.on('start', (event) => {
      const { visit } = event.detail;
      const changesPage =
        visit.method !== 'get' ||
        visit.url.pathname !== window.location.pathname;

      if (visit.prefetch || visit.async || !changesPage) {
        return;
      }

      clearCloseTimer();
      activeVisitId.current = visit.id;
      document.documentElement.classList.add(scrollLockClass);
      document.body.classList.add(scrollLockClass);
      setPhase('visible');
    });

    const stopFinishListener = router.on('finish', (event) => {
      if (event.detail.visit.id !== activeVisitId.current) {
        return;
      }

      activeVisitId.current = null;
      setPhase('closing');
      closeTimer.current = window.setTimeout(() => {
        setPhase('hidden');
        unlockPage();
        closeTimer.current = null;
      }, fadeDurationMs);
    });

    return () => {
      stopStartListener();
      stopFinishListener();
      clearCloseTimer();
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
