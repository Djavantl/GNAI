import { useCallback, useId } from 'react';
import Table, { TableContents, tableAttributes } from '@/Components/Tables/Table/Table';
import type { TableDefinition } from '@/Components/Tables/Table/Table.types';
import PluginTable from '@/Components/Tables/PluginTable/PluginTable';
import type { TableInitializer } from '@/Components/Tables/PluginTable/PluginTable.types';
import './ResponsiveTable.plugin.css';
import './ResponsiveTable.css';
const scripts = ['/assets/gnai/plugins/RWD-Table-Patterns/rwd-table.min.js'];
export default function ResponsiveTable({
  definition,
}: {
  definition: TableDefinition;
}): JSX.Element {
  const id = 'responsive-' + useId().replace(/:/g, '');
  const initialize = useCallback<TableInitializer>(($, host) => {
    const element = host.querySelector('.table-responsive')!;
    $(element).responsiveTable();
    host.querySelectorAll('button').forEach((button) => {
      button.childNodes.forEach((node) => {
        if (node.nodeType === Node.TEXT_NODE)
          node.textContent =
            node.textContent
              ?.replace('Focus', 'Foco')
              .replace('Display all', 'Exibir tudo')
              .replace('Display', 'Exibir') ?? '';
      });
    });
    return () => {
      const api = $(element).data('responsiveTable');
      api?.destroy?.();
    };
  }, []);
  return (
    <PluginTable
      scripts={scripts}
      initialize={initialize}
      className="table-rep-plugin"
    >
      <div className="table-responsive" data-pattern="priority-columns">
        <Table {...tableAttributes(definition.attributes)} id={id}>
          <TableContents definition={definition} />
        </Table>
      </div>
    </PluginTable>
  );
}
