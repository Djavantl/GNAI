import DatePicker from "@/components/Forms/DatePicker/DatePicker";
import FormField from "@/components/Forms/FormField/FormField";
import FormGrid, { FormGridWide } from "@/components/Forms/FormGrid/FormGrid";
import FormSection from "@/components/Forms/FormSection/FormSection";
import SelectField from "@/components/Forms/SelectField/SelectField";
import TextInput from "@/components/Forms/TextInput/TextInput";
import ToggleControl from "@/components/Forms/ToggleControl/ToggleControl";
import InfoTooltip from "@/components/UI/InfoTooltip/InfoTooltip";
import { describedBy, type PublicationFieldsProps } from "./PublicationFields.types";

export function slugify(value: string): string {
    return value
        .normalize("NFD")
        .replace(/[̀-ͯ]/g, "")
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, "-")
        .replace(/^-+|-+$/g, "")
        .slice(0, 160)
        .replace(/-+$/g, "");
}

/** Situação, data, destaque, endereço e dados para buscadores. */
export default function PublicationPublishingFields({
    data,
    errors,
    disabled,
    update,
    publicBaseUrl,
}: PublicationFieldsProps & {
    publicBaseUrl: string;
}): React.JSX.Element {
    const suggestedSlug = slugify(data.title) || "titulo-da-publicacao";

    return (
        <>
            <FormSection id="publication-status" title="Publicação" variant="inline" iconClass="fa fa-bullhorn">
                <FormGrid>
                    <FormField id="publication-active" label="Situação" error={errors.is_active} requiredIndicator>
                        <SelectField
                            id="publication-active"
                            name="is_active"
                            label="Situação"
                            required
                            value={data.is_active ? "1" : "0"}
                            options={[
                                { value: "1", label: "Publicado" },
                                { value: "0", label: "Rascunho" },
                            ]}
                            aria-invalid={!!errors.is_active}
                            aria-describedby={describedBy(errors, "is_active", "publication-active")}
                            onChange={(event) => update("is_active", event.target.value === "1")}
                        />
                    </FormField>
                    <FormField
                        id="publication-published-at"
                        label="Data de publicação"
                        error={errors.published_at}
                        hint="Em branco, vale o momento em que a publicação for para o ar. Datas futuras só aparecem no site a partir do dia escolhido."
                        hintLabel="Sobre a data de publicação"
                    >
                        <DatePicker
                            id="publication-published-at"
                            name="published_at"
                            label="Data de publicação"
                            value={data.published_at}
                            disabled={disabled}
                            invalid={!!errors.published_at}
                            describedBy={describedBy(errors, "published_at", "publication-published-at")}
                            onChange={(value) => update("published_at", value)}
                        />
                    </FormField>
                    <FormGridWide>
                        <ToggleControl
                            id="publication-featured"
                            name="is_featured"
                            size="small"
                            label="Exibir em destaque"
                            labelAddon={(
                                <InfoTooltip text="Publicações em destaque aparecem primeiro na página inicial do site." />
                            )}
                            checked={data.is_featured}
                            disabled={disabled}
                            onChange={(checked) => update("is_featured", checked)}
                        />
                    </FormGridWide>
                </FormGrid>
            </FormSection>
            <FormSection id="publication-seo" title="Endereço e buscadores" variant="inline" iconClass="fa fa-globe">
                <FormGrid>
                    <FormGridWide>
                        <FormField
                            id="publication-slug"
                            label="Endereço da página"
                            error={errors.slug}
                            hint={`Em branco, é gerado a partir do título: ${publicBaseUrl}/${data.slug || suggestedSlug}`}
                            hintLabel="Sobre o endereço da página"
                        >
                            <div className="publicationForm__slug">
                                <span className="publicationForm__slugPrefix" aria-hidden="true">/site/publicacoes/</span>
                                <TextInput
                                    id="publication-slug"
                                    name="slug"
                                    value={data.slug}
                                    maxLength={160}
                                    placeholder={suggestedSlug}
                                    invalid={!!errors.slug}
                                    aria-describedby={describedBy(errors, "slug", "publication-slug")}
                                    onChange={(event) => update("slug", event.target.value.toLowerCase().replace(/\s+/g, "-"))}
                                    onBlur={() => update("slug", slugify(data.slug))}
                                />
                            </div>
                        </FormField>
                    </FormGridWide>
                    <FormField
                        id="publication-seo-title"
                        label="Título para buscadores"
                        error={errors.seo_title}
                        hint="Vazio usa o título da publicação."
                        hintLabel="Sobre o título para buscadores"
                    >
                        <TextInput
                            id="publication-seo-title"
                            name="seo_title"
                            value={data.seo_title}
                            maxLength={255}
                            invalid={!!errors.seo_title}
                            aria-describedby={describedBy(errors, "seo_title", "publication-seo-title")}
                            onChange={(event) => update("seo_title", event.target.value)}
                        />
                    </FormField>
                    <FormField
                        id="publication-seo-description"
                        label="Descrição para buscadores"
                        error={errors.seo_description}
                        hint="Vazio usa o resumo."
                        hintLabel="Sobre a descrição para buscadores"
                    >
                        <TextInput
                            id="publication-seo-description"
                            name="seo_description"
                            value={data.seo_description}
                            maxLength={300}
                            invalid={!!errors.seo_description}
                            aria-describedby={describedBy(errors, "seo_description", "publication-seo-description")}
                            onChange={(event) => update("seo_description", event.target.value)}
                        />
                    </FormField>
                </FormGrid>
            </FormSection>
        </>
    );
}
