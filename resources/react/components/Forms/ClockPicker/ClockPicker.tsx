import { useEffect, useRef, useState } from "react";
import TextInput from "@/components/Forms/TextInput/TextInput";
import { loadExternalScript } from "@/utils/loadExternalScript";
import { loadExternalStylesheet } from "@/utils/loadExternalStylesheet";
import {
    maskTime,
    timeCaret,
} from "@/components/Forms/TimePicker/TimePicker.utils";
import "./ClockPicker.css";

interface ClockPickerCollection {
    clockpicker: (options?: Record<string, unknown> | string) => ClockPickerCollection;
    data: (key: string) => ClockPickerWidget | undefined;
    on: (events: string, callback: () => void) => ClockPickerCollection;
    off: (events: string) => ClockPickerCollection;
}

interface ClockPickerWidget {
    hours: number;
    minutes: number;
    spanHours: { html: (value: string) => void };
    spanMinutes: { html: (value: string) => void };
    toggleView: (view: "hours" | "minutes") => void;
}

export interface ClockPickerProps {
    id: string;
    label: string;
    defaultValue?: string;
    required?: boolean;
    onChange?: (value: string) => void;
}

export default function ClockPicker({
    id,
    label,
    defaultValue,
    required = false,
    onChange,
}: ClockPickerProps): React.JSX.Element {
    const input = useRef<HTMLInputElement>(null);
    const picker = useRef<ClockPickerWidget | null>(null);
    const callback = useRef(onChange);
    const [display, setDisplay] = useState(() => {
        const initial = maskTime(defaultValue ?? "");
        return initial.valid && initial.complete ? initial.value : "";
    });
    const [invalidEntry, setInvalidEntry] = useState(false);
    const lastValid = useRef(display);
    callback.current = onChange;

    function syncDial(value: string): void {
        const widget = picker.current;
        if (!widget) return;

        const [hourPart = "", minutePart = ""] = value.split(":");
        const hour = Number(hourPart);
        const minute = Number(minutePart);
        if (hourPart.length > 0 && Number.isFinite(hour)) {
            widget.hours = hour;
            widget.spanHours.html(String(hour).padStart(2, "0"));
        }
        if (minutePart.length > 0 && Number.isFinite(minute)) {
            widget.minutes = minute;
            widget.spanMinutes.html(String(minute).padStart(2, "0"));
        }

        widget.toggleView(minutePart.length > 0 || value.includes(":") ? "minutes" : "hours");
    }

    function applyTime(rawValue: string, caret?: number): void {
        const result = maskTime(rawValue);
        if (!result.valid) {
            setInvalidEntry(true);
            setDisplay(lastValid.current);
            callback.current?.(lastValid.current);
            return;
        }

        setInvalidEntry(false);
        lastValid.current = result.value;
        setDisplay(result.value);
        syncDial(result.value);
        callback.current?.(result.complete ? result.value : "");

        if (caret !== undefined) {
            const digitCount = rawValue.slice(0, caret).replace(/\D/g, "").length;
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
        let instance: ClockPickerCollection | undefined;

        async function initialize(): Promise<void> {
            await loadExternalStylesheet(
                "/assets/react/plugins/clockpicker/jquery-clockpicker.min.css",
            );
            await loadExternalScript("/assets/react/js/jquery.min.js");
            await loadExternalScript(
                "/assets/react/plugins/clockpicker/jquery-clockpicker.min.js",
            );
            if (disposed || !input.current) return;

            const $ = (
                window as unknown as {
                    jQuery: (element: HTMLElement) => ClockPickerCollection;
                }
            ).jQuery;
            instance = $(input.current);
            instance.clockpicker({
                twelvehour: false,
                autoclose: false,
                default: display,
                donetext: "Concluído",
            });
            picker.current = instance.data("clockpicker") ?? null;
            instance.on("change.clockpicker.react", () =>
                applyTime(input.current?.value ?? ""),
            );
        }

        void initialize().catch(console.error);
        return () => {
            disposed = true;
            picker.current = null;
            instance?.off(".react");
            instance?.clockpicker("remove");
        };
    }, []);

    const description = invalidEntry ? `${id}-time-error` : undefined;

    return (
        <div className="clockPicker">
            <div className="input-group">
                <TextInput
                    ref={input}
                    id={id}
                    aria-label={label}
                    className="form-control"
                    value={display}
                    required={required}
                    inputMode="numeric"
                    autoComplete="off"
                    invalid={invalidEntry}
                    aria-describedby={description}
                    onChange={(event) =>
                        applyTime(
                            event.currentTarget.value,
                            event.currentTarget.selectionStart ?? event.currentTarget.value.length,
                        )
                    }
                />
                <span className="input-group-addon" aria-hidden="true">
                    <i className="glyphicon glyphicon-time" />
                </span>
            </div>
            {invalidEntry ? (
                <span id={`${id}-time-error`} role="alert" className="clockPicker__error">
                    Informe um horário entre 00:00 e 23:59.
                </span>
            ) : null}
        </div>
    );
}
