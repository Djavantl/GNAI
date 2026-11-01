import { useEffect, useState } from "react";
import FormField from "@/components/Forms/FormField/FormField";
import FormGrid, { FormGridWide } from "@/components/Forms/FormGrid/FormGrid";
import FormSection from "@/components/Forms/FormSection/FormSection";
import TextArea from "@/components/Forms/TextArea/TextArea";
import TextInput from "@/components/Forms/TextInput/TextInput";
import ToggleControl from "@/components/Forms/ToggleControl/ToggleControl";
import Button from "@/components/UI/Button/Button";
import type { PillarCardAppearance, WebsiteSectionItem } from "@/components/Website/Website.types";
import {
    AppearanceColorField, AppearanceRangeField, AppearanceSelectField, appearanceAlignments,
} from "./BuilderAppearanceFields";
import BuilderIconField from "./BuilderIconField";
import type { BuilderEditTarget } from "./websiteBuilder";

type Props = {
    sectionId: string;
    sectionIndex: number;
    item: WebsiteSectionItem;
    itemIndex: number;
    count: number;
    template: PillarCardAppearance;
    disabled: boolean;
    errors: Record<string, string | undefined>;
    editTarget: BuilderEditTarget | null;
    onChange: (patch: Partial<WebsiteSectionItem>) => void;
    onMove: (offset: -1 | 1) => void;
    onRemove: () => void;
};

export default function BuilderPillarItemEditor({
    sectionId, sectionIndex, item, itemIndex, count, template, disabled, errors, editTarget, onChange, onMove, onRemove,
}: Props): React.JSX.Element {
    const appearance = item.appearance ?? template;
    const prefix = `sections.${sectionIndex}.items.${itemIndex}`;
    const hasErrors = Object.keys(errors).some((key) => key.startsWith(`${prefix}.`));
    const [open, setOpen] = useState(itemIndex === 0 || hasErrors);

    useEffect(() => {
        if (hasErrors || (editTarget?.sectionId === sectionId && editTarget.field?.startsWith(`item-${itemIndex}-`))) {
            setOpen(true);
        }
    }, [hasErrors, editTarget, sectionId, itemIndex]);

    const id = (key: string) => `website-section-${sectionId}-item-${itemIndex}-${key}`;
    const error = (key: string) => errors[`${prefix}.${key}`];
    const patch = (values: Partial<PillarCardAppearance>) => onChange({ appearance: { ...appearance, ...values } });

    function color(key: keyof PillarCardAppearance, label: string): React.JSX.Element {
        return (
            <AppearanceColorField
                id={id(`appearance-${key}`)}
                label={label}
                value={String(appearance[key])}
                error={error(`appearance.${key}`)}
                disabled={disabled}
                onChange={(value) => patch({ [key]: value })}
            />
        );
    }

    function range(key: keyof PillarCardAppearance, label: string, min: number, max: number): React.JSX.Element {
        return (
            <AppearanceRangeField
                id={id(`appearance-${key}`)}
                label={label}
                value={Number(appearance[key])}
                min={min}
                max={max}
                error={error(`appearance.${key}`)}
                disabled={disabled}
                onChange={(value) => patch({ [key]: value })}
            />
        );
    }

    function select(key: keyof PillarCardAppearance, label: string, options = appearanceAlignments): React.JSX.Element {
        return (
            <AppearanceSelectField
                id={id(`appearance-${key}`)}
                name={`sections[${sectionIndex}][items][${itemIndex}][appearance][${key}]`}
                label={label}
                value={String(appearance[key])}
                options={options}
                error={error(`appearance.${key}`)}
                disabled={disabled}
                onChange={(value) => patch({ [key]: value })}
            />
        );
    }

    function toggle(key: "hover_enabled" | "gradient_enabled", label: string): React.JSX.Element {
        return (
            <FormGridWide>
                <ToggleControl
                    id={id(`appearance-${key}`)}
                    label={label}
                    size="small"
                    checked={appearance[key]}
                    disabled={disabled}
                    onChange={(checked) => patch({ [key]: checked })}
                />
            </FormGridWide>
        );
    }

    return (
        <li className="websiteBuilder__pillarItem">
            <details open={open} onToggle={(event) => setOpen(event.currentTarget.open)}>
                <summary>
                    <strong>Item {itemIndex + 1}</strong>
                    <span>{item.title || "Novo item"}</span>
                </summary>
                {open ? (
                    <div className="websiteBuilder__pillarItemBody">
                        <div className="websiteBuilder__slideActions">
                            <Button
                                label="Subir"
                                leftIconClass="fa fa-arrow-up"
                                tone="white"
                                size="sm"
                                disabled={disabled || itemIndex === 0}
                                onClick={() => onMove(-1)}
                            />
                            <Button
                                label="Descer"
                                leftIconClass="fa fa-arrow-down"
                                tone="white"
                                size="sm"
                                disabled={disabled || itemIndex === count - 1}
                                onClick={() => onMove(1)}
                            />
                            <Button
                                label="Remover item"
                                leftIconClass="fa fa-times"
                                tone="dark-red"
                                size="sm"
                                disabled={disabled}
                                onClick={onRemove}
                            />
                        </div>
                        <FormSection title="Ícone" variant="inline" iconClass="fa fa-star-o">
                            <FormGrid>
                                <FormGridWide>
                                    <BuilderIconField
                                        id={id("icon")}
                                        name={`sections[${sectionIndex}][items][${itemIndex}][icon]`}
                                        label="Ícone"
                                        value={item.icon}
                                        error={error("icon")}
                                        disabled={disabled}
                                        onChange={(icon) => onChange({ icon })}
                                    />
                                </FormGridWide>
                                {select("icon_position", "Posição do ícone", [
                                    { value: "top", label: "Acima do texto" },
                                    { value: "left", label: "À esquerda do texto" },
                                    { value: "right", label: "À direita do texto" },
                                ])}
                                {select("icon_align", "Alinhamento do ícone")}
                                {color("icon_color", "Cor do ícone")}
                                {color("icon_background", "Cor do fundo do ícone")}
                                {range("icon_size", "Tamanho do ícone (px)", 12, 64)}
                                {range("icon_width", "Largura do fundo do ícone (px)", 24, 120)}
                                {range("icon_height", "Altura do fundo do ícone (px)", 24, 120)}
                                {range("icon_radius", "Arredondamento do fundo do ícone (px)", 0, 60)}
                                {range("icon_gap", "Distância entre ícone e texto (px)", 4, 40)}
                            </FormGrid>
                        </FormSection>
                        <FormSection title="Título do item" variant="inline" iconClass="fa fa-font">
                            <FormGrid>
                                <FormGridWide>
                                    <FormField id={id("title")} label="Título" error={error("title")}>
                                        <TextInput
                                            id={id("title")}
                                            value={item.title}
                                            maxLength={120}
                                            disabled={disabled}
                                            invalid={!!error("title")}
                                            aria-describedby={error("title") ? `${id("title")}-error` : undefined}
                                            onChange={(event) => onChange({ title: event.target.value })}
                                        />
                                    </FormField>
                                </FormGridWide>
                                {select("title_align", "Alinhamento do título")}
                                {range("title_size", "Tamanho do título (px)", 14, 32)}
                                {color("title_color", "Cor do título")}
                            </FormGrid>
                        </FormSection>
                        <FormSection title="Texto do item" variant="inline" iconClass="fa fa-align-left">
                            <FormGrid>
                                <FormGridWide>
                                    <FormField id={id("text")} label="Texto" error={error("text")}>
                                        <TextArea
                                            id={id("text")}
                                            value={item.text}
                                            rows={3}
                                            maxLength={300}
                                            disabled={disabled}
                                            aria-invalid={!!error("text")}
                                            aria-describedby={error("text") ? `${id("text")}-error` : undefined}
                                            onChange={(event) => onChange({ text: event.target.value })}
                                        />
                                    </FormField>
                                </FormGridWide>
                                {select("text_align", "Alinhamento do texto", [...appearanceAlignments, { value: "justify", label: "Justificado" }])}
                                {range("text_size", "Tamanho do texto (px)", 12, 24)}
                                {range("text_line_height", "Espaçamento entre linhas (%)", 140, 220)}
                                {color("text_color", "Cor do texto")}
                            </FormGrid>
                        </FormSection>
                        <FormSection title="Card" variant="inline" iconClass="fa fa-square-o">
                            <FormGrid>
                                {color("card_background", "Cor de fundo")}
                                {color("card_border_color", "Cor da borda")}
                                {range("card_border_width", "Espessura da borda (px)", 0, 4)}
                                {range("card_radius", "Arredondamento do card (px)", 0, 60)}
                                {range("card_width", "Largura do card na coluna (%)", 50, 100)}
                                {range("card_min_height", "Altura mínima do card (px)", 0, 600)}
                                {range("card_padding", "Espaçamento interno (px)", 8, 48)}
                                {range("card_shadow", "Sombra do card", 0, 40)}
                                {toggle("gradient_enabled", "Usar degradê no card")}
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
                        <FormSection title="Efeito ao passar o ponteiro" variant="inline" iconClass="fa fa-mouse-pointer">
                            <FormGrid>
                                {toggle("hover_enabled", "Ativar efeito")}
                                {appearance.hover_enabled ? (
                                    <>
                                        {color("hover_background", "Cor de fundo no efeito")}
                                        {color("hover_border_color", "Cor da borda no efeito")}
                                        {range("hover_lift", "Elevação do card (px)", 0, 12)}
                                        {range("hover_shadow", "Sombra no efeito", 0, 40)}
                                    </>
                                ) : null}
                            </FormGrid>
                        </FormSection>
                    </div>
                ) : null}
            </details>
        </li>
    );
}
