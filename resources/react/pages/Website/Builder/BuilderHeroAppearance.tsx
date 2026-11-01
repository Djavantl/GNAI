import FormField from "@/components/Forms/FormField/FormField";
import FormGrid, { FormGridWide } from "@/components/Forms/FormGrid/FormGrid";
import FormSection from "@/components/Forms/FormSection/FormSection";
import ImageUploadField from "@/components/Forms/ImageUploadField/ImageUploadField";
import TextArea from "@/components/Forms/TextArea/TextArea";
import TextInput from "@/components/Forms/TextInput/TextInput";
import ToggleControl from "@/components/Forms/ToggleControl/ToggleControl";
import type { HeroAppearance } from "@/components/Website/Website.types";
import type { BuilderSection } from "./websiteBuilder";
import {
    AppearanceColorField,
    AppearanceRangeField,
    AppearanceSelectField,
    appearanceAlignments as alignments,
} from "./BuilderAppearanceFields";

import BuilderLinkField, { type BuilderLinkOption } from "./BuilderLinkField";
type Props = {
    section: BuilderSection;
    linkOptions: BuilderLinkOption[];
    index: number;
    disabled: boolean;
    errors: Record<string, string | undefined>;
    onChange: (patch: Partial<BuilderSection>) => void;
    onImageError: (message: string) => void;
};

export default function BuilderHeroAppearance({
    section,
    linkOptions,
    index,
    disabled,
    errors,
    onChange,
    onImageError,
}: Props): React.JSX.Element | null {
    const appearance = section.hero;
    if (!appearance) return null;

    const id = (key: string) => `website-section-${section.id}-hero-${key}`;
    const error = (key: string) => errors[`sections.${index}.hero.${key}`];
    const describedBy = (key: string) => error(key) ? `${id(key)}-error` : undefined;
    const patch = (values: Partial<HeroAppearance>) => onChange({ hero: { ...appearance, ...values } });

    function select(key: keyof HeroAppearance, label: string, options: { value: string; label: string }[]): React.JSX.Element {
        return (
            <AppearanceSelectField
                id={id(key)}
                name={`sections[${index}][hero][${key}]`}
                label={label}
                value={String(appearance![key])}
                options={options}
                error={error(key)}
                disabled={disabled}
                onChange={(value) => patch({ [key]: value })}
            />
        );
    }

    function range(key: keyof HeroAppearance, label: string, min: number, max: number): React.JSX.Element {
        return (
            <AppearanceRangeField
                id={id(key)}
                label={label}
                value={Number(appearance![key])}
                min={min}
                max={max}
                error={error(key)}
                disabled={disabled}
                onChange={(value) => patch({ [key]: value })}
            />
        );
    }

    function color(key: keyof HeroAppearance, label: string): React.JSX.Element {
        return (
            <AppearanceColorField
                id={id(key)}
                label={label}
                value={String(appearance![key])}
                error={error(key)}
                disabled={disabled}
                onChange={(value) => patch({ [key]: value })}
            />
        );
    }

    function textField(
        key: "title" | "button_label" | "image_alt",
        label: string,
        maxLength: number,
        hint?: string,
    ): React.JSX.Element {
        const fieldId = `website-section-${section.id}-${key}`;
        const fieldError = errors[`sections.${index}.${key}`];
        return (
            <FormField
                id={fieldId}
                label={label}
                error={fieldError}
                hint={hint}
                requiredIndicator={key === "image_alt" && appearance?.mode === "banners"
                    && (!!section.image || (!!section.image_url && !section.remove_image))}
            >
                <TextInput
                    id={fieldId}
                    name={`sections[${index}][${key}]`}
                    value={section[key]}
                    maxLength={maxLength}
                    disabled={disabled}
                    invalid={!!fieldError}
                    aria-describedby={[
                        hint ? `${fieldId}-hint` : null,
                        fieldError ? `${fieldId}-error` : null,
                    ].filter(Boolean).join(" ") || undefined}
                    onChange={(event) => onChange({ [key]: event.target.value })}
                />
            </FormField>
        );
    }

    const subtitleId = `website-section-${section.id}-subtitle`;
    const subtitleError = errors[`sections.${index}.subtitle`];
    const imageId = `website-section-${section.id}-image`;
    const imageError = errors[`sections.${index}.image`];

    return (
        <FormGridWide>
            <fieldset disabled={disabled} className="websiteBuilder__heroFields websiteBuilder__appearanceFields">
                {appearance.mode === "text" ? (
                    <>
                        <FormSection title="Identificação do núcleo" variant="inline" iconClass="fa fa-tag">
                            <FormGrid>
                                <FormGridWide>
                                    <FormField
                                        id={id("badge_text")}
                                        label="Texto da identificação"
                                        error={error("badge_text")}
                                        hint="Vazio utiliza o nome do site."
                                    >
                                        <TextInput
                                            id={id("badge_text")}
                                            value={appearance.badge_text}
                                            maxLength={120}
                                            invalid={!!error("badge_text")}
                                            aria-describedby={[`${id("badge_text")}-hint`, describedBy("badge_text")].filter(Boolean).join(" ")}
                                            onChange={(event) => patch({ badge_text: event.target.value })}
                                        />
                                    </FormField>
                                </FormGridWide>
                                <FormGridWide>
                                    <ToggleControl
                                        id={id("badge_visible")}
                                        label="Exibir identificação"
                                        size="small"
                                        checked={appearance.badge_visible}
                                        disabled={disabled}
                                        onChange={(checked) => patch({ badge_visible: checked })}
                                    />
                                </FormGridWide>
                                {select("badge_align", "Posição da identificação", alignments)}
                                {range("badge_radius", "Arredondamento da identificação (px)", 0, 60)}
                                {color("badge_color", "Cor do texto da identificação")}
                                {color("badge_background", "Cor de fundo da identificação")}
                            </FormGrid>
                        </FormSection>
                        <FormSection title="Título" variant="inline" iconClass="fa fa-font">
                            <FormGrid>
                                <FormGridWide>
                                    {textField("title", "Título", 160, `Para criar um link para esta seção, use #${section.id}.`)}
                                </FormGridWide>
                                {select("title_align", "Posição do título", alignments)}
                                {range("title_size", "Tamanho do título (px)", 24, 72)}
                                {color("title_color", "Cor do título")}
                            </FormGrid>
                        </FormSection>
                        <FormSection title="Texto de apoio" variant="inline" iconClass="fa fa-align-left">
                            <FormGrid>
                                <FormGridWide>
                                    <FormField id={subtitleId} label="Texto de apoio" error={subtitleError}>
                                        <TextArea
                                            id={subtitleId}
                                            name={`sections[${index}][subtitle]`}
                                            rows={3}
                                            value={section.subtitle}
                                            maxLength={500}
                                            disabled={disabled}
                                            aria-invalid={!!subtitleError}
                                            aria-describedby={subtitleError ? `${subtitleId}-error` : undefined}
                                            onChange={(event) => onChange({ subtitle: event.target.value })}
                                        />
                                    </FormField>
                                </FormGridWide>
                                {select("subtitle_align", "Posição do texto de apoio", alignments)}
                                {range("subtitle_size", "Tamanho do texto de apoio (px)", 14, 32)}
                                {color("subtitle_color", "Cor do texto de apoio")}
                            </FormGrid>
                        </FormSection>
                        <FormSection title="Botão" variant="inline" iconClass="fa fa-hand-o-up">
                            <FormGrid>
                                <FormGridWide>
                                    {textField("button_label", "Texto do botão", 60)}
                                </FormGridWide>
                                <FormGridWide>
                                    <BuilderLinkField
                                        id={`website-section-${section.id}-button_url`}
                                        name={`sections[${index}][button_url]`}
                                        value={section.button_url}
                                        options={linkOptions}
                                        error={errors[`sections.${index}.button_url`]}
                                        disabled={disabled}
                                        onChange={(button_url) => onChange({ button_url })}
                                    />
                                </FormGridWide>
                                {select("button_align", "Posição do botão", alignments)}
                                {range("button_radius", "Arredondamento do botão (px)", 0, 60)}
                                {color("button_color", "Cor do texto do botão")}
                                {color("button_background", "Cor de fundo do botão")}
                                {range("button_shadow", "Sombra do botão", 0, 40)}
                            </FormGrid>
                        </FormSection>
                    </>
                ) : null}
                <FormSection title="Apresentação e fundo" variant="inline" iconClass="fa fa-picture-o">
                    <FormGrid>
                        {select("mode", "Apresentação", [
                            { value: "text", label: "Textos sobre o fundo" },
                            { value: "banners", label: "Somente banners com links" },
                        ])}
                        {appearance.mode === "banners" ? (
                            <FormGridWide>
                                {textField("title", "Título da seção", 160, "Identifica a seção para quem usa leitor de tela.")}
                            </FormGridWide>
                        ) : null}
                        {range("height", "Altura mínima (px)", 240, 900)}
                        {appearance.mode === "text" ? range("content_width", "Largura máxima dos textos (px)", 320, 1160) : null}
                        {color("background_color", "Cor de fundo")}
                        <FormGridWide>
                            <FormField id={imageId} label="Imagem de fundo única" error={imageError}>
                                <ImageUploadField
                                    id={imageId}
                                    name={`sections[${index}][image]`}
                                    label="Imagem de fundo"
                                    file={section.image}
                                    currentUrl={section.image_url}
                                    removed={section.remove_image}
                                    previewAlt={section.image_alt}
                                    disabled={disabled}
                                    invalid={!!imageError}
                                    describedBy={imageError ? `${imageId}-error` : undefined}
                                    onChange={(image) => onChange({ image, remove_image: false })}
                                    onRemove={() => onChange({ image: null, remove_image: true })}
                                    onError={onImageError}
                                />
                            </FormField>
                        </FormGridWide>
                        <FormGridWide>
                            {textField("image_alt", "Texto alternativo da imagem", 255)}
                        </FormGridWide>
                        {appearance.mode === "text" ? (
                            <>
                                {color("overlay_color", "Cor da sobreposição")}
                                {range("overlay_opacity", "Intensidade da sobreposição (%)", 0, 100)}
                                <FormGridWide>
                                    <ToggleControl
                                        id={id("gradient_enabled")}
                                        label="Usar degradê"
                                        size="small"
                                        checked={appearance.gradient_enabled}
                                        disabled={disabled}
                                        onChange={(checked) => patch({ gradient_enabled: checked })}
                                    />
                                </FormGridWide>
                                {appearance.gradient_enabled ? (
                                    <>
                                        {select("gradient_direction", "Direção do degradê", [
                                            { value: "to bottom", label: "De cima para baixo" },
                                            { value: "to top", label: "De baixo para cima" },
                                            { value: "to right", label: "Da esquerda para a direita" },
                                            { value: "to left", label: "Da direita para a esquerda" },
                                        ])}
                                        {color("gradient_end_color", "Cor final do degradê")}
                                        {range("gradient_end_opacity", "Intensidade no fim do degradê (%)", 0, 100)}
                                    </>
                                ) : null}
                            </>
                        ) : null}
                    </FormGrid>
                </FormSection>
                <FormSection title="Imagens e carrossel" variant="inline" iconClass="fa fa-picture-o">
                    <FormGrid>
                        {select("image_fit", "Enquadramento das imagens", [
                            { value: "cover", label: "Preencher a área (pode recortar)" },
                            { value: "contain", label: "Mostrar a imagem inteira" },
                        ])}
                        {select("image_position", "Posição das imagens", [
                            { value: "center", label: "Centro" },
                            { value: "top", label: "Topo" },
                            { value: "bottom", label: "Base" },
                            { value: "left", label: "Esquerda" },
                            { value: "right", label: "Direita" },
                        ])}
                        <FormGridWide>
                            <ToggleControl
                                id={id("autoplay")}
                                label="Passar imagens automaticamente"
                                size="small"
                                checked={appearance.autoplay}
                                disabled={disabled}
                                onChange={(checked) => patch({ autoplay: checked })}
                            />
                        </FormGridWide>
                        {range("interval", "Tempo por imagem (segundos)", 3, 20)}
                    </FormGrid>
                </FormSection>
            </fieldset>
        </FormGridWide>
    );
}
