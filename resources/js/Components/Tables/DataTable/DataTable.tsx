import Table from '@/Components/Tables/Table/Table';
import type { ReactNode } from 'react';
import './DataTable.css';
import { joinClassNames } from '@/utils/joinClassNames';

interface DataTableProps {
  children: ReactNode;
  className?: string;
}

export default function DataTable({ children, className }: DataTableProps): JSX.Element {
  return (
    <div className="dataTable__wrapper table-responsive">
      <Table appearance="inherit" className={joinClassNames('dataTable table', className ?? '')}>{children}</Table>
    </div>
  );
}
