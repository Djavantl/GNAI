import { useEffect, useLayoutEffect, useRef, useState } from 'react';
import TextInput from '@/components/Forms/TextInput/TextInput';
import { loadExternalScript } from '@/utils/loadExternalScript';
import { loadExternalStylesheet } from '@/utils/loadExternalStylesheet';
import {
  backendDate,
  dateCaret,
  displayDate,
  isValidDatePrefix,
  maskDate,
} from './DatePicker.utils';
import './DatePicker.css';

interface DatePickerProps {
  id: string;
  name: string;
  label: string;
  value: string;
  onChange: (value: string) => void;
  required?: boolean;
  disabled?: boolean;
  invalid?: boolean;
  describedBy?: string;
}
interface DatePickerCollection {
  datepicker: (options: Record<string, unknown> | string) => DatePickerCollection;
  on: (events: string, handler: () => void) => DatePickerCollection;
  off: (events: string) => DatePickerCollection;
  data: (key: string) => { picker?: { 0?: HTMLElement } } | undefined;
}

/** Adapter for the same Bootstrap Datepicker used by the FormPickers catalog. */
export default function DatePicker({
  id,
  name,
  label,
  value,
  onChange,
  required = false,
  disabled = false,
  invalid = false,
  describedBy,
}: DatePickerProps): React.JSX.Element {
  const input = useRef<HTMLInputElement>(null);
  const instance = useRef<DatePickerCollection | undefined>(undefined);
  const [display, setDisplay] = useState(() => displayDate(value));
  const [loadFailed, setLoadFailed] = useState(false);
  const [invalidEntry, setInvalidEntry] = useState(false);
  const callback = useRef(onChange);
  callback.current = onChange;
  const blocked = useRef(disabled);
  blocked.current = disabled;
  const syncing = useRef(false);
  const pendingCaret = useRef<number | null>(null);

  function editDate(rawValue: string, caret: number): void {
    const formatted = maskDate(rawValue);
    if (!isValidDatePrefix(formatted)) {
      setInvalidEntry(true);
      callback.current('');
      return;
    }

    setInvalidEntry(false);
    pendingCaret.current = dateCaret(
      formatted,
      rawValue.slice(0, caret).replace(/\D/g, '').length,
    );
    setDisplay(formatted);
    callback.current(backendDate(formatted));
  }
  useLayoutEffect(() => {
    if (pendingCaret.current === null || document.activeElement !== input.current) return;
    input.current?.setSelectionRange(pendingCaret.current, pendingCaret.current);
    pendingCaret.current = null;
  });

  useEffect(() => { setDisplay(displayDate(value)); }, [value]);
  useEffect(() => {
    syncing.current = true;
    instance.current?.datepicker('update');
    syncing.current = false;
  }, [display]);
  useEffect(() => { if (disabled) instance.current?.datepicker('hide'); }, [disabled]);

  useEffect(() => {
    let cancelled = false;
    let picker: DatePickerCollection | undefined;
    async function initialize(): Promise<void> {
      await loadExternalStylesheet('/assets/react/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker3.standalone.min.css');
      await loadExternalScript('/assets/react/js/jquery.min.js');
      await loadExternalScript('/assets/react/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js');
      await loadExternalScript('/assets/react/plugins/bootstrap-datepicker/dist/locales/bootstrap-datepicker.pt-BR.min.js');
      if (cancelled || !input.current) return;
      const runtime = (window as Window & { jQuery?: (element: HTMLElement) => DatePickerCollection }).jQuery;
      if (!runtime) throw new Error('Date Picker indisponível.');
      picker = runtime(input.current);
      picker.datepicker({ language: 'pt-BR', format: 'dd/mm/yyyy', autoclose: true, todayHighlight: true,
        clearBtn: true, forceParse: false, enableOnReadonly: false });
      picker.data('datepicker')?.picker?.[0]?.classList.add('datePickerPopup');
      picker.on('changeDate.gnaiDatePicker clearDate.gnaiDatePicker', () => {
        if (cancelled || syncing.current || blocked.current) return;
        const selected = input.current?.value ?? '';
        setInvalidEntry(false);
        setDisplay(selected);
        callback.current(backendDate(selected));
      });
      instance.current = picker;
    }
    void initialize().catch(() => { if (!cancelled) setLoadFailed(true); });
    return () => {
      cancelled = true;
      picker?.off('.gnaiDatePicker');
      picker?.datepicker('remove');
      instance.current = undefined;
    };
  }, []);

  const description = [
    describedBy,
    invalidEntry ? `${id}-date-error` : undefined,
    loadFailed ? `${id}-picker-error` : undefined,
  ].filter(Boolean).join(' ') || undefined;
  return <div className="datePicker">
    <div className="datePicker__control">
      <TextInput ref={input} id={id} name={name} value={display} required={required} disabled={disabled}
        invalid={invalid || invalidEntry} placeholder="DD/MM/AAAA" inputMode="numeric" autoComplete="off" aria-describedby={description}
        onChange={event =>
          editDate(
            event.target.value,
            event.target.selectionStart ?? event.target.value.length,
          )
        }
        onKeyDown={event => {
          const control = event.currentTarget;
          const start = control.selectionStart ?? 0;
          if (start !== control.selectionEnd) return;
          // Delete the adjacent digit when the caret crosses an automatic slash.
          if (event.key === 'Backspace' && control.value[start - 1] === '/') {
            event.preventDefault();
            editDate(control.value.slice(0, start - 2) + control.value.slice(start), start - 2);
          } else if (event.key === 'Delete' && control.value[start] === '/') {
            event.preventDefault();
            editDate(control.value.slice(0, start) + control.value.slice(start + 2), start);
          }
        }} />
      <button type="button" className="datePicker__toggle" disabled={disabled || loadFailed}
        aria-label={`Abrir calendário: ${label}`} onClick={() => { input.current?.focus(); instance.current?.datepicker('show'); }}>
        <i className="icon-calender" aria-hidden="true" />
      </button>
    </div>
    {invalidEntry ? <span id={`${id}-date-error`} role="alert" className="datePicker__error">Informe uma data real no formato DD/MM/AAAA.</span> : null}
    {loadFailed ? <span id={`${id}-picker-error`} role="alert" className="datePicker__error">Não foi possível carregar o calendário. Digite a data no formato DD/MM/AAAA.</span> : null}
  </div>;
}
