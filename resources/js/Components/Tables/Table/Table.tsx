import { forwardRef, type ReactNode, type TableHTMLAttributes } from 'react';
import type { TableDefinition } from './Table.types';
import TableLabel from '@/Components/UI/TableLabel/TableLabel';
import './Table.css';
export function tableAttributes(
  attributes: Record<string, string>,
): Record<string, unknown> {
  return Object.fromEntries(
    Object.entries(attributes)
      .filter(([key]) => key !== 'style')
      .map(([key, value]) => [
        (
          {
            class: 'className',
            colspan: 'colSpan',
            rowspan: 'rowSpan',
          } as Record<string, string>
        )[key] ?? key,
        value,
      ]),
  );
}
export interface TableProps extends TableHTMLAttributes<HTMLTableElement> {
  striped?: boolean;
  bordered?: boolean;
  hover?: boolean;
  condensed?: boolean;
  appearance?: 'standard' | 'inherit';
}
const Table = forwardRef<HTMLTableElement, TableProps>(function Table(
  {
    className = '',
    striped,
    bordered,
    hover,
    condensed,
    appearance = 'standard',
    children,
    ...props
  },
  ref,
) {
  return (
    <table
      {...props}
      ref={ref}
      className={`${appearance === 'standard' ? 'boldTable ' : ''}table ${striped ? 'table-striped' : ''} ${bordered ? 'table-bordered' : ''} ${hover ? 'table-hover' : ''} ${condensed ? 'table-condensed' : ''} ${className}`}
    >
      {children}
    </table>
  );
});
export default Table;
export function TableContents({
  definition,
  footer,
  actions,
}: {
  definition: TableDefinition;
  footer?: ReactNode;
  actions?: ReactNode;
}): JSX.Element {
  return (
    <>
      <thead>
        {definition.head.map((row, i) => (
          <tr key={i}>
            {row.map((cell, j) => (
              <th {...tableAttributes(cell.attributes)} key={j}>
                {cell.text}
              </th>
            ))}
          </tr>
        ))}
      </thead>
      <tbody>
        {definition.body.map((row, i) => (
          <tr className={row.className} key={i}>
            {row.cells.map((cell, j) => {
              const Tag = cell.tag === 'th' ? 'th' : 'td';
              return (
                <Tag {...tableAttributes(cell.attributes)} key={j}>
                  {cell.badge ? (
                    <TableLabel tone={cell.badge}>{cell.text}</TableLabel>
                  ) : cell.actions ? (
                    actions
                  ) : cell.link ? (
                    <a href="#" onClick={(e) => e.preventDefault()}>
                      {cell.text}
                    </a>
                  ) : (
                    cell.text
                  )}
                  {cell.secondary && (
                    <span className="co-name"> {cell.secondary}</span>
                  )}
                </Tag>
              );
            })}
          </tr>
        ))}
      </tbody>
      {footer && <tfoot>{footer}</tfoot>}
    </>
  );
}
