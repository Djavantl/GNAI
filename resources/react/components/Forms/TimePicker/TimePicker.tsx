import { useEffect, useRef, useState } from 'react';
import TextInput from '@/components/Forms/TextInput/TextInput';
import { loadExternalScript } from '@/utils/loadExternalScript';
import { maskTime, timeCaret } from './TimePicker.utils';
import './TimePicker.plugin.css';
import './TimePicker.css';
export interface TimePickerProps {
  id?: string;
  label: string;
  showMeridian?: boolean;
  minuteStep?: number;
  defaultValue?: string;
  onChange?: (value: string) => void;
}
interface TimeCollection {
  timepicker: (options: Record<string, unknown> | string) => TimeCollection;
  on: (event: string, callback: () => void) => TimeCollection;
  off: (event: string) => TimeCollection;
}
export default function TimePicker({
  id,
  label,
  showMeridian = true,
  minuteStep = 15,
  defaultValue,
  onChange,
}: TimePickerProps): React.JSX.Element {
  const input = useRef<HTMLInputElement>(null);
  const callback = useRef(onChange);
  const [invalidEntry, setInvalidEntry] = useState(false);
  const initialTime = maskTime(defaultValue ?? '', showMeridian);
  const lastValid = useRef(initialTime.valid ? initialTime.value : '');
  callback.current = onChange;

  function applyTime(rawValue: string, caret?: number): void {
    const result = maskTime(rawValue, showMeridian);
    if (!result.valid) {
      setInvalidEntry(true);
      if (input.current) input.current.value = lastValid.current;
      return;
    }

    setInvalidEntry(false);
    lastValid.current = result.value;
    if (input.current) input.current.value = result.value;
    callback.current?.(result.complete ? result.value : '');

    if (caret !== undefined) {
      const digitCount = rawValue.slice(0, caret).replace(/\D/g, '').length;
      requestAnimationFrame(() => {
        if (document.activeElement === input.current) {
          const nextCaret = timeCaret(result.value, digitCount, caret === rawValue.length);
          input.current?.setSelectionRange(nextCaret, nextCaret);
        }
      });
    }
  }
  useEffect(() => {
    let disposed = false;
    let instance: TimeCollection | undefined;
    async function initialize(): Promise<void> {
      await loadExternalScript('/assets/react/js/jquery.min.js');
      await loadExternalScript('/assets/react/js/bootstrap.min.js');
      await loadExternalScript(
        '/assets/react/plugins/timepicker/bootstrap-timepicker.min.js',
      );
      if (disposed || !input.current) return;
      const $ = (
        window as unknown as {
          jQuery: (element: HTMLElement) => TimeCollection;
        }
      ).jQuery;
      instance = $(input.current);
      instance.timepicker({
        showMeridian,
        minuteStep,
        ...(lastValid.current ? { defaultTime: lastValid.current } : {}),
      });
      instance.on('changeTime.timepicker.react', () =>
        applyTime(input.current?.value ?? ''),
      );
    }
    void initialize().catch(console.error);
    return () => {
      disposed = true;
      instance?.off('.react');
      instance?.timepicker('hideWidget');
      instance?.timepicker('remove');
      instance?.off('.timepicker');
    };
  }, [showMeridian, minuteStep]);
  const errorId = id ? `${id}-time-error` : undefined;
  return (
    <div className="timePicker">
      <div className="input-group">
        <div className="bootstrap-timepicker">
          <TextInput
            ref={input}
            id={id}
            aria-label={label}
            className="form-control"
            defaultValue={lastValid.current}
            inputMode={showMeridian ? 'text' : 'numeric'}
            invalid={invalidEntry}
            aria-describedby={invalidEntry ? errorId : undefined}
            onChange={event =>
              applyTime(
                event.currentTarget.value,
                event.currentTarget.selectionStart ?? event.currentTarget.value.length,
              )
            }
          />
        </div>
        <span className="input-group-addon">
          <i className="glyphicon glyphicon-time" />
        </span>
      </div>
      {invalidEntry ? <span id={errorId} role="alert" className="timePicker__error">Informe um horário válido.</span> : null}
    </div>
  );
}
