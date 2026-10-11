import { createContext, useContext, useEffect, useLayoutEffect, useRef, useState, type MouseEvent, type ReactNode } from 'react';
import { createPortal, flushSync } from 'react-dom';
import { getCustomboxCloseDelay, getCustomboxPagePlaneName, isCustomboxPagePlaneEffect } from './Modal.runtime';
import './Modal.css';

const ModalCloseContext = createContext<(() => void) | null>(null);

export function useModalClose(): (() => void) | null {
  return useContext(ModalCloseContext);
}

export type ModalSize = 'default' | 'large' | 'small' | 'full' | 'custom';
export type ModalEffect =
  | 'fadein' | 'slide' | 'newspaper' | 'fall' | 'sidefall' | 'blur' | 'flip' | 'sign'
  | 'superscaled' | 'slit' | 'rotate' | 'letmein' | 'makeway' | 'slip' | 'corner'
  | 'slidetogether' | 'scale' | 'door' | 'push' | 'contentscale' | 'swell' | 'rotatedown' | 'flash';

interface ModalProps {
  open: boolean;
  title?: ReactNode;
  children: ReactNode;
  footer?: ReactNode;
  size?: ModalSize;
  customWidth?: string;
  effect?: ModalEffect;
  onClose: () => void;
  className?: string;
  layerClassName?: string;
  contentClassName?: string;
  bodyClassName?: string;
  bootstrap?: boolean;
  closeOnBackdrop?: boolean;
}

export default function Modal({ open, title, children, footer, size = 'default', customWidth, effect, onClose, className, layerClassName, contentClassName, bodyClassName, bootstrap = false, closeOnBackdrop = true }: ModalProps): React.JSX.Element | null {
  const isCustombox = !bootstrap && effect !== undefined;
  const customboxPagePlaneName = getCustomboxPagePlaneName(effect);
  const closeDelay = isCustombox ? getCustomboxCloseDelay(effect) : 300;
  const [isMounted, setIsMounted] = useState<boolean>(open);
  const [isClosing, setIsClosing] = useState<boolean>(false);
  const [isBackdropVisible, setIsBackdropVisible] = useState<boolean>(open);
  const [isDialogShown, setIsDialogShown] = useState<boolean>(open);
  const [isDialogVisible, setIsDialogVisible] = useState<boolean>(open);
  const timersRef = useRef<number[]>([]);
  const framesRef = useRef<number[]>([]);
  const closingRef = useRef<boolean>(false);
  const onCloseRef = useRef<() => void>(onClose);
  const backdropRef = useRef<HTMLDivElement | null>(null);
  const dialogRef = useRef<HTMLDivElement | null>(null);
  const dialogMouseDownRef = useRef<boolean>(false);
  const ignoreBackdropClickRef = useRef<boolean>(false);

  const clearTimers = (): void => {
    timersRef.current.forEach((timer) => window.clearTimeout(timer));
    timersRef.current = [];
    framesRef.current.forEach((frame) => window.cancelAnimationFrame(frame));
    framesRef.current = [];
  };

  const schedule = (callback: () => void, delay: number): void => {
    const timer = window.setTimeout(callback, delay);
    timersRef.current.push(timer);
  };

  const scheduleFrame = (callback: () => void): void => {
    const frame = window.requestAnimationFrame(callback);
    framesRef.current.push(frame);
  };

  useEffect(() => {
    onCloseRef.current = onClose;
  }, [onClose]);

  useEffect(() => {
    if (open) {
      clearTimers();
      closingRef.current = false;
      setIsMounted(true);
      setIsClosing(false);
      setIsBackdropVisible(false);
      setIsDialogShown(!bootstrap);
      setIsDialogVisible(false);

      if (bootstrap) {
        scheduleFrame(() => {
          backdropRef.current?.offsetWidth;
          setIsBackdropVisible(true);
        });
        schedule(() => {
          flushSync(() => setIsDialogShown(true));
          dialogRef.current?.offsetWidth;
          setIsDialogVisible(true);
        }, 150);
      } else if (effect === 'contentscale') {
        scheduleFrame(() => {
          const appPage = document.querySelector<HTMLElement>('.app-page');
          backdropRef.current?.offsetWidth;
          dialogRef.current?.offsetWidth;
          appPage?.offsetWidth;
          flushSync(() => {
            setIsBackdropVisible(true);
            setIsDialogShown(true);
            setIsDialogVisible(true);
          });
          appPage?.classList.add('app-page--customboxContentScaleOpen');
        });
      } else {
        schedule(() => {
          setIsBackdropVisible(true);
          setIsDialogShown(true);
          setIsDialogVisible(true);
        }, 0);
      }

      return undefined;
    }

    if (!isMounted) return undefined;
    if (closingRef.current) return undefined;
    closingRef.current = true;
    setIsClosing(true);
    setIsDialogVisible(false);

    if (bootstrap) {
      schedule(() => {
        setIsDialogShown(false);
        setIsBackdropVisible(false);
      }, 150);
      schedule(() => {
        setIsMounted(false);
        setIsClosing(false);
        closingRef.current = false;
      }, 300);
    } else {
      schedule(() => {
        setIsMounted(false);
        setIsClosing(false);
        closingRef.current = false;
      }, closeDelay);
    }

    return undefined;
  }, [open, bootstrap, closeDelay, effect]);

  useEffect(() => {
    return () => clearTimers();
  }, []);

  const requestClose = (): void => {
    if (isClosing || closingRef.current) return;
    clearTimers();
    closingRef.current = true;
    setIsClosing(true);
    setIsDialogVisible(false);

    if (bootstrap) {
      schedule(() => {
        setIsDialogShown(false);
        setIsBackdropVisible(false);
      }, 150);
      schedule(() => {
        setIsMounted(false);
        setIsClosing(false);
        closingRef.current = false;
        onCloseRef.current();
      }, 300);
      return;
    }

    schedule(() => {
      setIsMounted(false);
      setIsClosing(false);
      closingRef.current = false;
      onCloseRef.current();
    }, closeDelay);
  };

  useEffect(() => {
    if (!isMounted) return undefined;

    const previousOverflow = document.body.style.overflow;
    document.body.style.overflow = 'hidden';

    return () => {
      document.body.style.overflow = previousOverflow;
    };
  }, [isMounted]);

  useEffect(() => {
    if (!isMounted || isClosing) return undefined;

    const handleKeyDown = (event: KeyboardEvent): void => {
      if (event.key === 'Escape') requestClose();
    };
    document.addEventListener('keydown', handleKeyDown);

    return () => {
      document.removeEventListener('keydown', handleKeyDown);
    };
  }, [isMounted, isClosing]);

  useLayoutEffect(() => {
    if (!isMounted || bootstrap || !effect || !isCustomboxPagePlaneEffect(effect) || customboxPagePlaneName === null) return undefined;

    const appPage = document.querySelector<HTMLElement>('.app-page');
    const sidebarInner = document.querySelector<HTMLElement>('.sidebarPrimary__inner');
    const root = document.getElementById('root');
    const appPageParent = appPage?.parentElement;
    if (!appPage || !root || !appPageParent) return undefined;

    const scrollingElement = document.scrollingElement ?? document.documentElement;
    const currentScroll = window.scrollY || scrollingElement.scrollTop || document.body.scrollTop || 0;
    const currentSidebarScroll = sidebarInner?.scrollTop ?? 0;
    const appHeight = Math.max(appPage.scrollHeight, document.documentElement.scrollHeight, window.innerHeight);
    const spacer = document.createElement('div');
    const appPageEffectClass = `app-page--custombox${customboxPagePlaneName}`;
    const appPageOpenClass = `${appPageEffectClass}Open`;
    const appPageClosingClass = `${appPageEffectClass}Closing`;
    const rootEffectClass = `root--custombox${customboxPagePlaneName}`;
    let openingFrame: number | undefined;
    let pageAnimation: Animation | undefined;

    spacer.className = `modalLayer__customboxPageSpacer modalLayer__${effect}Spacer`;
    spacer.style.height = `${appHeight}px`;
    spacer.setAttribute('aria-hidden', 'true');

    appPageParent.insertBefore(spacer, appPage);
    root.classList.add('root--customboxPagePlane', rootEffectClass);
    root.style.setProperty('--modal-custombox-page-scroll-y', `${currentScroll}px`);
    appPage.style.setProperty('--modal-custombox-page-scroll-y', `${currentScroll}px`);
    appPage.classList.add('app-page--customboxPagePlane');
    appPage.classList.add(appPageEffectClass);
    if (effect === 'push') {
      void appPage.offsetWidth;
      openingFrame = window.requestAnimationFrame(() => {
        pageAnimation = appPage.animate(
          [{ transform: 'translateX(0)' }, { transform: 'translateX(calc(100% - 15px))' }],
          { duration: 500, easing: 'ease', fill: 'forwards' },
        );
      });
    }
    if (effect === 'contentscale') document.body.classList.add('custombox-open-contentscale');

    return () => {
      if (openingFrame !== undefined) window.cancelAnimationFrame(openingFrame);
      pageAnimation?.cancel();
      appPage.classList.remove('app-page--customboxPagePlane', appPageEffectClass, appPageOpenClass, appPageClosingClass);
      appPage.style.removeProperty('--modal-custombox-page-scroll-y');
      root.classList.remove('root--customboxPagePlane', rootEffectClass);
      root.style.removeProperty('--modal-custombox-page-scroll-y');
      document.body.classList.remove('custombox-open-contentscale');
      spacer.remove();
      // Restore after the spacer and fixed page have left the layout. Restoring
      // in the same layout pass can make the browser clamp the scroll position.
      window.requestAnimationFrame(() => {
        window.scrollTo({ left: 0, top: currentScroll, behavior: 'auto' });
        scrollingElement.scrollTop = currentScroll;
        document.body.scrollTop = currentScroll;
        if (sidebarInner) sidebarInner.scrollTop = currentSidebarScroll;
        window.requestAnimationFrame(() => {
          window.scrollTo({ left: 0, top: currentScroll, behavior: 'auto' });
          scrollingElement.scrollTop = currentScroll;
          if (sidebarInner) sidebarInner.scrollTop = currentSidebarScroll;
        });
      });
    };
  }, [isMounted, bootstrap, effect, customboxPagePlaneName]);

  useLayoutEffect(() => {
    if (!isMounted || bootstrap || !effect || !isCustomboxPagePlaneEffect(effect) || customboxPagePlaneName === null || !isClosing) return undefined;

    const appPage = document.querySelector<HTMLElement>('.app-page');
    if (!appPage) return undefined;

    const appPageClosingClass = `app-page--custombox${customboxPagePlaneName}Closing`;
    if (effect === 'push') {
      const currentLeft = appPage.getBoundingClientRect().left;
      appPage.getAnimations().forEach((animation: Animation) => animation.cancel());
      appPage.animate(
        [{ transform: `translateX(${currentLeft}px)` }, { transform: 'translateX(0)' }],
        { duration: 500, easing: 'ease', fill: 'forwards' },
      );
    } else if (effect === 'contentscale') {
      const appPageOpenClass = `app-page--custombox${customboxPagePlaneName}Open`;
      appPage.classList.remove(appPageOpenClass);
    } else {
      appPage.classList.add(appPageClosingClass);
    }

    return () => {
      appPage.classList.remove(appPageClosingClass);
    };
  }, [isMounted, bootstrap, effect, customboxPagePlaneName, isClosing]);

  if (!isMounted) return null;

  const handleLayerMouseUp = (event: MouseEvent<HTMLDivElement>): void => {
    const isOutsideTarget = event.target === event.currentTarget || event.target === backdropRef.current;

    if (isOutsideTarget && dialogMouseDownRef.current) {
      ignoreBackdropClickRef.current = true;
    }
    dialogMouseDownRef.current = false;
  };

  const handleLayerClick = (event: MouseEvent<HTMLDivElement>): void => {
    const isOutsideTarget = event.target === event.currentTarget || event.target === backdropRef.current;

    if (!isOutsideTarget || !closeOnBackdrop) return;

    if (ignoreBackdropClickRef.current) {
      ignoreBackdropClickRef.current = false;
      return;
    }

    requestClose();
  };

  const modalElement = (
    <ModalCloseContext.Provider value={requestClose}>
      <div
        className={`modalLayer ${layerClassName ?? ''} ${bootstrap ? 'modalLayer--bootstrap' : ''} ${!bootstrap && effect ? 'modalLayer--custombox' : ''} ${effect ? `modalLayer--${effect}` : ''} ${isClosing ? 'modalLayer--closing' : ''} ${isBackdropVisible ? 'modalLayer--backdrop-visible' : ''} ${isDialogShown ? 'modalLayer--dialog-shown' : ''} ${isDialogVisible ? 'modalLayer--dialog-visible' : ''}`}
        role="presentation"
        onMouseUp={handleLayerMouseUp}
        onClick={handleLayerClick}
      >
      <div className="modalLayer__backdrop" ref={backdropRef} />
      <div
        className={`modalLayer__dialog modalLayer__dialog--${size} ${className ?? ''}`}
        ref={dialogRef}
        style={customWidth ? { width: customWidth } : undefined}
        role="dialog"
        aria-modal="true"
        aria-labelledby={title ? 'modal-title' : undefined}
        onMouseDown={() => {
          dialogMouseDownRef.current = true;
        }}
        onClick={(event) => event.stopPropagation()}
      >
        <div className={`modalLayer__content ${contentClassName ?? ''}`}>
          {title ? <div className="modalLayer__header"><button type="button" className="modalLayer__close" aria-label="Fechar" onClick={requestClose}>×</button><h4 id="modal-title">{title}</h4></div> : null}
          <div className={`modalLayer__body ${bodyClassName ?? ''}`}>{children}</div>
          {footer ? <div className="modalLayer__footer">{footer}</div> : null}
        </div>
      </div>
      </div>
    </ModalCloseContext.Provider>
  );

  return createPortal(modalElement, document.body);
}
