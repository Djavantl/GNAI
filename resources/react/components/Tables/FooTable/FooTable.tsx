import { useCallback, useId } from 'react';
import { renderToStaticMarkup } from 'react-dom/server';
import Table, { TableContents, tableAttributes } from '@/components/Tables/Table/Table';
import type { TableDefinition } from '@/components/Tables/Table/Table.types';
import PluginTable from '@/components/Tables/PluginTable/PluginTable';
import type { TableInitializer } from '@/components/Tables/PluginTable/PluginTable.types';
import TableLabel from '@/components/UI/TableLabel/TableLabel';
import './FooTable.plugin.css';
import './FooTable.css';
const scripts = ['/assets/react/plugins/footable/footable.all.min.js'];
export type FooTableMode =
  | 'filter'
  | 'rows'
  | 'toggle'
  | 'pagination'
  | 'accordion';
export default function FooTable({
  definition,
  mode = 'toggle',
}: {
  definition: TableDefinition;
  mode?: FooTableMode;
}): React.JSX.Element {
  const id = 'foo-' + useId().replace(/:/g, '');
  const initialize = useCallback<TableInitializer>(
    ($, host, table) => {
      const collection = $(table);
      collection.footable();
      const api = collection.data('footable');
      const search = host.querySelector<HTMLInputElement>('[data-search]');
      const status = host.querySelector<HTMLSelectElement>('[data-status]');
      const size = host.querySelector<HTMLSelectElement>('[data-size]');
      const filter = (): void => {
        collection.trigger('footable_filter', {
          filter: [search?.value, status?.value].filter(Boolean).join(' '),
        });
      };
      const resize = (): void => {
        collection.data('page-size', Number(size?.value));
        collection.trigger('footable_initialized');
      };
      search?.addEventListener('input', filter);
      status?.addEventListener('change', filter);
      size?.addEventListener('change', resize);
      if (mode === 'accordion')
        collection.on(
          'footable_row_expanded.react',
          (event: { row: HTMLElement }) => {
            table
              .querySelectorAll('tbody tr.footable-detail-show')
              .forEach((row) => {
                if (row !== event.row) api.toggleDetail(row);
              });
          },
        );
      const click = (event: MouseEvent): void => {
        const target = event.target as HTMLElement;
        const remove = target.closest('[data-delete-row]');
        if (remove) {
          const row = remove.closest('tr');
          if (row) api.removeRow($(row));
        }
        if (target.closest('[data-add-row]')) {
          api.appendRow(
            renderToStaticMarkup(
              <tr>
                <td>
                  <button
                    className="btn btn-danger btn-xs btn-icon fa fa-times"
                    data-delete-row
                    aria-label="Excluir linha"
                  />
                </td>
                <td>Adam</td>
                <td>Doe</td>
                <td>Árbitro de trânsito</td>
                <td>22 Jun 1972</td>
                <td>
                  <TableLabel>Ativo</TableLabel>
                </td>
              </tr>,
            ),
          );
        }
      };
      host.addEventListener('click', click);
      return () => {
        host.removeEventListener('click', click);
        search?.removeEventListener('input', filter);
        status?.removeEventListener('change', filter);
        size?.removeEventListener('change', resize);
        collection.off('.react');
        if (api.destroy) api.destroy();
      };
    },
    [mode],
  );
  const paged = ['filter', 'rows', 'pagination'].includes(mode);
  return (
    <PluginTable
      scripts={scripts}
      initialize={initialize}
      className={`fooTable fooTable--${mode}`}
    >
      {mode === 'filter' && (
        <div className="form-inline m-b-20">
          <div className="row">
            <div className="col-sm-6">
              <div className="form-group">
                <label htmlFor={`${id}-status`} className="fooTable__statusLabel">Status</label>{' '}
                <select id={`${id}-status`} data-status className="form-control input-sm">
                  <option value="">Mostrar todos</option>
                  <option value="Ativo">Ativo</option>
                  <option value="Desativado">Desativado</option>
                  <option value="Suspenso">Suspenso</option>
                </select>
              </div>
            </div>
            <div className="col-sm-6 text-right">
              <div className="form-group">
                <input
                  data-search
                  className="form-control input-sm"
                  placeholder="Buscar"
                  aria-label="Buscar registros"
                />
              </div>
            </div>
          </div>
        </div>
      )}
      {mode === 'rows' && (
        <div className="form-inline">
          <div className="row">
            <div className="col-sm-6">
              <div className="form-group">
                <button className="btn btn-default m-b-20" data-add-row>
                  <i className="fa fa-plus fooTable__addIcon" aria-hidden="true" />{' '}
                  Adicionar linha
                </button>
              </div>
            </div>
            <div className="col-sm-6 text-right">
              <div className="form-group">
                <input
                  data-search
                  className="form-control input-sm"
                  placeholder="Buscar"
                  aria-label="Buscar registros"
                />
              </div>
            </div>
          </div>
        </div>
      )}
      {mode === 'pagination' && (
        <div className="form-inline">
          <label>
            Exibir{' '}
            <select
              data-size
              className="form-control input-sm"
              defaultValue="5"
            >
              {[5, 10, 15, 20].map((n) => (
                <option key={n}>{n}</option>
              ))}
            </select>{' '}
            registros
          </label>
        </div>
      )}
      <Table {...tableAttributes(definition.attributes)} id={id}>
        <TableContents
          definition={definition}
          actions={
            <button
              data-delete-row
              className="btn btn-danger btn-xs btn-icon fa fa-times"
              aria-label="Excluir linha"
            />
          }
          footer={
            paged ? (
              <tr>
                <td colSpan={definition.head[0].length}>
                  <div className="text-right">
                    <ul
                      className={`pagination pagination-split m-t-30 ${mode === 'filter' ? 'm-b-0' : ''}`}
                    />
                  </div>
                </td>
              </tr>
            ) : undefined
          }
        />
      </Table>
    </PluginTable>
  );
}
