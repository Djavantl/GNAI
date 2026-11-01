import FormField from "@/components/Forms/FormField/FormField";
import FormGrid, { FormGridWide } from "@/components/Forms/FormGrid/FormGrid";
import FormSection from "@/components/Forms/FormSection/FormSection";
import ImageUploadField from "@/components/Forms/ImageUploadField/ImageUploadField";
import SummernoteEditor from "@/components/Forms/SummernoteEditor/SummernoteEditor";
import TextArea from "@/components/Forms/TextArea/TextArea";
import TextInput from "@/components/Forms/TextInput/TextInput";
import ToggleControl from "@/components/Forms/ToggleControl/ToggleControl";
import type { AboutAppearance } from "@/components/Website/Website.types";
import {
    AppearanceColorField,
    AppearanceRangeField,
    AppearanceSelectField,
    appearanceAlignments,
} from "./BuilderAppearanceFields";
import type { BuilderSection } from "./websiteBuilder";

import BuilderLinkField, { type BuilderLinkOption } from "./BuilderLinkField";
type Props = {
    section: BuilderSection;
    linkOptions: BuilderLinkOption[];
    index: number;
    errors: Record<string, string | undefined>;
    disabled: boolean;
    onChange: (patch: Partial<BuilderSection>) => void;
    onImageError: (message: string) => void;
    onEditorError: () => void;
};

export default function BuilderAboutEditor({
    section, linkOptions, index, errors, disabled, onChange, onImageError, onEditorError,
}: Props): React.JSX.Element | null {
    const appearance = section.about;
    if (!appearance) return null;

    const id = (field: string) => `website-section-${section.id}-${field}`;
    const error = (field: string) => errors[`sections.${index}.${field}`];
    const patch = (values: Partial<AboutAppearance>) => onChange({ about: { ...appearance, ...values } });

    function color(key: keyof AboutAppearance, label: string): React.JSX.Element {
        return (
            <AppearanceColorField
                id={id(`about-${key}`)}
                label={label}
                value={String(appearance![key])}
                error={error(`about.${key}`)}
                disabled={disabled}
                onChange={(value) => patch({ [key]: value })}
            />
        );
    }

    function range(key: keyof AboutAppearance, label: string, min: number, max: number): React.JSX.Element {
        return (
            <AppearanceRangeField
                id={id(`about-${key}`)}
                label={label}
                value={Number(appearance![key])}
                min={min}
                max={max}
                error={error(`about.${key}`)}
                disabled={disabled}
                onChange={(value) => patch({ [key]: value })}
            />
        );
    }

    function select(key: keyof AboutAppearance, label: string, options = appearanceAlignments): React.JSX.Element {
        return (
            <AppearanceSelectField
                id={id(`about-${key}`)}
                name={`sections[${index}][about][${key}]`}
                label={label}
                value={String(appearance![key])}
                options={options}
                error={error(`about.${key}`)}
                disabled={disabled}
                onChange={(value) => patch({ [key]: value })}
            />
        );
    }

    function toggle(key: "image_visible" | "title_divider" | "gradient_enabled", label: string): React.JSX.Element {
        return (
            <FormGridWide>
                <ToggleControl
                    id={id(`about-${key}`)}
                    label={label}
                    size="small"
                    checked={appearance![key]}
                    disabled={disabled}
                    onChange={(checked) => patch({ [key]: checked })}
                />
            </FormGridWide>
        );
    }

    function text(key: "title" | "subtitle" | "button_label" | "image_alt", label: string, maxLength: number): React.JSX.Element {
        const props = {
            id: id(key),
            name: `sections[${index}][${key}]`,
            value: section[key],
            maxLength,
            disabled,
            "aria-describedby": error(key) ? `${id(key)}-error` : undefined,
            onChange: (event: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement>) => onChange({ [key]: event.target.value }),
        };
        return (
            <FormGridWide>
                <FormField
                    id={id(key)}
                    label={label}
                    error={error(key)}
                    requiredIndicator={key === "image_alt" && (!!section.image || (!!section.image_url && !section.remove_image))}
                >
                    {key === "subtitle" ? (
                        <TextArea {...props} rows={3} aria-invalid={!!error(key)} />
                    ) : (
                        <TextInput {...props} invalid={!!error(key)} />
                    )}
                </FormField>
            </FormGridWide>
        );
    }

    return (
        <FormGrid>
            <FormGridWide>
                <fieldset disabled={disabled} className="websiteBuilder__appearanceFields">
                    <FormSection title="Título" variant="inline" iconClass="fa fa-font">
                        <FormGrid>
                            {text("title", "Título", 160)}
                            {select("title_align", "Posição do título")}
                            {range("title_size", "Tamanho do título (px)", 20, 56)}
                            {color("title_color", "Cor do título")}
                            {toggle("title_divider", "Exibir linha abaixo do título")}
                            {color("divider_color", "Cor da linha")}
                        </FormGrid>
                    </FormSection>
                    <FormSection title="Texto de apoio" variant="inline" iconClass="fa fa-align-left">
                        <FormGrid>
                            {text("subtitle", "Texto de apoio", 500)}
                            {select("subtitle_align", "Posição do texto de apoio")}
                            {range("subtitle_size", "Tamanho do texto de apoio (px)", 14, 28)}
                            {color("subtitle_color", "Cor do texto de apoio")}
                        </FormGrid>
                    </FormSection>
                    <FormSection title="Texto da seção" variant="inline" iconClass="fa fa-file-text-o">
                        <FormGrid>
                            <FormGridWide>
                                <FormField id={`${id("body")}-input`} label="Texto" error={error("body")}>
                                    <SummernoteEditor
                                        key={section.id}
                                        id={id("body")}
                                        label="Texto da seção"
                                        defaultValue={section.body}
                                        height={180}
                                        disabled={disabled}
                                        invalid={!!error("body")}
                                        describedBy={error("body") ? `${id("body")}-input-error` : undefined}
                                        onLoadError={onEditorError}
                                        onChange={(body) => onChange({ body })}
                                    />
                                </FormField>
                            </FormGridWide>
                            {select("body_align", "Alinhamento do texto", [...appearanceAlignments, { value: "justify", label: "Justificado" }])}
                            {range("body_size", "Tamanho do texto (px)", 14, 26)}
                            {range("body_line_height", "Espaçamento entre linhas (%)", 140, 220)}
                            {color("body_color", "Cor do texto")}
                        </FormGrid>
                    </FormSection>
                    <FormSection title="Imagem" variant="inline" iconClass="fa fa-picture-o">
                        <FormGrid>
                            <FormGridWide>
                                <FormField id={id("image")} label="Imagem" error={error("image")}>
                                    <ImageUploadField
                                        id={id("image")}
                                        name={`sections[${index}][image]`}
                                        label="Imagem do núcleo"
                                        file={section.image}
                                        currentUrl={section.image_url}
                                        removed={section.remove_image}
                                        previewAlt={section.image_alt}
                                        disabled={disabled}
                                        invalid={!!error("image")}
                                        describedBy={error("image") ? `${id("image")}-error` : undefined}
                                        onChange={(image) => onChange({ image, remove_image: false })}
                                        onRemove={() => onChange({ image: null, remove_image: true })}
                                        onError={onImageError}
                                    />
                                </FormField>
                            </FormGridWide>
                            {text("image_alt", "Texto alternativo da imagem", 255)}
                            {toggle("image_visible", "Exibir imagem")}
                            {select("image_side", "Posição da imagem", [
                                { value: "right", label: "À direita do texto" },
                                { value: "left", label: "À esquerda do texto" },
                                { value: "top", label: "Acima do texto" },
                                { value: "bottom", label: "Abaixo do texto" },
                            ])}
                            {select("image_fit", "Enquadramento", [
                                { value: "cover", label: "Preencher a área" },
                                { value: "contain", label: "Mostrar a imagem inteira" },
                            ])}
                            {select("image_position", "Foco da imagem", [
                                { value: "center", label: "Centro" },
                                { value: "top", label: "Topo" },
                                { value: "bottom", label: "Base" },
                                { value: "left", label: "Esquerda" },
                                { value: "right", label: "Direita" },
                            ])}
                            {range("image_width", "Largura da coluna da imagem (%)", 25, 60)}
                            {range("image_height", "Altura da imagem (px)", 160, 700)}
                            {range("image_radius", "Arredondamento da imagem (px)", 0, 60)}
                            {range("image_shadow", "Sombra da imagem", 0, 40)}
                        </FormGrid>
                    </FormSection>
                    <FormSection title="Botão" variant="inline" iconClass="fa fa-hand-o-up">
                        <FormGrid>
                            {text("button_label", "Texto do botão", 60)}
                            <BuilderLinkField
                                id={`website-section-${section.id}-button_url`}
                                name={`sections[${index}][button_url]`}
                                value={section.button_url}
                                options={linkOptions}
                                error={errors[`sections.${index}.button_url`]}
                                disabled={disabled}
                                onChange={(button_url) => onChange({ button_url })}
                            />
                            {select("button_align", "Posição do botão")}
                            {range("button_radius", "Arredondamento do botão (px)", 0, 60)}
                            {color("button_color", "Cor do texto do botão")}
                            {color("button_background", "Cor de fundo do botão")}
                            {range("button_shadow", "Sombra do botão", 0, 40)}
                        </FormGrid>
                    </FormSection>
                    <FormSection title="Fundo e espaçamento" variant="inline" iconClass="fa fa-adjust">
                        <FormGrid>
                            {color("background_color", "Cor de fundo")}
                            {range("padding_vertical", "Espaçamento vertical (px)", 24, 120)}
                            {range("gap", "Espaço entre texto e imagem (px)", 16, 80)}
                            {toggle("gradient_enabled", "Usar degradê")}
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
