import { useEffect, useRef, useState } from 'react';
import type { DashboardThreeProjectItem } from './DashboardThreeProjectsPortlet.types';

export function useDashboardThreeProjectsPortlet(items: DashboardThreeProjectItem[]) {
  const collapseRef = useRef<HTMLDivElement | null>(null);
  const isFirstRenderRef = useRef<boolean>(true);
  const [collapsed, setCollapsed] = useState<boolean>(false);
  const [refreshing, setRefreshing] = useState<boolean>(false);
  const [removing, setRemoving] = useState<boolean>(false);
  const [removed, setRemoved] = useState<boolean>(false);
  const [collapseHeight, setCollapseHeight] = useState<number | 'auto'>('auto');

  useEffect(() => {
    const collapseElement = collapseRef.current;

    if (!collapseElement) {
      return;
    }

    if (isFirstRenderRef.current) {
      isFirstRenderRef.current = false;
      setCollapseHeight(collapsed ? 0 : 'auto');
      return;
    }

    if (collapsed) {
      const currentHeight = collapseElement.scrollHeight;
      setCollapseHeight(currentHeight);

      window.requestAnimationFrame(() => {
        window.requestAnimationFrame(() => {
          setCollapseHeight(0);
        });
      });

      return;
    }

    setCollapseHeight(0);

    window.requestAnimationFrame(() => {
      window.requestAnimationFrame(() => {
        setCollapseHeight(collapseElement.scrollHeight);
      });
    });

    const timerId = window.setTimeout(() => {
      setCollapseHeight('auto');
    }, 350);

    return () => {
      window.clearTimeout(timerId);
    };
  }, [collapsed, items]);

  useEffect(() => {
    if (!refreshing) {
      return;
    }

    const timeoutId = window.setTimeout(() => {
      setRefreshing(false);
    }, 1200);

    return () => {
      window.clearTimeout(timeoutId);
    };
  }, [refreshing]);

  function handleReload(): void {
    setRefreshing(true);
  }

  function handleCollapse(): void {
    setCollapsed((currentValue: boolean) => !currentValue);
  }

  function handleRemove(): void {
    setRemoving(true);
  }

  function handleRemoveTransitionEnd(): void {
    if (removing) {
      setRemoved(true);
    }
  }

  return {
    collapseRef,
    collapsed,
    refreshing,
    removing,
    removed,
    collapseHeight,
    handleReload,
    handleCollapse,
    handleRemove,
    handleRemoveTransitionEnd
  };
}
