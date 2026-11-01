import FormField from "@/components/Forms/FormField/FormField";
import RangeSlider from "@/components/Forms/RangeSlider/RangeSlider";
import SelectField from "@/components/Forms/SelectField/SelectField";
import TextInput from "@/components/Forms/TextInput/TextInput";
import Button from "@/components/UI/Button/Button";

export const appearanceAlignments = [
    { value: "left", label: "À esquerda" },
    { value: "center", label: "Centralizado" },
    { value: "right", label: "À direita" },
];

type BaseProps = { id: string; label: string; error?: string; disabled: boolean };

export function AppearanceColorField({
    id, label, error, disabled, value, onChange,
}: BaseProps & { value: string; onChange: (value: string) => void }): React.JSX.Element {
    return (
        <FormField id={id} label={label} error={error}>
            <div className="websiteBuilder__color">
                <input
                    type="color"
                    aria-label={`Selecionar ${label.toLowerCase()}`}
                    title={`Selecionar ${label.toLowerCase()}`}
                    value={/^#[0-9a-f]{6}$/i.test(value) ? value : "#322b75"}
                    disabled={disabled}
                    onChange={(event) => onChange(event.target.value)}
                />
                <TextInput
                    id={id}
                    value={value}
                    placeholder="Padrão do tema"
                    maxLength={7}
                    disabled={disabled}
                    invalid={!!error}
                    aria-describedby={error ? `${id}-error` : undefined}
                    onChange={(event) => onChange(event.target.value)}
                />
                <Button
                    size="sm"
                    tone="white"
                    label="Padrão"
                    title={`Restaurar padrão de ${label.toLowerCase()}`}
                    leftIconClass="fa fa-undo"
                    disabled={disabled || !value}
                    onClick={() => onChange("")}
                />
            </div>
        </FormField>
    );
}

export function AppearanceRangeField({
    id, label, error, disabled, value, min, max, onChange,
}: BaseProps & { value: number; min: number; max: number; onChange: (value: number) => void }): React.JSX.Element {
    return (
        <FormField id={id} label={label} error={error}>
            <RangeSlider
                id={id}
                label={label}
                value={value}
                min={min}
                max={max}
                disabled={disabled}
                invalid={!!error}
                describedBy={error ? `${id}-error` : undefined}
                live
                onChange={onChange}
            />
        </FormField>
    );
}

export function AppearanceSelectField({
    id, label, error, disabled, value, name, options, onChange,
}: BaseProps & {
    value: string;
    name?: string;
    options: { value: string; label: string }[];
    onChange: (value: string) => void;
}): React.JSX.Element {
    return (
        <FormField id={id} label={label} error={error}>
            <SelectField
                id={id}
                name={name ?? id}
                label={label}
                value={value}
                options={options}
                disabled={disabled}
                aria-invalid={!!error}
                aria-describedby={error ? `${id}-error` : undefined}
                onChange={(event) => onChange(event.target.value)}
            />
        </FormField>
    );
}
