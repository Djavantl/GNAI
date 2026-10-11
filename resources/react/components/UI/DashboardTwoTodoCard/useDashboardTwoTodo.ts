import { useMemo, useRef, useState } from 'react';
import { useCustomScrollbar } from '@/hooks/useCustomScrollbar';
import type { DashboardTwoTodoItem } from './DashboardTwoTodoCard.types';

export function useDashboardTwoTodo(initialItems: DashboardTwoTodoItem[], onEmptyTodo?: () => void) {
  const [items, setItems] = useState<DashboardTwoTodoItem[]>(initialItems);
  const [value, setValue] = useState<string>('');
  const scrollbar = useCustomScrollbar<HTMLUListElement>(true);
  const inputRef = useRef<HTMLInputElement | null>(null);

  const remainingItems = useMemo(
    () => items.filter((item: DashboardTwoTodoItem) => !item.done).length,
    [items]
  );

  function toggleItem(itemId: string): void {
    setItems((currentItems: DashboardTwoTodoItem[]) =>
      currentItems.map((item: DashboardTwoTodoItem) =>
        item.id === itemId ? { ...item, done: !item.done } : item
      )
    );
  }

  function archiveCompleted(): void {
    setItems((currentItems: DashboardTwoTodoItem[]) =>
      currentItems.filter((item: DashboardTwoTodoItem) => !item.done)
    );
  }

  function addItem(): void {
    const trimmedValue = value.trim();

    if (!trimmedValue) {
      onEmptyTodo?.();
      inputRef.current?.focus();
      return;
    }

    setItems((currentItems: DashboardTwoTodoItem[]) => [
      { id: `todo-${Date.now()}`, text: trimmedValue, done: false },
      ...currentItems
    ]);
    setValue('');
  }

  return {
    items,
    value,
    setValue,
    remainingItems,
    toggleItem,
    archiveCompleted,
    addItem,
    scrollRef: scrollbar.scrollRef,
    thumbHeight: scrollbar.thumbHeight,
    thumbTop: scrollbar.thumbTop,
    isVisible: scrollbar.isVisible,
    inputRef
  };
}
