import type { HTMLAttributes, ReactNode, TdHTMLAttributes } from 'react';
import Pagination from '@/components/Navigation/Pagination/Pagination';
import Button from '@/components/UI/Button/Button';
import { joinClassNames } from '@/utils/joinClassNames';
import './TableList.css';

export { default as TableListTable } from './TableListTable';
export { default as TableListMoreFilters } from './TableListMoreFilters';
export type { TableListColumn } from './TableList.types';

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
