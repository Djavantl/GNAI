import { useEffect, useId, useRef, useState } from 'react';
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
  const menuRef = useRef<HTMLDivElement>(null);
  const [open, setOpen] = useState(false);
  const visibleCount = columns.length - hiddenColumns.size;

  useEffect(() => {
    function closeOnOutsidePointer(event: globalThis.PointerEvent): void {
      const menu = menuRef.current;
      if (open && menu && !menu.contains(event.target as Node)) {
        setOpen(false);
      }
    }

    function closeOnEscape(event: globalThis.KeyboardEvent): void {
      const menu = menuRef.current;
      if (event.key !== 'Escape' || !open) {
        return;
      }

      setOpen(false);
      menu?.querySelector<HTMLElement>('button')?.focus();
    }

    document.addEventListener('pointerdown', closeOnOutsidePointer);
    document.addEventListener('keydown', closeOnEscape);

    return () => {
      document.removeEventListener('pointerdown', closeOnOutsidePointer);
      document.removeEventListener('keydown', closeOnEscape);
    };
  }, [open]);

  return (
    <div
      ref={menuRef}
      className={`tableListColumnsMenu${open ? ' tableListColumnsMenu--open' : ''}`}
    >
      <button
        type="button"
        className="tableListColumnsMenu__trigger"
        aria-expanded={open}
        aria-controls={`${controlId}-options`}
        onClick={() => setOpen((current) => !current)}
      >
        Colunas
        <span className="tableListColumnsMenu__caret" aria-hidden="true" />
      </button>
      <div
        id={`${controlId}-options`}
        className="tableListColumnsMenu__collapse"
        aria-hidden={!open}
      >
        <fieldset className="tableListColumnsMenu__options" disabled={!open}>
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
      </div>
    </div>
  );
}
