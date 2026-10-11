import { useCallback, useRef } from 'react';
import Table, { TableContents, tableAttributes } from '@/components/Tables/Table/Table';
import type { TableDefinition } from '@/components/Tables/Table/Table.types';
import PluginTable from '@/components/Tables/PluginTable/PluginTable';
import type {
  TableInitializer,
  JQueryTable,
} from '@/components/Tables/PluginTable/PluginTable.types';
const scripts = ['/assets/react/plugins/tiny-editable/mindmup-editabletable.js'];
export default function InlineEditableTable({
  definition,
  maxTotal = 5000,
  onChange,
}: {
  definition: TableDefinition;
  maxTotal?: number;
  onChange?: (rows: string[][]) => void;
}): React.JSX.Element {
  const callback = useRef(onChange);
  callback.current = onChange;
  const initialize = useCallback<TableInitializer>(
    ($, host, table) => {
      (
        $(table) as JQueryTable & { editableTableWidget: () => JQueryTable }
      ).editableTableWidget();
      const totals = (): number[] =>
        definition.head[0].map((_, column) =>
          column
            ? Array.from(table.tBodies[0].rows).reduce(
                (sum, row) => sum + Number(row.cells[column].textContent),
                0,
              )
            : 0,
        );
      const update = (): void => {
        totals().forEach((total, column) => {
          if (column)
            table.tFoot!.rows[0].cells[column].textContent = String(total);
        });
      };
      update();
      $(table)
        .find('td')
        .on(
          'validate.react',
          function (
            this: HTMLTableCellElement,
            _event: unknown,
            value: string,
          ) {
            return this.cellIndex === 0
              ? Boolean(value.trim())
              : value.trim() !== '' && Number.isFinite(Number(value));
          },
        )
        .on('change.react', function (this: HTMLTableCellElement) {
          if (this.cellIndex === 1 && totals()[1] > maxTotal) return false;
          update();
          callback.current?.(
            Array.from(table.tBodies[0].rows).map((row) =>
              Array.from(row.cells).map((cell) => cell.textContent ?? ''),
            ),
          );
        });
      return () => {
        $(table).find('td').off('.react');
        $(table).off();
        $(host).find('input').remove();
      };
    },
    [definition, maxTotal],
  );
  return (
    <PluginTable scripts={scripts} initialize={initialize}>
      <div className="table-responsive">
        <Table {...tableAttributes(definition.attributes)}>
          <TableContents
            definition={definition}
            footer={
              <tr>
                <th>
                  <strong>TOTAL</strong>
                </th>
                {definition.head[0].slice(1).map((_, i) => (
                  <th key={i} />
                ))}
              </tr>
            }
          />
        </Table>
      </div>
    </PluginTable>
  );
}
