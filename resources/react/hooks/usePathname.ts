import { useEffect, useState } from 'react';

export function usePathname(): string {
  const [pathname, setPathname] = useState<string>(() => window.location.pathname);

  useEffect(() => {
    const handleNavigationChange = (): void => setPathname(window.location.pathname);
    window.addEventListener('popstate', handleNavigationChange);
    return () => window.removeEventListener('popstate', handleNavigationChange);
  }, []);

  return pathname;
}
