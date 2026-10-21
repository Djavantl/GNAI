import type { ReactNode } from 'react';
import {
  TableListCard,
  TableListEmpty,
  TableListScroll,
  TableListSummary,
  TableListTable,
  type TableListColumn,
} from '@/components/Tables/TableList/TableList';

export default function StudentRecordTable({
  ariaLabel,
  columns,
  count,
  emptyMessage,
  actions,
  children,
}: {
  ariaLabel: string;
  columns: TableListColumn[];
  count: number;
  emptyMessage: string;
  actions?: ReactNode;
  children: ReactNode;
}): React.JSX.Element {
  const summary = `${count} ${count === 1 ? 'registro' : 'registros'}`;

  return (
    <div className="studentsPage__recordList">
      {actions ? (
        <div className="studentsPage__recordListActions">{actions}</div>
      ) : null}
      <TableListCard aria-label={ariaLabel}>
        <TableListScroll aria-label={ariaLabel}>
          <TableListTable columns={columns} summary={summary}>
            {count ? children : (
              <tr>
                <TableListEmpty colSpan={columns.length}>
                  {emptyMessage}
                </TableListEmpty>
              </tr>
            )}
          </TableListTable>
        </TableListScroll>
        <TableListSummary>{summary}</TableListSummary>
      </TableListCard>
    </div>
  );
}
