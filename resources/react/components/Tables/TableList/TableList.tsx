import type { HTMLAttributes, ReactNode, TdHTMLAttributes } from 'react';
import Pagination from '@/components/Navigation/Pagination/Pagination';
import Table, { type TableProps } from '@/components/Tables/Table/Table';
import Button from '@/components/UI/Button/Button';
import { joinClassNames } from '@/utils/joinClassNames';
import './TableList.css';

export function TableListCard({
  className,
  children,
  ...props
}: HTMLAttributes<HTMLElement>): React.JSX.Element {
  return (
    <section
      className={joinClassNames('tableListCard', className)}
      {...props}
    >
      {children}
    </section>
  );
}

export function TableListCreateButton({
  href,
  ariaLabel,
}: {
  href: string;
  ariaLabel: string;
}): React.JSX.Element {
  return (
    <Button
      href={href}
      tone="list-action"
      labelIconClass="fa fa-plus"
      aria-label={ariaLabel}
    >
      Cadastrar
    </Button>
  );
}

export function TableListFilterActions({
  children,
  status,
}: {
  children: ReactNode;
  status?: ReactNode;
}): React.JSX.Element {
  return (
    <div className="tableListFilterActions">
      {children}
      {status ? (
        <span role="status">
          {status}
        </span>
      ) : null}
    </div>
  );
}

export function TableListClearButton({
  disabled = false,
  onClick,
}: {
  disabled?: boolean;
  onClick: () => void;
}): React.JSX.Element {
  return (
    <Button type="button" disabled={disabled} onClick={onClick}>
      Limpar filtros
    </Button>
  );
}

export function TableListProgress({
  className,
  children,
  ...props
}: HTMLAttributes<HTMLDivElement>): React.JSX.Element {
  return (
    <div
      role="status"
      className={joinClassNames('tableListProgress', className)}
      {...props}
    >
      {children}
    </div>
  );
}

export function TableListScroll({
  className,
  children,
  ...props
}: HTMLAttributes<HTMLDivElement>): React.JSX.Element {
  return (
    <div
      tabIndex={0}
      role="region"
      className={joinClassNames('tableListScroll', className)}
      {...props}
    >
      {children}
    </div>
  );
}

export function TableListTable({
  className,
  children,
  ...props
}: TableProps): React.JSX.Element {
  return (
    <Table
      condensed
      className={joinClassNames('tableListTable', className)}
      {...props}
    >
      {children}
    </Table>
  );
}

export function TableListEmpty({
  className,
  children,
  ...props
}: TdHTMLAttributes<HTMLTableCellElement>): React.JSX.Element {
  return (
    <td className={joinClassNames('tableListEmpty', className)} {...props}>
      {children}
    </td>
  );
}

export function TableListFooter({
  summary,
  currentPage,
  pageCount,
  disabled = false,
  paginationLabel,
  onSelect,
}: {
  summary: ReactNode;
  currentPage: number;
  pageCount: number;
  disabled?: boolean;
  paginationLabel: string;
  onSelect: (page: number) => void;
}): React.JSX.Element {
  return (
    <footer className="tableListFooter">
      <span>{summary}</span>
      <nav aria-label={paginationLabel}>
        <Pagination
          currentPage={currentPage}
          pageCount={pageCount}
          disabled={disabled}
          onSelect={onSelect}
        />
      </nav>
    </footer>
  );
}
