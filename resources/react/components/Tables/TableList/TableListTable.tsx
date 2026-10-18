import {
  Children,
  cloneElement,
  isValidElement,
  useMemo,
  useRef,
  useState,
  type KeyboardEvent,
  type PointerEvent,
  type ReactNode,
} from 'react';
import Table from '@/components/Tables/Table/Table';
import { joinClassNames } from '@/utils/joinClassNames';
import TableListColumnsMenu from './TableListColumnsMenu';
import type {
  TableListColumn,
  TableListTableProps,
} from './TableList.types';
import './TableList.css';

interface RowProps {
  children?: ReactNode;
}

interface CellProps {
  colSpan?: number;
}

interface ResizeState {
  leftColumnId: string;
  rightColumnId: string;
  pointerId: number;
  startX: number;
  widths: Record<string, number>;
}

function resizeAdjacentColumns(
  widths: Record<string, number>,
  leftColumn: TableListColumn,
  rightColumn: TableListColumn,
  delta: number,
): Record<string, number> {
  const leftWidth = widths[leftColumn.id];
  const rightWidth = widths[rightColumn.id];

  if (!leftWidth || !rightWidth) {
    return widths;
  }

  const leftMinWidth = Math.min(leftColumn.minWidth ?? 56, leftWidth);
  const rightMinWidth = Math.min(rightColumn.minWidth ?? 56, rightWidth);
  const boundedDelta = Math.max(
    leftMinWidth - leftWidth,
    Math.min(delta, rightWidth - rightMinWidth),
  );

  return {
    ...widths,
    [leftColumn.id]: Math.round(leftWidth + boundedDelta),
    [rightColumn.id]: Math.round(rightWidth - boundedDelta),
  };
}

function filterRowCells(
  rows: ReactNode,
  columns: TableListColumn[],
  hiddenColumns: ReadonlySet<string>,
): ReactNode {
  const visibleCount = columns.length - hiddenColumns.size;

  return Children.map(rows, (row) => {
    if (!isValidElement<RowProps>(row) || row.type !== 'tr') {
      return row;
    }

    const cells = Children.toArray(row.props.children);
    const onlyCell = cells[0];

    if (
      cells.length === 1
      && isValidElement<CellProps>(onlyCell)
      && onlyCell.props.colSpan
    ) {
      return cloneElement(
        row,
        undefined,
        cloneElement(onlyCell, { colSpan: visibleCount }),
      );
    }

    const visibleCells = cells.filter((_, index) => {
      const column = columns[index];
      return column ? !hiddenColumns.has(column.id) : true;
    });

    return cloneElement(row, undefined, visibleCells);
  });
}

export default function TableListTable({
  className,
  children,
  columns,
  summary,
  hover = true,
  ...props
}: TableListTableProps): React.JSX.Element {
  const tableRef = useRef<HTMLTableElement>(null);
  const resizeState = useRef<ResizeState | null>(null);
  const [hiddenColumns, setHiddenColumns] = useState<Set<string>>(
    () => new Set(),
  );
  const [columnWidths, setColumnWidths] = useState<Record<string, number>>({});

  const visibleColumns = useMemo(
    () => columns.filter((column) => !hiddenColumns.has(column.id)),
    [columns, hiddenColumns],
  );

  const visibleRows = useMemo(
    () => filterRowCells(children, columns, hiddenColumns),
    [children, columns, hiddenColumns],
  );

  function measureVisibleColumns(): Record<string, number> {
    const headerCells = tableRef.current?.querySelectorAll('thead th');
    const widths: Record<string, number> = {};

    visibleColumns.forEach((column, index) => {
      const headerCell = headerCells?.item(index);
      if (headerCell) {
        widths[column.id] = Math.round(headerCell.getBoundingClientRect().width);
      }
    });

    return widths;
  }

  function beginResize(
    event: PointerEvent<HTMLSpanElement>,
    leftColumn: TableListColumn,
    rightColumn: TableListColumn,
  ): void {
    if (event.button !== 0) {
      return;
    }

    const widths = measureVisibleColumns();

    if (!widths[leftColumn.id] || !widths[rightColumn.id]) {
      return;
    }

    event.preventDefault();
    event.currentTarget.setPointerCapture(event.pointerId);
    resizeState.current = {
      leftColumnId: leftColumn.id,
      rightColumnId: rightColumn.id,
      pointerId: event.pointerId,
      startX: event.clientX,
      widths,
    };
    setColumnWidths(widths);
  }

  function resizeColumn(event: PointerEvent<HTMLSpanElement>): void {
    const currentResize = resizeState.current;

    if (!currentResize || currentResize.pointerId !== event.pointerId) {
      return;
    }

    const leftColumn = columns.find(
      ({ id }) => id === currentResize.leftColumnId,
    );
    const rightColumn = columns.find(
      ({ id }) => id === currentResize.rightColumnId,
    );

    if (!leftColumn || !rightColumn) {
      return;
    }

    setColumnWidths(
      resizeAdjacentColumns(
        currentResize.widths,
        leftColumn,
        rightColumn,
        event.clientX - currentResize.startX,
      ),
    );
  }

  function endResize(event: PointerEvent<HTMLSpanElement>): void {
    if (resizeState.current?.pointerId !== event.pointerId) {
      return;
    }

    if (event.currentTarget.hasPointerCapture(event.pointerId)) {
      event.currentTarget.releasePointerCapture(event.pointerId);
    }
    resizeState.current = null;
  }

  function resizeColumnWithKeyboard(
    event: KeyboardEvent<HTMLSpanElement>,
    leftColumn: TableListColumn,
    rightColumn: TableListColumn,
  ): void {
    if (event.key !== 'ArrowLeft' && event.key !== 'ArrowRight') {
      return;
    }

    event.preventDefault();
    const widths = measureVisibleColumns();
    const direction = event.key === 'ArrowRight' ? 1 : -1;

    setColumnWidths(
      resizeAdjacentColumns(
        widths,
        leftColumn,
        rightColumn,
        direction * 10,
      ),
    );
  }

  function toggleColumn(columnId: string): void {
    const column = columns.find(({ id }) => id === columnId);
    if (!column || column.hideable === false || column.fixed) {
      return;
    }

    setHiddenColumns((currentColumns) => {
      const nextColumns = new Set(currentColumns);
      if (nextColumns.has(columnId)) {
        nextColumns.delete(columnId);
      } else if (columns.length - nextColumns.size > 1) {
        nextColumns.add(columnId);
      }
      return nextColumns;
    });
  }

  return (
    <div className="tableListFrame">
      <div className="tableListToolbar">
        <span className="tableListToolbar__spacer" aria-hidden="true" />
        <strong className="tableListToolbar__summary" role="status">
          {summary}
        </strong>
        <TableListColumnsMenu
          columns={columns}
          hiddenColumns={hiddenColumns}
          onToggle={toggleColumn}
        />
      </div>
      <div className="tableListViewport">
        <Table
          {...props}
          ref={tableRef}
          condensed
          hover={hover}
          className={joinClassNames('tableListTable', className)}
        >
          <colgroup>
            {visibleColumns.map((column) => (
              <col
                key={column.id}
                width={columnWidths[column.id] ?? column.width}
              />
            ))}
          </colgroup>
          <thead>
            <tr>
              {visibleColumns.map((column, index) => {
                const nextColumn = visibleColumns[index + 1];
                const canResizeBoundary = Boolean(
                  nextColumn && !column.fixed && !nextColumn.fixed,
                );

                return (
                  <th key={column.id} scope="col">
                    <span className="tableListTable__heading">
                      {column.label}
                    </span>
                    {nextColumn && canResizeBoundary ? (
                      <span
                        className="tableListTable__resizeHandle"
                        role="separator"
                        aria-label={`Redimensionar colunas ${column.label} e ${nextColumn.label}`}
                        aria-orientation="vertical"
                        aria-valuemin={column.minWidth ?? 56}
                        aria-valuenow={
                          columnWidths[column.id] ?? column.minWidth ?? 56
                        }
                        tabIndex={0}
                        onPointerDown={(event) =>
                          beginResize(event, column, nextColumn)
                        }
                        onPointerMove={resizeColumn}
                        onPointerUp={endResize}
                        onPointerCancel={endResize}
                        onKeyDown={(event) =>
                          resizeColumnWithKeyboard(
                            event,
                            column,
                            nextColumn,
                          )
                        }
                      />
                    ) : null}
                  </th>
                );
              })}
            </tr>
          </thead>
          <tbody>{visibleRows}</tbody>
        </Table>
      </div>
    </div>
  );
}
