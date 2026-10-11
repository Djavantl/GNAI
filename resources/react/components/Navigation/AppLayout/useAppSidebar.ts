import { useEffect, useState } from 'react';

const storageKey = 'gnai.sidebarCollapsed';

function savedPreference(): boolean | undefined {
  try {
    const value = window.localStorage.getItem(storageKey);
    return value === 'true' ? true : value === 'false' ? false : undefined;
  } catch {
    return undefined;
  }
}

function isMobile(): boolean {
  return window.matchMedia('(max-width: 767px)').matches;
}

function initialCollapsed(preference: boolean | undefined): boolean {
  return isMobile() || (preference ?? window.matchMedia('(max-width: 990px)').matches);
}

export function useAppSidebar(url: string): { collapsed: boolean; toggle: () => void; closeOnMobile: () => void } {
  const [preference, setPreference] = useState(savedPreference);
  const [collapsed, setCollapsed] = useState(() => initialCollapsed(preference));

  useEffect(() => {
    const media = window.matchMedia('(max-width: 990px)');
    const mobile = window.matchMedia('(max-width: 767px)');
    const resize = (): void => setCollapsed(initialCollapsed(preference));
    media.addEventListener('change', resize);
    mobile.addEventListener('change', resize);
    return () => {
      media.removeEventListener('change', resize);
      mobile.removeEventListener('change', resize);
    };
  }, [preference]);

  const closeOnMobile = (): void => {
    if (isMobile()) setCollapsed(true);
  };
  useEffect(() => { closeOnMobile(); }, [url]);
  useEffect(() => {
    const escape = (event: KeyboardEvent): void => {
      if (event.key === 'Escape') closeOnMobile();
    };
    document.addEventListener('keydown', escape);
    return () => document.removeEventListener('keydown', escape);
  }, []);

  function toggle(): void {
    const next = !collapsed;
    setCollapsed(next);
    // The mobile drawer is temporary and must not overwrite the desktop preference.
    if (!isMobile()) {
      setPreference(next);
      try {
        window.localStorage.setItem(storageKey, String(next));
      } catch {
        // Keep the menu usable when browser storage is unavailable.
      }
    }
  }

  return { collapsed, toggle, closeOnMobile };
}
