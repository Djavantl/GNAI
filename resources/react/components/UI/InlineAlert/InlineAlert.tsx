import { useEffect, useState } from 'react';
import { joinClassNames } from '@/utils/joinClassNames';
import type { AlertVariant } from './InlineAlert.types';
import './InlineAlert.css';

const alertFadeDurationMs = 150;

export default function InlineAlert({
  children,
  className,
  closeButton = false,
  dismissible = false,
  fade = false,
  variant
}: {
  children: React.ReactNode;
  className?: string;
  closeButton?: boolean;
  dismissible?: boolean;
  fade?: boolean;
  variant: AlertVariant;
}): React.JSX.Element | null {
  const [mounted, setMounted] = useState<boolean>(true);
  const [shown, setShown] = useState<boolean>(true);

  useEffect(() => {
    if (!fade || shown || !mounted) {
      return undefined;
    }

    const timeout = window.setTimeout(() => setMounted(false), alertFadeDurationMs + 30);
    return () => window.clearTimeout(timeout);
  }, [fade, mounted, shown]);

  if (!mounted) {
    return null;
  }

  const closeAlert = (): void => {
    if (fade) {
      setShown(false);
      return;
    }

    setMounted(false);
  };

  return (
    <div
      className={joinClassNames(
        'inlineAlert',
        `inlineAlert--${variant}`,
        dismissible ? 'inlineAlert--dismissable' : undefined,
        fade ? 'inlineAlert--fade' : undefined,
        fade && shown ? 'inlineAlert--in' : undefined,
        className
      )}
      onTransitionEnd={(event) => {
        if (fade && !shown && event.target === event.currentTarget) {
          setMounted(false);
        }
      }}
    >
      {(dismissible || closeButton) ? (
        <button type="button" className={joinClassNames('inlineAlert__close', closeButton && !dismissible ? 'inlineAlert__close--plain' : undefined)} aria-label="Fechar" onClick={closeAlert}>
          ×
        </button>
      ) : null}
      {children}
    </div>
  );
}

