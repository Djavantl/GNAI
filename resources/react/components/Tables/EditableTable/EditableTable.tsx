import { useCallback, useId, useRef } from 'react';
import { renderToStaticMarkup } from 'react-dom/server';
import Table, { TableContents, tableAttributes } from '@/components/Tables/Table/Table';
import type { TableDefinition } from '@/components/Tables/Table/Table.types';
import PluginTable from '@/components/Tables/PluginTable/PluginTable';
import type { TableInitializer } from '@/components/Tables/PluginTable/PluginTable.types';
import {
  dataTableScripts,
  dataTablePortuguese,
} from '@/components/Tables/SearchableTable/SearchableTable';
import TextInput from '@/components/Forms/TextInput/TextInput';
import './EditableTable.css';
const scripts = [
  ...dataTableScripts,
  '/assets/react/plugins/magnific-popup/dist/jquery.magnific-popup.min.js',
];
const actions = (
  <>
    <a href="#" className="hidden on-editing save-row" aria-label="Salvar">
      <i className="fa fa-save" />
    </a>{' '}
    <a href="#" className="hidden on-editing cancel-row" aria-label="Cancelar">
      <i className="fa fa-times" />
    </a>{' '}
    <a href="#" className="on-default edit-row" aria-label="Editar">
      <i className="fa fa-pencil" />
    </a>{' '}
    <a href="#" className="on-default remove-row" aria-label="Excluir">
      <i className="fa fa-trash-o" />
    </a>
  </>
);
export default function EditableTable({
  definition,
  onChange,
}: {
  definition: TableDefinition;
  onChange?: (rows: string[][]) => void;
}): React.JSX.Element {
  const id = 'editable-dialog-' + useId().replace(/:/g, '');
  const callback = useRef(onChange);
  callback.current = onChange;
  const initialize = useCallback<TableInitializer>(
    ($, host, table) => {
      const api = $(table).DataTable({
        language: dataTablePortuguese,
        columnDefs: [{ targets: -1, orderable: false }],
      });
      const add = host.querySelector<HTMLButtonElement>('[data-add]')!;
      const dialog = host.querySelector<HTMLElement>('[data-dialog]')!;
      const popup = (
        $ as unknown as {
          magnificPopup: {
            open: (options: unknown) => void;
            close: () => void;
          };
        }
      ).magnificPopup;
      let pending: HTMLTableRowElement | null = null;
      const publish = (): void =>
        callback.current?.(
          api
            .rows()
            .data()
            .toArray()
            .map((row: string[]) => row.slice(0, -1)),
        );
      const edit = (row: HTMLTableRowElement): void => {
        const values = Array.from(row.cells).map(
          (cell) => cell.textContent ?? '',
        );
        Array.from(row.cells).forEach((cell, index) => {
          if (index === row.cells.length - 1) {
            cell.innerHTML = renderToStaticMarkup(actions);
            cell
              .querySelectorAll('.on-default')
              .forEach((e) => e.classList.add('hidden'));
            cell
              .querySelectorAll('.on-editing')
              .forEach((e) => e.classList.remove('hidden'));
          } else
            cell.innerHTML = renderToStaticMarkup(
              <TextInput
                className="form-control input-block"
                aria-label={definition.head[0][index].text}
                defaultValue={values[index]}
              />,
            );
        });
      };
      const click = (event: MouseEvent): void => {
        const target = event.target as HTMLElement;
        const link = target.closest('a');
        const row = target.closest('tr');
        if (target.closest('[data-add]')) {
          add.disabled = true;
          const newRow = api.row
            .add([
              ...definition.head[0].slice(0, -1).map(() => ''),
              renderToStaticMarkup(actions),
            ])
            .draw()
            .node() as HTMLTableRowElement;
          newRow.classList.add('adding');
          newRow.cells[newRow.cells.length - 1].classList.add('actions');
          edit(newRow);
          api.order([0, 'asc']).draw();
        }
        if (!link || !row) return;
        event.preventDefault();
        if (link.classList.contains('edit-row')) edit(row);
        if (link.classList.contains('save-row')) {
          const values = Array.from(row.querySelectorAll('input')).map(
            (input) => input.value.trim(),
          );
          api
            .row(row)
            .data([
              ...values.map((value) => renderToStaticMarkup(<>{value}</>)),
              renderToStaticMarkup(actions),
            ])
            .draw();
          if (row.classList.contains('adding')) {
            row.classList.remove('adding');
            add.disabled = false;
          }
          publish();
        }
        if (link.classList.contains('cancel-row')) {
          if (row.classList.contains('adding')) {
            api.row(row).remove().draw();
            add.disabled = false;
          } else api.row(row).data(api.row(row).data()).draw();
        }
        if (link.classList.contains('remove-row')) {
          pending = row;
          popup.open({
            items: { src: '#' + id, type: 'inline' },
            preloader: false,
            modal: true,
          });
        }
      };
      const dialogClick = (event: MouseEvent): void => {
        const target = event.target as HTMLElement;
        if (target.closest('[data-confirm]') && pending) {
          api.row(pending).remove().draw();
          publish();
          popup.close();
          pending = null;
        }
        if (target.closest('[data-cancel]')) {
          popup.close();
          pending = null;
        }
      };
      host.addEventListener('click', click);
      dialog.addEventListener('click', dialogClick);
      return () => {
        popup.close();
        host.removeEventListener('click', click);
        dialog.removeEventListener('click', dialogClick);
        api.destroy();
      };
    },
    [definition, id],
  );
  return (
    <PluginTable
      scripts={scripts}
      initialize={initialize}
      className="editableTable"
    >
      <div className="m-b-30">
        <button className="btn btn-default waves-effect waves-light" data-add>
          Adicionar <i className="fa fa-plus" />
        </button>
      </div>
      <Table {...tableAttributes(definition.attributes)}>
        <TableContents definition={definition} actions={actions} />
      </Table>
      <div
        id={id}
        data-dialog
        className="editableTable__dialog tableSurface mfp-hide"
      >
        <section className="editableTable__panel">
          <header>
            <h2>Tem certeza?</h2>
          </header>
          <div className="editableTable__body">
            <p>Deseja realmente excluir esta linha?</p>
          </div>
          <footer>
            <button
              className="btn btn-primary waves-effect waves-light"
              data-confirm
            >
              Confirmar
            </button>{' '}
            <button className="btn btn-default waves-effect" data-cancel>
              Cancelar
            </button>
          </footer>
        </section>
      </div>
    </PluginTable>
  );
}
