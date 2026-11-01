import { useState } from "react";
import FormField from "@/components/Forms/FormField/FormField";
import SelectField from "@/components/Forms/SelectField/SelectField";
import TextInput from "@/components/Forms/TextInput/TextInput";

export type BuilderLinkOption = { value: string; label: string };

export default function BuilderLinkField({
    id, name, value, options, error, disabled, onChange,
}: {
    id: string;
    name: string;
    value: string;
    options: BuilderLinkOption[];
    error?: string;
    disabled: boolean;
    onChange: (value: string) => void;
}): React.JSX.Element {
    const [custom, setCustom] = useState(false);
    const known = options.some((option) => option.value === value);
    const unavailable = value.startsWith("#") && !known;
    const customAddress = !known && !unavailable && (!!value || custom);
    const describedBy = error ? `${id}-error` : undefined;

    return (
        <div>
            <FormField id={id} label="Destino da ação" error={error}>
                <SelectField
                    id={id}
                    name={name}
                    label="Destino da ação"
                    placeholder="Sem destino"
                    value={customAddress ? "custom" : value}
                    options={[
                        ...options,
                        ...(unavailable ? [{ value, label: "Seção indisponível" }] : []),
                        { value: "custom", label: "Outro endereço" },
                    ]}
                    disabled={disabled}
                    aria-invalid={!!error}
                    aria-describedby={describedBy}
                    onChange={(event) => {
                        const selected = event.target.value;
                        setCustom(selected === "custom");
                        onChange(selected === "custom" ? "" : selected);
                    }}
                />
            </FormField>
            {customAddress ? (
                <FormField id={`${id}-address`} label="Endereço">
                    <TextInput
                        id={`${id}-address`}
                        value={value}
                        maxLength={500}
                        placeholder="https://"
                        disabled={disabled}
                        invalid={!!error}
                        aria-describedby={describedBy}
                        onChange={(event) => {
                            if (!event.target.value.trimStart().startsWith("#")) onChange(event.target.value);
                        }}
                    />
                </FormField>
            ) : null}
        </div>
    );
}
