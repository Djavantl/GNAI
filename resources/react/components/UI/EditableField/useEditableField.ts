import { useEffect, useRef, type RefObject } from 'react';
import { loadExternalScript } from '@/utils/loadExternalScript';
import type { EditableCollection, EditableFieldProps, EditableRuntime } from './EditableField.types';

const months = ['jan', 'fev', 'mar', 'abr', 'mai', 'jun', 'jul', 'ago', 'set', 'out', 'nov', 'dez'];
export function useEditableField(host: RefObject<HTMLSpanElement | null>, props: EditableFieldProps): void {
  const { id, type = 'text', mode = 'inline', defaultValue = '', initialText, title, placeholder, required, options, sourceUrl, placement = 'top' } = props;
  const callback = useRef(props.onChange);
  callback.current = props.onChange;
  useEffect(() => {
    let cancelled = false;
    let collection: EditableCollection | undefined;
    let anchor: HTMLAnchorElement | undefined;
    async function initialize(): Promise<void> {
      for (const src of ['/assets/react/js/jquery.min.js', '/assets/react/js/bootstrap.min.js', '/assets/react/plugins/moment/moment.js', '/assets/react/plugins/x-editable/js/bootstrap-editable.min.js']) await loadExternalScript(src);
      if (cancelled || !host.current) return;
      const runtime = (window as Window & { jQuery?: EditableRuntime }).jQuery;
      if (!runtime) throw new Error('jQuery indisponível.');
      runtime.fn.editableform.buttons = '<button type="submit" aria-label="Salvar" class="btn btn-primary editable-submit btn-sm waves-effect waves-light"><i class="md md-done"></i></button><button type="button" aria-label="Cancelar" class="btn editable-cancel btn-sm waves-effect waves-light"><i class="md md-clear"></i></button>';
      anchor = document.createElement('a');
      anchor.href = '#'; anchor.id = id;
      anchor.textContent = initialText ?? (type === 'combodate' ? '' : String(defaultValue));
      anchor.setAttribute('aria-label', title);
      host.current.append(anchor);
      collection = runtime(anchor);
      collection.editable({
        type, mode, title, placeholder, placement, pk: 1, value: defaultValue,
        emptytext: 'Vazio', sourceError: 'Erro ao carregar a lista', send: 'never',
        ...(sourceUrl ? { source: sourceUrl } : {}),
        ...(required ? { validate: (value: unknown): string | undefined => value !== null && value !== undefined && String(value).trim() ? undefined : 'Este campo é obrigatório' } : {}),
        ...(type === 'textarea' ? { showbuttons: 'bottom' } : {}),
        ...(type === 'combodate' ? { format: 'YYYY-MM-DD', viewformat: 'DD/MM/YYYY', template: 'D / MMM / YYYY' } : {}),
        ...(options ? { prepend: 'não selecionado', source: options, display: function(this: HTMLAnchorElement, value: string | number): void {
          const selected = options.find(option => String(option.value) === String(value));
          this.textContent = selected?.text ?? 'não selecionado';
          this.style.color = selected?.color ?? '#98a6ad';
        } } : {})
      });
      collection.on('save.editableReact', (_event, params) => {
        const value = params.newValue;
        if (type === 'combodate' && value && typeof value === 'object' && 'format' in value) callback.current?.((value as { format: (pattern: string) => string }).format('YYYY-MM-DD'));
        else if (typeof value === 'string' || typeof value === 'number') callback.current?.(value);
        else if (value === null) callback.current?.('');
      });
      collection.on('shown.editableReact', () => {
        host.current?.querySelectorAll<HTMLInputElement>('input,select,textarea').forEach(input => input.setAttribute('aria-label', title));
        host.current?.querySelectorAll<HTMLOptionElement>('.month option').forEach(option => { if (option.value !== '') option.textContent = months[Number(option.value)]; });
      });
    }
    void initialize().catch((error: unknown) => console.error('Não foi possível carregar a edição inline.', error));
    return () => { cancelled = true; collection?.off('.editableReact').editable('destroy'); anchor?.remove(); };
  }, [host, id, type, mode, defaultValue, initialText, title, placeholder, required, options, sourceUrl, placement]);
}
