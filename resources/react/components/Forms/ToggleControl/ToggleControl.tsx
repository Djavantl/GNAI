import { useEffect, useRef, type ReactNode } from "react";
import { loadExternalScript } from "@/utils/loadExternalScript";
import { loadExternalStylesheet } from "@/utils/loadExternalStylesheet";
import "./ToggleControl.css";

interface SwitcheryInstance {
    switcher: HTMLElement;
    setPosition: () => void;
    enable: () => void;
    disable: () => void;
    destroy: () => void;
}
interface SwitcheryConstructor {
    new (
        element: HTMLInputElement,
        options: Record<string, unknown>,
    ): SwitcheryInstance;
}
interface ToggleControlProps {
    id: string;
    name?: string;
    label: string;
    labelAddon?: ReactNode;
    description?: string;
    checked: boolean;
    disabled?: boolean;
    size?: "small" | "default" | "large";
    onChange: (checked: boolean) => void;
}

export default function ToggleControl({
    id,
    name,
    label,
    labelAddon,
    description,
    checked,
    disabled = false,
    size = "default",
    onChange,
}: ToggleControlProps): React.JSX.Element {
    const inputRef = useRef<HTMLInputElement>(null);
    const instanceRef = useRef<SwitcheryInstance | null>(null);
    const onChangeRef = useRef(onChange);
    const checkedRef = useRef(checked);
    const disabledRef = useRef(disabled);
    onChangeRef.current = onChange;
    checkedRef.current = checked;
    disabledRef.current = disabled;
    const descriptionId = description ? `${id}-description` : undefined;

    useEffect(() => {
        let cancelled = false;
        let switchery: SwitcheryInstance | null = null;
        let switchElement: HTMLElement | null = null;
        const input = inputRef.current;
        if (!input) return;

        const synchronize = (): void => onChangeRef.current(input.checked);
        const handleKeyDown = (event: KeyboardEvent): void => {
            if (
                (event.key === " " || event.key === "Enter") &&
                !input.disabled
            ) {
                event.preventDefault();
                switchElement?.click();
            }
        };
        input.addEventListener("change", synchronize);
        async function initialize(): Promise<void> {
            await loadExternalStylesheet(
                "/assets/react/plugins/switchery/switchery.css",
            );
            await loadExternalScript(
                "/assets/react/plugins/switchery/browser/switchery.min.js",
            );
            if (cancelled) return;
            const Switchery = (
                window as Window & { Switchery?: SwitcheryConstructor }
            ).Switchery;
            if (!Switchery) return;
            input.checked = checkedRef.current;
            input.disabled = disabledRef.current;
            switchery = new Switchery(input, {
                color:
                    getComputedStyle(document.documentElement)
                        .getPropertyValue("--ui-color-gnai-primary")
                        .trim() || "#322b75",
                secondaryColor: "#dfdfdf",
                size,
            });
            instanceRef.current = switchery;
            switchElement = switchery.switcher;
            switchElement.setAttribute("role", "switch");
            switchElement.setAttribute(
                "tabindex",
                disabledRef.current ? "-1" : "0",
            );
            switchElement.setAttribute(
                "aria-checked",
                String(checkedRef.current),
            );
            switchElement.setAttribute(
                "aria-disabled",
                String(disabledRef.current),
            );
            switchElement.setAttribute("aria-labelledby", `${id}-label`);
            if (descriptionId)
                switchElement.setAttribute("aria-describedby", descriptionId);
            switchElement.addEventListener("keydown", handleKeyDown);
        }
        void initialize();
        return () => {
            cancelled = true;
            input.removeEventListener("change", synchronize);
            switchElement?.removeEventListener("keydown", handleKeyDown);
            switchery?.destroy();
            switchElement?.remove();
            input.style.removeProperty("display");
            input.removeAttribute("data-switchery");
            instanceRef.current = null;
        };
    }, [descriptionId, id, size]);

    useEffect(() => {
        const input = inputRef.current;
        const instance = instanceRef.current;
        if (!input || !instance) return;
        if (input.checked !== checked) {
            input.checked = checked;
            instance.setPosition();
        }
        instance.switcher.setAttribute("aria-checked", String(checked));
    }, [checked]);

    useEffect(() => {
        const instance = instanceRef.current;
        if (!instance) return;
        if (disabled) instance.disable();
        else instance.enable();
        instance.switcher.setAttribute("tabindex", disabled ? "-1" : "0");
        instance.switcher.setAttribute("aria-disabled", String(disabled));
    }, [disabled]);

    return (
        <div className="toggleControl">
            <div className="toggleControl__text">
                <div className="toggleControl__labelRow">
                    <label
                        id={`${id}-label`}
                        htmlFor={id}
                        className="toggleControl__label"
                    >
                        {label}
                    </label>
                    {labelAddon}
                </div>
                {description ? (
                    <span
                        id={descriptionId}
                        className="toggleControl__description"
                    >
                        {description}
                    </span>
                ) : null}
            </div>
            <div className="toggleControl__switch">
                <input
                    ref={inputRef}
                    id={id}
                    name={name}
                    type="checkbox"
                    defaultChecked={checked}
                    disabled={disabled}
                />
                <span
                    className={`toggleControl__state${checked ? " toggleControl__state--active" : ""}`}
                >
                    {checked ? "Ativado" : "Desativado"}
                </span>
            </div>
        </div>
    );
}
