import { useEffect, useRef, useState } from "react";
import { joinClassNames } from "@/utils/joinClassNames";
import { loadExternalScript } from "@/utils/loadExternalScript";
import { loadExternalStylesheet } from "@/utils/loadExternalStylesheet";
import "./RangeSlider.css";

type IonSliderData = { from: number };

interface IonSliderInstance {
    update: (options: Record<string, unknown>) => void;
    destroy: () => void;
    plugin_count?: number;
    current_plugin?: number;
    result?: { from: number };
}

interface IonCollection {
    ionRangeSlider: (options: Record<string, unknown>) => IonCollection;
    data: (key: string) => unknown;
}

type IonRuntime = ((element: Element) => IonCollection) & { fn?: { ionRangeSlider?: unknown } };

const stylesheets = [
    "/assets/react/plugins/ion-rangeslider/ion.rangeSlider.css",
    "/assets/react/plugins/ion-rangeslider/ion.rangeSlider.skinFlat.css",
];

export interface RangeSliderProps {
    id: string;
    /** Nome acessível do controle; use o mesmo texto do label visível. */
    label: string;
    name?: string;
    min: number;
    max: number;
    step?: number;
    value: number;
    grid?: boolean;
    /** Atualiza a prévia durante o arraste; o padrão preserva a entrega ao soltar. */
    live?: boolean;
    disabled?: boolean;
    invalid?: boolean;
    describedBy?: string;
    className?: string;
    onChange: (value: number) => void;
}

/**
 * Adaptador controlado do Ion.RangeSlider do Ubold, com as cores do Gnai.
 * Por padrão, entrega o valor ao soltar, clicar ou usar as setas. A opção
 * live também atualiza durante o arraste, sem interromper o gesto.
 * A trilha recebe foco e responde às setas, expondo `role="slider"`.
 */
export default function RangeSlider({
    id,
    label,
    name,
    min,
    max,
    step = 1,
    value,
    grid = true,
    live = false,
    disabled = false,
    invalid = false,
    describedBy,
    className,
    onChange,
}: RangeSliderProps): React.JSX.Element {
    const [loadFailed, setLoadFailed] = useState(false);
    const root = useRef<HTMLDivElement>(null);
    const input = useRef<HTMLInputElement>(null);
    const instance = useRef<IonSliderInstance | null>(null);
    const callback = useRef(onChange);
    const interacting = useRef(false);
    const current = useRef({ value, disabled, label, describedBy, invalid, live });
    callback.current = onChange;
    current.current = { value, disabled, label, describedBy, invalid, live };

    function describeTrack(): void {
        const track = root.current?.querySelector<HTMLElement>(".irs-line");
        if (!track) return;
        const state = current.current;
        track.setAttribute("role", "slider");
        track.setAttribute("tabindex", state.disabled ? "-1" : "0");
        track.setAttribute("aria-label", state.label);
        track.setAttribute("aria-valuemin", String(min));
        track.setAttribute("aria-valuemax", String(max));
        track.setAttribute("aria-valuenow", String(state.value));
        track.setAttribute("aria-disabled", String(state.disabled));
        if (state.invalid) track.setAttribute("aria-invalid", "true");
        else track.removeAttribute("aria-invalid");
        if (state.describedBy) track.setAttribute("aria-describedby", state.describedBy);
        else track.removeAttribute("aria-describedby");
        // O plugin só aceita as setas na instância ativada por ponteiro; o foco por Tab também a ativa.
        track.onfocus = () => {
            const slider = instance.current;
            if (slider) slider.current_plugin = slider.plugin_count;
        };
    }

    useEffect(() => {
        let disposed = false;

        async function initialize(): Promise<void> {
            await Promise.all(stylesheets.map((href) => loadExternalStylesheet(href)));
            await loadExternalScript("/assets/react/js/jquery.min.js");
            await loadExternalScript("/assets/react/plugins/ion-rangeslider/ion.rangeSlider.min.js");
            const $ = (window as unknown as { jQuery?: IonRuntime }).jQuery;
            if (disposed || !input.current || !$?.fn?.ionRangeSlider) return;

            $(input.current).ionRangeSlider({
                min,
                max,
                step,
                from: current.current.value,
                grid,
                keyboard: true,
                keyboard_step: (step / (max - min)) * 100,
                hide_min_max: grid,
                disable: current.current.disabled,
                onChange: (data: IonSliderData) => {
                    root.current?.querySelector(".irs-line")?.setAttribute("aria-valuenow", String(data.from));
                    if (current.current.live && data.from !== current.current.value) callback.current(data.from);
                },
                onFinish: (data: IonSliderData) => {
                    interacting.current = false;
                    if (data.from !== current.current.value) callback.current(data.from);
                },
            });
            instance.current = $(input.current).data("ionRangeSlider") as IonSliderInstance;
            describeTrack();
        }

        void initialize().catch(() => {
            if (!disposed) setLoadFailed(true);
        });

        return () => {
            disposed = true;
            instance.current?.destroy();
            instance.current = null;
        };
    }, [min, max, step, grid]);

    useEffect(() => {
        const finish = (): void => {
            interacting.current = false;
        };
        window.addEventListener("pointerup", finish);
        window.addEventListener("pointercancel", finish);

        return () => {
            window.removeEventListener("pointerup", finish);
            window.removeEventListener("pointercancel", finish);
        };
    }, []);

    useEffect(() => {
        const slider = instance.current;
        if (!slider || interacting.current) return;
        const track = root.current?.querySelector(".irs-line");
        const hadFocus = !!track && track === document.activeElement;
        if (slider.result?.from !== value || track?.getAttribute("aria-disabled") !== String(disabled)) {
            slider.update({ from: value, disable: disabled });
            if (hadFocus) root.current?.querySelector<HTMLElement>(".irs-line")?.focus();
        }
        describeTrack();
    }, [value, disabled, label, describedBy, invalid]);

    return (
        <div
            ref={root}
            onPointerDownCapture={() => {
                interacting.current = true;
            }}
            className={joinClassNames(
                "rangeSlider",
                invalid ? "rangeSlider--invalid" : undefined,
                disabled ? "rangeSlider--disabled" : undefined,
                className,
            )}
        >
            {loadFailed ? (
                <input
                    id={id}
                    name={name}
                    type="range"
                    min={min}
                    max={max}
                    step={step}
                    value={value}
                    disabled={disabled}
                    aria-label={label}
                    aria-invalid={invalid || undefined}
                    aria-describedby={describedBy}
                    onChange={(event) => callback.current(Number(event.target.value))}
                />
            ) : (
                <input ref={input} id={id} name={name} type="text" defaultValue={value} readOnly tabIndex={-1} aria-hidden="true" />
            )}
        </div>
    );
}
