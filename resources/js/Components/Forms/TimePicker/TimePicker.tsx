import { useEffect, useRef } from 'react';
import TextInput from '@/Components/Forms/TextInput/TextInput';
import { loadExternalScript } from '@/utils/loadExternalScript';
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
}: TimePickerProps): JSX.Element {
  const input = useRef<HTMLInputElement>(null);
  const callback = useRef(onChange);
  callback.current = onChange;
  useEffect(() => {
    let disposed = false;
    let instance: TimeCollection | undefined;
    async function initialize(): Promise<void> {
      await loadExternalScript('/assets/gnai/js/jquery.min.js');
      await loadExternalScript('/assets/gnai/js/bootstrap.min.js');
      await loadExternalScript(
        '/assets/gnai/plugins/timepicker/bootstrap-timepicker.min.js',
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
        ...(defaultValue ? { defaultTime: defaultValue } : {}),
      });
      instance.on('changeTime.timepicker.react', () =>
        callback.current?.(input.current?.value ?? ''),
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
  }, [showMeridian, minuteStep, defaultValue]);
  return (
    <div className="timePicker">
      <div className="input-group">
        <div className="bootstrap-timepicker">
          <TextInput
            ref={input}
            id={id}
            aria-label={label}
            className="form-control"
            defaultValue={defaultValue}
          />
        </div>
        <span className="input-group-addon">
          <i className="glyphicon glyphicon-time" />
        </span>
      </div>
    </div>
  );
}
