import FormField from "@/components/Forms/FormField/FormField";
import FormGrid, { FormGridWide } from "@/components/Forms/FormGrid/FormGrid";
import FormSection from "@/components/Forms/FormSection/FormSection";
import TextInput from "@/components/Forms/TextInput/TextInput";
import TextArea from "@/components/Forms/TextArea/TextArea";
import ToggleControl from "@/components/Forms/ToggleControl/ToggleControl";
import Button from "@/components/UI/Button/Button";
import type { PillarCardAppearance, PillarsAppearance, WebsiteSectionItem } from "@/components/Website/Website.types";
import "@/components/Forms/RemovableField/RemovableField.css";
import {
    AppearanceColorField, AppearanceRangeField, AppearanceSelectField, appearanceAlignments,
} from "./BuilderAppearanceFields";
import BuilderPillarItemEditor from "./BuilderPillarItemEditor";
import { emptyItem, type BuilderEditTarget, type BuilderSection } from "./websiteBuilder";

export default function BuilderPillarsEditor({
    section, index, errors, disabled, template, editTarget, onChange, onEditTarget,
}: {
    section: BuilderSection;
    index: number;
    errors: Record<string, string | undefined>;
    disabled: boolean;
    template: PillarCardAppearance;
    editTarget: BuilderEditTarget | null;
    onEditTarget: (target: BuilderEditTarget) => void;
    onChange: (patch: Partial<BuilderSection>) => void;
}): React.JSX.Element | null {
    const appearance = section.pillars;
    if (!appearance) return null;

    const id = (key: string) => `website-section-${section.id}-${key}`;
    const error = (key: string) => errors[`sections.${index}.${key}`];
    const patch = (values: Partial<PillarsAppearance>) => onChange({ pillars: { ...appearance, ...values } });

    function color(key: keyof PillarsAppearance, label: string): React.JSX.Element {
        return (
            <AppearanceColorField
                id={id(`pillars-${key}`)}
                label={label}
                value={String(appearance![key])}
                error={error(`pillars.${key}`)}
                disabled={disabled}
                onChange={(value) => patch({ [key]: value })}
            />
        );
    }

    function range(key: keyof PillarsAppearance, label: string, min: number, max: number): React.JSX.Element {
        return (
            <AppearanceRangeField
                id={id(`pillars-${key}`)}
                label={label}
                value={Number(appearance![key])}
                min={min}
                max={max}
                error={error(`pillars.${key}`)}
                disabled={disabled}
                onChange={(value) => patch({ [key]: value })}
            />
        );
    }

    function select(key: keyof PillarsAppearance, label: string, options = appearanceAlignments): React.JSX.Element {
        return (
            <AppearanceSelectField
                id={id(`pillars-${key}`)}
                name={`sections[${index}][pillars][${key}]`}
                label={label}
                value={String(appearance![key])}
                options={options}
                error={error(`pillars.${key}`)}
                disabled={disabled}
                onChange={(value) => patch({ [key]: value })}
            />
        );
    }

    function updateItem(itemIndex: number, values: Partial<WebsiteSectionItem>): void {
        onChange({ items: section.items.map((item, current) => current === itemIndex ? { ...item, ...values } : item) });
    }

    function moveItem(itemIndex: number, offset: -1 | 1): void {
        const target = itemIndex + offset;
        if (target < 0 || target >= section.items.length) return;
        const items = [...section.items];
        [items[itemIndex], items[target]] = [items[target], items[itemIndex]];
        onChange({ items });
    }

    return (
        <FormGrid>
            <FormGridWide>
                <fieldset disabled={disabled} className="websiteBuilder__appearanceFields">
                    <FormSection title="Título" variant="inline" iconClass="fa fa-font">
                        <FormGrid>
                            <FormGridWide>
                                <FormField id={id("title")} label="Título" error={error("title")}>
                                    <TextInput
                                        id={id("title")}
                                        value={section.title}
                                        maxLength={160}
                                        disabled={disabled}
                                        invalid={!!error("title")}
                                        aria-describedby={error("title") ? `${id("title")}-error` : undefined}
                                        onChange={(event) => onChange({ title: event.target.value })}
                                    />
                                </FormField>
                            </FormGridWide>
                            {select("title_align", "Posição do título")}
                            {range("title_size", "Tamanho do título (px)", 20, 56)}
                            {color("title_color", "Cor do título")}
                            <FormGridWide>
                                <ToggleControl
                                    id={id("pillars-title_divider")}
                                    label="Exibir linha abaixo do título"
                                    size="small"
                                    checked={appearance.title_divider}
                                    disabled={disabled}
                                    onChange={(checked) => patch({ title_divider: checked })}
                                />
                            </FormGridWide>
                            {color("divider_color", "Cor da linha")}
                        </FormGrid>
                    </FormSection>
                    <FormSection title="Texto de apoio" variant="inline" iconClass="fa fa-align-left">
                        <FormGrid>
                            <FormGridWide>
                                <FormField id={id("subtitle")} label="Texto de apoio" error={error("subtitle")}>
                                    <TextArea
                                        id={id("subtitle")}
                                        value={section.subtitle}
                                        rows={3}
                                        maxLength={500}
                                        disabled={disabled}
                                        aria-invalid={!!error("subtitle")}
                                        aria-describedby={error("subtitle") ? `${id("subtitle")}-error` : undefined}
                                        onChange={(event) => onChange({ subtitle: event.target.value })}
                                    />
                                </FormField>
                            </FormGridWide>
                            {select("subtitle_align", "Posição do texto de apoio")}
                            {range("subtitle_size", "Tamanho do texto de apoio (px)", 14, 28)}
                            {color("subtitle_color", "Cor do texto de apoio")}
                        </FormGrid>
                    </FormSection>
                    <FormSection title="Itens" variant="inline" iconClass="fa fa-th-large">
                        {error("items") ? <p className="websiteBuilder__itemsError">{error("items")}</p> : null}
                        <ol className="websiteBuilder__pillarItems">
                            {section.items.map((item, itemIndex) => (
                                <BuilderPillarItemEditor
                                    key={item.editor_key ?? itemIndex}
                                    sectionId={section.id}
                                    sectionIndex={index}
                                    item={item}
                                    itemIndex={itemIndex}
                                    count={section.items.length}
                                    template={template}
                                    disabled={disabled}
                                    errors={errors}
                                    editTarget={editTarget}
                                    onChange={(values) => updateItem(itemIndex, values)}
                                    onMove={(offset) => moveItem(itemIndex, offset)}
                                    onRemove={() => onChange({ items: section.items.filter((_, current) => current !== itemIndex) })}
                                />
                            ))}
                        </ol>
                        <Button
                            type="button"
                            tone="white"
                            className="removableField__add"
                            labelIconClass="fa fa-plus"
                            label="Item"
                            title="Adicionar item"
                            aria-label="Adicionar item"
                            disabled={disabled || section.items.length >= 12}
                            onClick={() => {
                                onChange({
                                    items: [...section.items, {
                                        ...emptyItem("pillars"),
                                        editor_key: `item-${Date.now().toString(36)}`,
                                        appearance: { ...template },
                                    }],
                                });
                                onEditTarget({ sectionId: section.id, field: `item-${section.items.length}-icon` });
                            }}
                        />
                    </FormSection>
                    <FormSection title="Organização e fundo" variant="inline" iconClass="fa fa-adjust">
                        <FormGrid>
                            {select("layout", "Organização dos itens", [
                                { value: "grid", label: "Grade de cards" },
                                { value: "list", label: "Lista" },
                            ])}
                            {appearance.layout === "grid" ? range("columns", "Colunas", 1, 4) : null}
                            {range("gap", "Espaço entre cards (px)", 8, 48)}
                            {range("padding_vertical", "Espaçamento vertical (px)", 24, 120)}
                            {color("background_color", "Cor de fundo da seção")}
                            <FormGridWide>
                                <ToggleControl
                                    id={id("pillars-gradient_enabled")}
                                    label="Usar degradê na seção"
                                    size="small"
                                    checked={appearance.gradient_enabled}
                                    disabled={disabled}
                                    onChange={(checked) => patch({ gradient_enabled: checked })}
                                />
                            </FormGridWide>
                            {appearance.gradient_enabled ? (
                                <>
                                    {color("gradient_end_color", "Cor final do degradê")}
                                    {select("gradient_direction", "Direção do degradê", [
                                        { value: "to bottom", label: "De cima para baixo" },
                                        { value: "to top", label: "De baixo para cima" },
                                        { value: "to right", label: "Da esquerda para a direita" },
                                        { value: "to left", label: "Da direita para a esquerda" },
                                    ])}
                                </>
                            ) : null}
                        </FormGrid>
                    </FormSection>
                </fieldset>
            </FormGridWide>
        </FormGrid>
    );
}
