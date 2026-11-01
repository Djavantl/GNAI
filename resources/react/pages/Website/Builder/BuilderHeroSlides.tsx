import FormField from "@/components/Forms/FormField/FormField";
import FormGrid, { FormGridWide } from "@/components/Forms/FormGrid/FormGrid";
import FormSection from "@/components/Forms/FormSection/FormSection";
import ImageUploadField from "@/components/Forms/ImageUploadField/ImageUploadField";
import TextInput from "@/components/Forms/TextInput/TextInput";
import Button from "@/components/UI/Button/Button";
import "@/components/Forms/RemovableField/RemovableField.css";
import type { BuilderSection, BuilderSlide } from "./websiteBuilder";

export default function BuilderHeroSlides({
    section,
    index,
    errors,
    disabled,
    onChange,
    onImageError,
}: {
    section: BuilderSection;
    index: number;
    errors: Record<string, string | undefined>;
    disabled: boolean;
    onChange: (patch: Partial<BuilderSection>) => void;
    onImageError: (message: string) => void;
}): React.JSX.Element {
    const slides = section.slides;

    function updateSlide(id: string, patch: Partial<BuilderSlide>): void {
        onChange({ slides: slides.map((slide) => slide.id === id ? { ...slide, ...patch } : slide) });
    }

    function moveSlide(index: number, offset: number): void {
        const next = [...slides];
        const target = index + offset;
        if (target < 0 || target >= next.length) return;
        [next[index], next[target]] = [next[target], next[index]];
        onChange({ slides: next });
    }

    return (
        <FormGridWide>
            <FormSection title="Lista de imagens" variant="inline" iconClass="fa fa-picture-o">
                {errors[`sections.${index}.slides`] ? (
                    <p className="websiteBuilder__itemsError" role="alert">{errors[`sections.${index}.slides`]}</p>
                ) : null}
                <ol className="websiteBuilder__slides">
                    {slides.map((slide, slideIndex) => {
                        const id = (field: string) => `website-section-${section.id}-slide-${slide.id}-${field}`;
                        const error = (field: string) => errors[`sections.${index}.slides.${slideIndex}.${field}`];
                        return (
                            <li key={slide.id} className="websiteBuilder__slide">
                                <h3>Imagem {slideIndex + 1}</h3>
                                <FormGrid>
                                    <FormGridWide>
                                        <FormField id={id("image")} label="Imagem" error={error("image")}>
                                            <ImageUploadField
                                                id={id("image")}
                                                name={`sections[${index}][slides][${slideIndex}][image]`}
                                                label="Imagem do banner"
                                                file={slide.image}
                                                currentUrl={slide.image_url}
                                                removed={slide.remove_image}
                                                previewAlt={slide.alt}
                                                disabled={disabled}
                                                invalid={!!error("image")}
                                                describedBy={error("image") ? `${id("image")}-error` : undefined}
                                                onChange={(image) => updateSlide(slide.id, { image, remove_image: false })}
                                                onRemove={() => updateSlide(slide.id, { image: null, remove_image: true })}
                                                onError={onImageError}
                                            />
                                        </FormField>
                                    </FormGridWide>
                                    <FormField id={id("alt")} label="Descrição da imagem" error={error("alt")} requiredIndicator>
                                        <TextInput
                                            id={id("alt")}
                                            value={slide.alt}
                                            maxLength={255}
                                            required
                                            disabled={disabled}
                                            invalid={!!error("alt")}
                                            aria-describedby={error("alt") ? `${id("alt")}-error` : undefined}
                                            onChange={(event) => updateSlide(slide.id, { alt: event.target.value })}
                                        />
                                    </FormField>
                                    <FormField id={id("url")} label="Link do banner" error={error("url")}>
                                        <TextInput
                                            id={id("url")}
                                            value={slide.url}
                                            maxLength={500}
                                            disabled={disabled}
                                            invalid={!!error("url")}
                                            aria-describedby={error("url") ? `${id("url")}-error` : undefined}
                                            onChange={(event) => updateSlide(slide.id, { url: event.target.value })}
                                        />
                                    </FormField>
                                </FormGrid>
                                <div className="websiteBuilder__slideActions">
                                    <Button
                                        label="Subir"
                                        leftIconClass="fa fa-arrow-up"
                                        tone="white"
                                        size="sm"
                                        disabled={disabled || slideIndex === 0}
                                        onClick={() => moveSlide(slideIndex, -1)}
                                    />
                                    <Button
                                        label="Descer"
                                        leftIconClass="fa fa-arrow-down"
                                        tone="white"
                                        size="sm"
                                        disabled={disabled || slideIndex === slides.length - 1}
                                        onClick={() => moveSlide(slideIndex, 1)}
                                    />
                                    <Button
                                        label="Remover imagem"
                                        leftIconClass="fa fa-trash-o"
                                        tone="dark-red"
                                        size="sm"
                                        disabled={disabled}
                                        onClick={() => onChange({ slides: slides.filter((item) => item.id !== slide.id) })}
                                    />
                                </div>
                            </li>
                        );
                    })}
                </ol>
                <Button
                    type="button"
                    tone="white"
                    className="removableField__add"
                    labelIconClass="fa fa-plus"
                    label="Imagem"
                    title="Adicionar imagem"
                    aria-label="Adicionar imagem"
                    disabled={disabled || slides.length >= 8}
                    onClick={() => onChange({
                        slides: [...slides, {
                            id: `slide-${Date.now().toString(36)}`,
                            image_url: null,
                            image: null,
                            remove_image: false,
                            alt: "",
                            url: "",
                        }],
                    })}
                />
            </FormSection>
        </FormGridWide>
    );
}
