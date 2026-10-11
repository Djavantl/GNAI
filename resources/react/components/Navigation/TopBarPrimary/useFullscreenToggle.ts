import { useEffect, useState } from 'react';

export function useFullscreenToggle(): { isFullscreen: boolean; toggleFullscreen: () => Promise<void> } {
  const [isFullscreen, setIsFullscreen] = useState<boolean>(false);

  useEffect(() => {
    function handleFullscreenChange(): void {
      setIsFullscreen(Boolean(document.fullscreenElement));
    }

    document.addEventListener('fullscreenchange', handleFullscreenChange);
    handleFullscreenChange();

    return () => {
      document.removeEventListener('fullscreenchange', handleFullscreenChange);
    };
  }, []);

  async function toggleFullscreen(): Promise<void> {
    if (!document.fullscreenElement) {
      await document.documentElement.requestFullscreen?.();
      return;
    }

    await document.exitFullscreen?.();
  }

  return { isFullscreen, toggleFullscreen };
}
