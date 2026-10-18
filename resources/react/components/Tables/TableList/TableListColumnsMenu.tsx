import { useEffect, useId, useRef } from 'react';
import ChoiceControl from '@/components/Forms/ChoiceControl/ChoiceControl';
import type { TableListColumn } from './TableList.types';

interface TableListColumnsMenuProps {
  columns: TableListColumn[];
  hiddenColumns: ReadonlySet<string>;
  onToggle: (columnId: string) => void;
}

export default function TableListColumnsMenu({
  columns,
  hiddenColumns,
  onToggle,
}: TableListColumnsMenuProps): React.JSX.Element {
  const controlId = useId();
  const menuRef = useRef<HTMLDetailsElement>(null);
  const visibleCount = columns.length - hiddenColumns.size;

  useEffect(() => {
    function closeOnOutsidePointer(event: globalThis.PointerEvent): void {
      const menu = menuRef.current;
      if (menu?.open && !menu.contains(event.target as Node)) {
        menu.removeAttribute('open');
      }
    }

    function closeOnEscape(event: globalThis.KeyboardEvent): void {
      const menu = menuRef.current;
      if (event.key !== 'Escape' || !menu?.open) {
        return;
      }

      menu.removeAttribute('open');
      menu.querySelector<HTMLElement>('summary')?.focus();
    }

    document.addEventListener('pointerdown', closeOnOutsidePointer);
    document.addEventListener('keydown', closeOnEscape);

    return () => {
      document.removeEventListener('pointerdown', closeOnOutsidePointer);
      document.removeEventListener('keydown', closeOnEscape);
    };
  }, []);

  return (
    <details ref={menuRef} className="tableListColumnsMenu">
      <summary>
        Colunas
        <span className="tableListColumnsMenu__caret" aria-hidden="true" />
      </summary>
      <fieldset className="tableListColumnsMenu__options">
        <legend>Colunas visíveis</legend>
        {columns.map((column) => {
          const visible = !hiddenColumns.has(column.id);
          const canHide = column.hideable !== false && !column.fixed;

          return (
            <ChoiceControl
              key={column.id}
              type="checkbox"
              tone="purple"
              id={`${controlId}-${column.id}`}
              label={column.label}
              checked={visible}
              disabled={!canHide || (visible && visibleCount === 1)}
              className="tableListColumnsMenu__option"
              onChange={() => onToggle(column.id)}
            />
          );
        })}
      </fieldset>
    </details>
  );
}
