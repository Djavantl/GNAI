import { useCallback, useId, useRef } from 'react';
import { renderToStaticMarkup } from 'react-dom/server';
import Table, { TableContents, tableAttributes } from '@/components/Tables/Table/Table';
import TableLabel from '@/components/UI/TableLabel/TableLabel';
import type { TableDefinition } from '@/components/Tables/Table/Table.types';
import PluginTable from '@/components/Tables/PluginTable/PluginTable';
import type { TableInitializer } from '@/components/Tables/PluginTable/PluginTable.types';
import './BootstrapTable.plugin.css';
const scripts = [
  '/assets/react/plugins/bootstrap-table/bootstrap-table.min.js',
  '/assets/react/plugins/bootstrap-table/bootstrap-table-pt-BR.js',
];
export default function BootstrapTable({
  definition,
  removable = false,
  onSelectionChange,
}: {
  definition: TableDefinition;
  removable?: boolean;
  onSelectionChange?: (rows: unknown[]) => void;
}): React.JSX.Element {
  const id = 'table-toolbar-' + useId().replace(/:/g, '');
  const callback = useRef(onSelectionChange);
  callback.current = onSelectionChange;
  const initialize = useCallback<TableInitializer>(
    ($, host, table) => {
      const columns = definition.head[0].map((cell) => {
        const column: Record<string, unknown> = { title: cell.text };
        for (const [key, value] of Object.entries(cell.attributes)) {
          if (key.startsWith('data-'))
            column[
              key
                .slice(5)
                .replace(/-([a-z])/g, (_, s: string) => s.toUpperCase())
            ] = value === 'true' ? true : value === 'false' ? false : value;
        }
        const format = column.formatter;
        if (format === 'invoiceFormatter')
          column.formatter = (value: string) =>
            renderToStaticMarkup(
              <a href="#" className="btn-link">
                Pedido #{value}
              </a>,
            );
        if (format === 'dateFormatter')
          column.formatter = (value: string) =>
            renderToStaticMarkup(<span className="text-muted">{value}</span>);
        if (format === 'statusFormatter')
          column.formatter = (value: string) =>
            renderToStaticMarkup(
              <TableLabel
                tone={
                  (
                    {
                      Pago: 'success',
                      'Não pago': 'warning',
                      Enviado: 'info',
                      Reembolsado: 'danger',
                    } as Record<string, string>
                  )[value]
                }
              >
                {value}
              </TableLabel>,
            );
        if (column.sorter === 'priceSorter')
          column.sorter = (a: string, b: string) =>
            Number(a.slice(1)) - Number(b.slice(1));
        return column;
      });
      const collection = $(table);
      collection.bootstrapTable({
        locale: 'pt-BR',
        columns,
        toolbar: removable ? '#' + id : undefined,
      });
      const remove = host.querySelector<HTMLButtonElement>('[data-remove]');
      collection.on(
        'check.bs.table uncheck.bs.table check-all.bs.table uncheck-all.bs.table',
        () => {
          const rows = collection.bootstrapTable('getSelections') as unknown[];
          if (remove) remove.disabled = !rows.length;
          callback.current?.(rows);
        },
      );
      const removeRows = (): void => {
        const rows = collection.bootstrapTable('getSelections') as {
          id: unknown;
        }[];
        collection.bootstrapTable('remove', {
          field: 'id',
          values: rows.map((row) => row.id),
        });
        if (remove) remove.disabled = true;
        callback.current?.([]);
      };
      remove?.addEventListener('click', removeRows);
      return () => {
        remove?.removeEventListener('click', removeRows);
        collection.off(
          'check.bs.table uncheck.bs.table check-all.bs.table uncheck-all.bs.table',
        );
        collection.bootstrapTable('destroy');
      };
    },
    [definition, removable, id],
  );
  const attrs = { ...definition.attributes };
  delete attrs['data-toggle'];
  delete attrs['data-toolbar'];
  const safeDefinition = {
    ...definition,
    head: definition.head.map((row) =>
      row.map((cell) => ({
        ...cell,
        attributes: Object.fromEntries(
          Object.entries(cell.attributes).filter(
            ([key]) => !['data-formatter', 'data-sorter'].includes(key),
          ),
        ),
      })),
    ),
  };
  return (
    <PluginTable scripts={scripts} initialize={initialize}>
      {removable && (
        <button id={id} data-remove className="btn btn-danger" disabled>
          <i className="fa fa-times m-r-5" />
          Excluir
        </button>
      )}
      <Table {...tableAttributes(attrs)}>
        <TableContents definition={safeDefinition} />
      </Table>
    </PluginTable>
  );
}
