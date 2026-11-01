import { useEffect, useMemo, useState } from "react";
import FormField from "@/components/Forms/FormField/FormField";
import SelectField from "@/components/Forms/SelectField/SelectField";
import { websiteIconLibraries } from "@/utils/websiteIconCatalog";

const iconAliases: Record<string, string> = {
    "fa fa-users": "Pessoas equipe grupo",
    "fa fa-wheelchair": "Acessibilidade inclusão",
    "fa fa-graduation-cap": "Educação formação estudante",
    "fa fa-book": "Livro ensino",
    "fa fa-lightbulb-o": "Ideia",
    "fa fa-heart-o": "Acolhimento coração",
    "fa fa-comments-o": "Conversa diálogo",
    "fa fa-calendar": "Calendário",
    "fa fa-university": "Instituição",
    "fa fa-hand-paper-o": "Mão",
    "fa fa-child": "Criança",
    "fa fa-microphone": "Palestra",
    "fa fa-eye": "Visão",
    "fa fa-star-o": "Destaque estrela",
    "fa fa-sitemap": "Organização",
    "fa fa-globe": "Mundo",
};

export default function BuilderIconField({
    id, name, label, value, error, disabled, onChange,
}: {
    id: string;
    name: string;
    label: string;
    value: string;
    error?: string;
    disabled: boolean;
    onChange: (value: string) => void;
}): React.JSX.Element {
    const selectedLibrary = websiteIconLibraries.find((library) => library.icons.includes(value));
    const [libraryId, setLibraryId] = useState(selectedLibrary?.value ?? websiteIconLibraries[0].value);

    useEffect(() => {
        if (selectedLibrary) setLibraryId(selectedLibrary.value);
    }, [selectedLibrary]);

    const options = useMemo(() => {
        const library = websiteIconLibraries.find((item) => item.value === libraryId) ?? websiteIconLibraries[0];
        const icons = value && !selectedLibrary ? [value, ...library.icons] : library.icons;
        return icons.map((icon) => ({
            value: icon,
            iconClass: icon,
            label: [iconAliases[icon], icon.replace(/-/g, " ")].filter(Boolean).join(" "),
        }));
    }, [libraryId, selectedLibrary, value]);

    return (
        <div className="websiteBuilder__iconPicker">
            <FormField id={`${id}-library`} label="Biblioteca de ícones">
                <SelectField
                    id={`${id}-library`}
                    name={`${name}-library`}
                    label="Biblioteca de ícones"
                    options={websiteIconLibraries.map(({ value, label }) => ({ value, label }))}
                    value={libraryId}
                    disabled={disabled}
                    onChange={(event) => {
                        setLibraryId(event.target.value || websiteIconLibraries[0].value);
                        onChange("");
                    }}
                />
            </FormField>
            <FormField id={id} label={label} error={error}>
                <SelectField
                    id={id}
                    name={name}
                    label={label}
                    placeholder="Sem ícone"
                    value={value}
                    options={options}
                    iconOnly
                    iconSearch
                    disabled={disabled}
                    aria-invalid={!!error}
                    aria-describedby={error ? `${id}-error` : undefined}
                    onChange={(event) => onChange(event.target.value)}
                />
            </FormField>
        </div>
    );
}
