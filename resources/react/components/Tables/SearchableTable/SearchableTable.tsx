import { useCallback } from 'react';
import Table, { TableContents, tableAttributes } from '@/components/Tables/Table/Table';
import type { TableDefinition } from '@/components/Tables/Table/Table.types';
import PluginTable from '@/components/Tables/PluginTable/PluginTable';
import type { TableInitializer } from '@/components/Tables/PluginTable/PluginTable.types';
import './SearchableTable.plugin.css';
export const dataTableScripts = [
  '/assets/react/plugins/datatables/jquery.dataTables.min.js',
  '/assets/react/plugins/datatables/dataTables.bootstrap.js',
];
export const dataTablePortuguese = {
  emptyTable: 'Nenhum registro disponível',
  info: 'Exibindo _START_ a _END_ de _TOTAL_ registros',
  infoEmpty: 'Exibindo 0 a 0 de 0 registros',
  infoFiltered: '(filtrados de _MAX_ registros)',
  lengthMenu: 'Exibir _MENU_ registros',
  loadingRecords: 'Carregando...',
  processing: 'Processando...',
  search: 'Buscar:',
  zeroRecords: 'Nenhum registro encontrado',
  paginate: {
    first: 'Primeira',
    last: 'Última',
    next: 'Próxima',
    previous: 'Anterior',
  },
  aria: {
    sortAscending: ': ordenar em ordem crescente',
    sortDescending: ': ordenar em ordem decrescente',
  },
};
export default function SearchableTable({
  definition,
  pageSize = 10,
}: {
  definition: TableDefinition;
  pageSize?: number;
}): React.JSX.Element {
  const initialize = useCallback<TableInitializer>(
    ($, host, table) => {
      const api = $(table).DataTable({
        language: dataTablePortuguese,
        pageLength: pageSize,
      });
      return () => api.destroy();
    },
    [pageSize],
  );
  return (
    <PluginTable scripts={dataTableScripts} initialize={initialize}>
      <Table {...tableAttributes(definition.attributes)}>
        <TableContents definition={definition} />
      </Table>
    </PluginTable>
  );
}
