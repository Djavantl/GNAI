import { forwardRef, useImperativeHandle, useRef } from 'react';
import { joinClassNames } from '@/utils/joinClassNames';
import type { NestableItem, NestableVariant } from './ComponentsNestable.types';
import { getNestableRuntime, type NestablePluginHandle, useNestablePlugin } from './useNestablePlugin';

interface NestableListProps {
  group?: number;
  id?: string;
  items: NestableItem[];
  variant?: NestableVariant;
}

const NestableList = forwardRef<NestablePluginHandle, NestableListProps>(function NestableList(
  { group, id, items, variant = 'default' }: NestableListProps,
  forwardedRef
): JSX.Element {
  const rootRef = useRef<HTMLDivElement | null>(null);

  useNestablePlugin(rootRef, { group });

  useImperativeHandle(forwardedRef, () => ({
    collapseAll: () => {
      const runtime = getNestableRuntime();
      if (runtime && rootRef.current) {
        runtime(rootRef.current).nestable('collapseAll');
      }
    },
    expandAll: () => {
      const runtime = getNestableRuntime();
      if (runtime && rootRef.current) {
        runtime(rootRef.current).nestable('expandAll');
      }
    }
  }), []);

  return (
    <div ref={rootRef} className={joinClassNames(variant === 'empty' ? 'custom-dd-empty' : 'custom-dd', 'dd')} id={id}>
      <ol className="dd-list">
        {items.map((item) => <NestableNode item={item} key={`${variant}-${item.id}`} variant={variant} />)}
      </ol>
    </div>
  );
});

function NestableNode({ item, variant }: { item: NestableItem; variant: NestableVariant }): JSX.Element {
  const hasChildren = Boolean(item.children?.length);

  return (
    <li className={joinClassNames('dd-item', variant === 'empty' ? 'dd3-item' : undefined)} data-id={item.id}>
      {variant === 'empty' ? (
        <>
          <div className="dd-handle dd3-handle" />
          <div className="dd3-content">Item {item.id}</div>
        </>
      ) : (
        <div className="dd-handle">Item {item.id}</div>
      )}
      {hasChildren ? (
        <ol className="dd-list">
          {item.children?.map((child) => <NestableNode item={child} key={`${item.id}-${child.id}`} variant={variant} />)}
        </ol>
      ) : null}
    </li>
  );
}

export default NestableList;
