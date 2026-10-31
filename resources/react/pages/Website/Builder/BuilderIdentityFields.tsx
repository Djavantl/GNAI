import FormField from "@/components/Forms/FormField/FormField";
import FormGrid, { FormGridWide } from "@/components/Forms/FormGrid/FormGrid";
import FormSection from "@/components/Forms/FormSection/FormSection";
import ImageUploadField from "@/components/Forms/ImageUploadField/ImageUploadField";
import TextArea from "@/components/Forms/TextArea/TextArea";
import TextInput from "@/components/Forms/TextInput/TextInput";
import type { BuilderFormData } from "./websiteBuilder";

type TextKey = Exclude<keyof BuilderFormData, "logo" | "remove_logo" | "sections">;

type Props = {
    data: BuilderFormData;
    currentLogoUrl: string | null;
    errors: Record<string, string | undefined>;
    disabled: boolean;
    onChange: <K extends keyof BuilderFormData>(key: K, value: BuilderFormData[K]) => void;
    onLogoChange: (file: File | null, remove: boolean) => void;
    onImageError: (message: string) => void;
};

const socialFields: { key: TextKey; label: string; placeholder: string }[] = [
    { key: "instagram_url", label: "Instagram", placeholder: "https://instagram.com/..." },
    { key: "facebook_url", label: "Facebook", placeholder: "https://facebook.com/..." },
    { key: "youtube_url", label: "YouTube", placeholder: "https://youtube.com/..." },
    { key: "website_url", label: "Site institucional", placeholder: "https://..." },
];

/** Identidade white label: nome, logo, cor, contatos e redes sociais. */
export default function BuilderIdentityFields({
    data,
    currentLogoUrl,
    errors,
    disabled,
    onChange,
    onLogoChange,
    onImageError,
}: Props): React.JSX.Element {
    const describedBy = (name: string, hint = false) =>
        [hint ? `website-${name}-hint` : null, errors[name] ? `website-${name}-error` : null]
            .filter(Boolean)
            .join(" ") || undefined;

    function text(key: TextKey, label: string, options: { placeholder?: string; maxLength?: number; type?: string; required?: boolean; hint?: string } = {}): React.JSX.Element {
        return (
            <FormField
                id={`website-${key}`}
                label={label}
                error={errors[key]}
                hint={options.hint}
                requiredIndicator={options.required}
            >
                <TextInput
                    id={`website-${key}`}
                    name={key}
                    type={options.type ?? "text"}
                    value={data[key]}
                    maxLength={options.maxLength ?? 255}
                    placeholder={options.placeholder}
                    required={options.required}
                    invalid={!!errors[key]}
                    aria-describedby={describedBy(key, !!options.hint)}
                    onChange={(event) => onChange(key, event.target.value)}
                />
            </FormField>
        );
    }

    return (
        <>
            <FormSection id="website-identity" title="Identidade" variant="inline" iconClass="fa fa-flag">
                <FormGrid>
                    {text("site_name", "Nome do site", { maxLength: 120, required: true, placeholder: "Ex.: NAPNE" })}
                    {text("tagline", "Slogan", { placeholder: "Frase curta exibida abaixo do nome" })}
                    <FormField
                        id="website-primary_color"
                        label="Cor principal"
                        error={errors.primary_color}
                        requiredIndicator
                    >
                        <div className="websiteBuilder__color">
                            <input
                                type="color"
                                aria-label="Selecionar cor principal"
                                title="Selecionar cor principal"
                                value={/^#[0-9a-f]{6}$/i.test(data.primary_color) ? data.primary_color : "#322b75"}
                                disabled={disabled}
                                onChange={(event) => onChange("primary_color", event.target.value)}
                            />
                            <TextInput
                                id="website-primary_color"
                                name="primary_color"
                                value={data.primary_color}
                                maxLength={7}
                                placeholder="#322b75"
                                required
                                invalid={!!errors.primary_color}
                                aria-describedby={describedBy("primary_color")}
                                onChange={(event) => onChange("primary_color", event.target.value)}
                            />
                        </div>
                    </FormField>
                    <FormField
                        id="website-logo"
                        label="Logo"
                        error={errors.logo}
                    >
                        <ImageUploadField
                            id="website-logo"
                            name="logo"
                            label="Logo"
                            variant="logo"
                            file={data.logo}
                            currentUrl={currentLogoUrl}
                            removed={data.remove_logo}
                            previewAlt={`Logo de ${data.site_name || "site"}`}
                            disabled={disabled}
                            invalid={!!errors.logo}
                            describedBy={describedBy("logo")}
                            onChange={(file) => onLogoChange(file, false)}
                            onRemove={() => onLogoChange(null, true)}
                            onError={onImageError}
                        />
                    </FormField>
                </FormGrid>
            </FormSection>
            <FormSection id="website-contact" title="Contato" variant="inline" iconClass="fa fa-envelope-o">
                <FormGrid>
                    {text("contact_email", "E-mail", { type: "email", placeholder: "contato@instituicao.edu.br" })}
                    {text("contact_phone", "Telefone", { maxLength: 40, placeholder: "(77) 0000-0000" })}
                    <FormGridWide>
                        {text("address", "Endereço", { placeholder: "Rua, número, bairro, cidade" })}
                    </FormGridWide>
                </FormGrid>
            </FormSection>
            <FormSection id="website-social" title="Redes sociais e rodapé" variant="inline" iconClass="fa fa-share-alt">
                <FormGrid>
                    {socialFields.map((field) => (
                        <div key={field.key}>
                            {text(field.key, field.label, { type: "url", placeholder: field.placeholder })}
                        </div>
                    ))}
                    <FormGridWide>
                        <FormField id="website-footer_text" label="Texto do rodapé" error={errors.footer_text}>
                            <TextArea
                                id="website-footer_text"
                                name="footer_text"
                                rows={3}
                                maxLength={500}
                                value={data.footer_text}
                                aria-invalid={!!errors.footer_text}
                                aria-describedby={describedBy("footer_text")}
                                onChange={(event) => onChange("footer_text", event.target.value)}
                            />
                        </FormField>
                    </FormGridWide>
                </FormGrid>
            </FormSection>
        </>
    );
}
