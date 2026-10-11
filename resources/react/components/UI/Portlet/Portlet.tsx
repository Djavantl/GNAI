import { useEffect, useRef, useState, type ReactNode } from 'react';
import './Portlet.css';
import { joinClassNames } from '@/utils/joinClassNames';

export type PortletTone = 'default' | 'custom' | 'primary' | 'info' | 'success' | 'warning' | 'danger' | 'inverse' | 'pink' | 'purple';

interface PortletProps {
  title: string;
  tone?: PortletTone;
  children: ReactNode;
  onRemoved?: () => void;
  removeImmediately?: boolean;
}

export default function Portlet({ title, tone = 'default', children, onRemoved, removeImmediately = false }: PortletProps): React.JSX.Element | null {
  const [collapsed, setCollapsed] = useState<boolean>(false);
  const [removing, setRemoving] = useState<boolean>(false);
  const [removed, setRemoved] = useState<boolean>(false);
  const [reloading, setReloading] = useState<boolean>(false);
  const [reloadClosing, setReloadClosing] = useState<boolean>(false);
  const [collapseHeight, setCollapseHeight] = useState<number | 'auto'>('auto');
  const collapseRef = useRef<HTMLDivElement | null>(null);
  const isFirstRenderRef = useRef<boolean>(true);

  useEffect(() => {
    const collapseElement = collapseRef.current;
    if (!collapseElement) return;

    if (isFirstRenderRef.current) {
      isFirstRenderRef.current = false;
      setCollapseHeight(collapsed ? 0 : 'auto');
      return;
    }

    if (collapsed) {
      const currentHeight = collapseElement.scrollHeight;
      setCollapseHeight(currentHeight);
      window.requestAnimationFrame(() => window.requestAnimationFrame(() => setCollapseHeight(0)));
      return;
    }

    setCollapseHeight(0);
    window.requestAnimationFrame(() => window.requestAnimationFrame(() => setCollapseHeight(collapseElement.scrollHeight)));
    const timerId = window.setTimeout(() => setCollapseHeight('auto'), 350);
    return () => window.clearTimeout(timerId);
  }, [collapsed]);

  useEffect(() => {
    if (!reloading) {
      return undefined;
    }

    let closingTimeoutId: number | undefined;
    const timeoutId = window.setTimeout(() => {
      setReloadClosing(true);
      closingTimeoutId = window.setTimeout(() => {
        setReloading(false);
        setReloadClosing(false);
      }, 200);
    }, 500 + 1500 * Math.random());
    return () => {
      window.clearTimeout(timeoutId);
      window.clearTimeout(closingTimeoutId);
    };
  }, [reloading]);

  if (removed) {
    return null;
  }

  return (
    <article
      className={joinClassNames('portletCard portlet', removing && 'portletCard--removing')}
      onTransitionEnd={() => {
        if (removing) {
          setRemoved(true);
          onRemoved?.();
        }
      }}
    >
      <div className={joinClassNames('portletCard__heading', 'portlet-heading', `portletCard__heading--${tone}`)}>
        <h3 className="portletCard__title portlet-title">{title}</h3>
        <div className="portletCard__widgets portlet-widgets">
          <button type="button" aria-label={`Recarregar ${title}`} onClick={() => setReloading(true)}>
            <i className="ion-refresh" aria-hidden="true" />
          </button>
          {' '}
          <span className="portletCard__divider divider" aria-hidden="true" />
          <button type="button" aria-label={`${collapsed ? 'Expandir' : 'Recolher'} ${title}`} onClick={() => setCollapsed((value: boolean) => !value)}>
            <i className={collapsed ? 'ion-plus-round' : 'ion-minus-round'} aria-hidden="true" />
          </button>
          {' '}
          <span className="portletCard__divider divider" aria-hidden="true" />
          <button type="button" aria-label={`Remover ${title}`} onClick={() => { if (removeImmediately) { setRemoved(true); onRemoved?.(); } else setRemoving(true); }}>
            <i className="ion-close-round" aria-hidden="true" />
          </button>
        </div>
        <div className="clearfix" />
      </div>
      <div ref={collapseRef} className="portletCard__collapse panel-collapse" style={{ height: collapseHeight === 'auto' ? 'auto' : `${collapseHeight}px` }}>
        <div className="portletCard__body portlet-body">{children}</div>
      </div>
      {reloading ? <div className={joinClassNames('portletCard__disabled panel-disabled', reloadClosing && 'portletCard__disabled--closing')} aria-label="Carregando"><div className="loader-1" /></div> : null}
    </article>
  );
}
