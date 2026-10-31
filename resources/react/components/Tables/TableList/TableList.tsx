import type { HTMLAttributes, ReactNode, TdHTMLAttributes } from 'react';
import Pagination from '@/components/Navigation/Pagination/Pagination';
import Button from '@/components/UI/Button/Button';
import { joinClassNames } from '@/utils/joinClassNames';
import type { TableListPage } from './TableList.types';
import { formatTableListRange } from './TableList.utils';
import './TableList.css';

export { default as TableListTable } from './TableListTable';
export { default as TableListMoreFilters } from './TableListMoreFilters';
export { formatTableListRange, formatTableListDate, formatTableListText } from './TableList.utils';
export { useTableListAction, useTableListVisit } from './useTableListRequest';
export type { TableListColumn, TableListPage, TableListRange } from './TableList.types';

export function TableListCard({
  className,
  children,
  loading = false,
  loadingLabel = 'Atualizando listagem',
  ...props
}: HTMLAttributes<HTMLElement> & {
  loading?: boolean;
  loadingLabel?: string;
}): React.JSX.Element {
  return (
    <section
      className={joinClassNames('tableListCard', className)}
      {...props}
      aria-busy={loading || props['aria-busy']}
    >
      {children}
      {loading ? (
        <div
          className="tableListCard__loading panel-disabled"
          role="status"
          aria-label={loadingLabel}
        >
          <span className="tableListCard__loader loader-1" aria-hidden="true" />
        </div>
      ) : null}
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
}: {
  children: ReactNode;
}): React.JSX.Element {
  return (
    <div className="tableListFilterActions">
      {children}
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
    <Button
      type="button"
      tone="cancel"
      size="sm"
      iconOnly
      leftIconClass="fa fa-eraser"
      title="Limpar filtros"
      aria-label="Limpar filtros"
      className="tableListClearButton"
      disabled={disabled}
      onClick={onClick}
    />
  );
}

export function TableListScroll({
  className,
  children,
  ...props
}: HTMLAttributes<HTMLDivElement>): React.JSX.Element {
  return (
    <div
      role="region"
      className={joinClassNames('tableListScroll', className)}
      {...props}
    >
      {children}
    </div>
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

/** Rodapé das listagens paginadas: o intervalo ("1 a 10 de 14") e a paginação vêm do mesmo paginador. */
export function TableListFooter({
  page,
  disabled = false,
  paginationLabel,
  onSelect,
}: {
  page: TableListPage;
  disabled?: boolean;
  paginationLabel: string;
  onSelect: (page: number) => void;
}): React.JSX.Element {
  return (
    <footer className="tableListFooter">
      <span>{formatTableListRange(page)}</span>
      <nav aria-label={paginationLabel}>
        <Pagination
          currentPage={page.current_page}
          pageCount={page.last_page}
          disabled={disabled}
          onSelect={onSelect}
        />
      </nav>
    </footer>
  );
}

export function TableListSummary({
  children,
}: {
  children: ReactNode;
}): React.JSX.Element {
  return (
    <footer className="tableListFooter">
      <span>{children}</span>
    </footer>
  );
}
