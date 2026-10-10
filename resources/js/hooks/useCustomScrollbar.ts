import { useEffect, useRef, useState } from 'react';

interface CustomScrollbarState<T extends HTMLElement> {
  scrollRef: React.RefObject<T>;
  thumbHeight: number;
  thumbTop: number;
  isVisible: boolean;
}

export function useCustomScrollbar<T extends HTMLElement>(isActive: boolean = true): CustomScrollbarState<T> {
  const scrollRef = useRef<T>(null);
  const hideTimerRef = useRef<number | null>(null);
  const [thumbHeight, setThumbHeight] = useState<number>(0);
  const [thumbTop, setThumbTop] = useState<number>(0);
  const [isVisible, setIsVisible] = useState<boolean>(false);

  useEffect(() => {
    const scrollElement = scrollRef.current;

    if (!scrollElement || !isActive) {
      setIsVisible(false);
      return;
    }

    function clearHideTimer(): void {
      if (hideTimerRef.current !== null) {
        window.clearTimeout(hideTimerRef.current);
        hideTimerRef.current = null;
      }
    }

    function scheduleHide(): void {
      clearHideTimer();
      hideTimerRef.current = window.setTimeout(() => {
        setIsVisible(false);
      }, 850);
    }

    const updateScrollbar = (): void => {
      const { clientHeight, scrollHeight, scrollTop } = scrollElement;

      if (scrollHeight <= clientHeight) {
        setThumbHeight(0);
        setThumbTop(0);
        setIsVisible(false);
        return;
      }

      const nextThumbHeight = Math.max((clientHeight / scrollHeight) * clientHeight, 24);
      const maxThumbTop = clientHeight - nextThumbHeight;
      const nextThumbTop = maxThumbTop * (scrollTop / (scrollHeight - clientHeight));

      setThumbHeight(nextThumbHeight);
      setThumbTop(nextThumbTop);
    };

    function handleScroll(): void {
      updateScrollbar();
      setIsVisible(true);
      scheduleHide();
    }

    const handleMouseEnter = (): void => {
      updateScrollbar();

      if (scrollElement.scrollHeight > scrollElement.clientHeight) {
        setIsVisible(true);
      }
    };

    function handleMouseLeave(): void {
      scheduleHide();
    }

    updateScrollbar();

    scrollElement.addEventListener('scroll', handleScroll, { passive: true });
    scrollElement.addEventListener('mouseenter', handleMouseEnter);
    scrollElement.addEventListener('mouseleave', handleMouseLeave);
    window.addEventListener('resize', updateScrollbar);

    return () => {
      clearHideTimer();
      scrollElement.removeEventListener('scroll', handleScroll);
      scrollElement.removeEventListener('mouseenter', handleMouseEnter);
      scrollElement.removeEventListener('mouseleave', handleMouseLeave);
      window.removeEventListener('resize', updateScrollbar);
    };
  }, [isActive]);

  return { scrollRef, thumbHeight, thumbTop, isVisible };
}
