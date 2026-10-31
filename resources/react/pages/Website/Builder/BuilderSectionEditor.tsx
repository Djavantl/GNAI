import FormField from "@/components/Forms/FormField/FormField";
import FormGrid, { FormGridWide } from "@/components/Forms/FormGrid/FormGrid";
import ImageUploadField from "@/components/Forms/ImageUploadField/ImageUploadField";
import SelectField from "@/components/Forms/SelectField/SelectField";
import SummernoteEditor from "@/components/Forms/SummernoteEditor/SummernoteEditor";
import TextArea from "@/components/Forms/TextArea/TextArea";
import TextInput from "@/components/Forms/TextInput/TextInput";
import Button from "@/components/UI/Button/Button";
import type { WebsiteSectionItem } from "@/components/Website/Website.types";
import { emptyItem, featureIconOptions, type BuilderSection } from "./websiteBuilder";

type Props = {
    section: BuilderSection;
    index: number;
    errors: Record<string, string | undefined>;
    disabled: boolean;
    onChange: (patch: Partial<BuilderSection>) => void;
    onImageError: (message: string) => void;
    onEditorError: () => void;
};

/** Campos editáveis da seção selecionada, conforme o tipo do bloco. */
export default function BuilderSectionEditor({
    section,
    index,
    errors,
    disabled,
    onChange,
    onImageError,
    onEditorError,
}: Props): React.JSX.Element {
    const id = (name: string) => `website-section-${section.id}-${name}`;
    const error = (name: string) => errors[`sections.${index}.${name}`];
    const describedBy = (name: string, hint = false) =>
        [hint ? `${id(name)}-hint` : null, error(name) ? `${id(name)}-error` : null]
            .filter(Boolean)
            .join(" ") || undefined;
    const usesSubtitle = section.type !== "about" && section.type !== "highlights";
    const usesButton = ["hero", "about", "cta"].includes(section.type);
    const usesLimit = ["team", "events", "publications"].includes(section.type);

    function updateItem(itemIndex: number, patch: Partial<WebsiteSectionItem>): void {
        onChange({
            items: section.items.map((item, current) => (current === itemIndex ? { ...item, ...patch } : item)),
        });
    }

    function moveItem(itemIndex: number, offset: -1 | 1): void {
        const items = [...section.items];
        const target = itemIndex + offset;

        if (target < 0 || target >= items.length) return;

        [items[itemIndex], items[target]] = [items[target], items[itemIndex]];
        onChange({ items });
    }

    return (
        <FormGrid>
            <FormGridWide>
                <FormField id={id("title")} label="Título" error={error("title")}>
                    <TextInput
                        id={id("title")}
                        name={`sections[${index}][title]`}
                        value={section.title}
                        maxLength={160}
                        invalid={!!error("title")}
                        aria-describedby={describedBy("title")}
                        onChange={(event) => onChange({ title: event.target.value })}
                    />
                </FormField>
            </FormGridWide>
            {usesSubtitle ? (
                <FormGridWide>
                    <FormField
                        id={id("subtitle")}
                        label={section.type === "hero" ? "Texto de apoio" : "Subtítulo"}
                        error={error("subtitle")}
                    >
                        <TextArea
                            id={id("subtitle")}
                            name={`sections[${index}][subtitle]`}
                            rows={3}
                            value={section.subtitle}
                            maxLength={500}
                            aria-invalid={!!error("subtitle")}
                            aria-describedby={describedBy("subtitle")}
                            onChange={(event) => onChange({ subtitle: event.target.value })}
                        />
                    </FormField>
                </FormGridWide>
            ) : null}
            {section.type === "about" ? (
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
                            onChange={(html) => onChange({ body: html })}
                        />
                    </FormField>
                </FormGridWide>
            ) : null}
            {section.type === "hero" || section.type === "about" ? (
                <FormGridWide>
                    <FormField
                        id={id("image")}
                        label={section.type === "hero" ? "Imagem de fundo" : "Imagem"}
                        error={error("image")}
                        hint={section.type === "about" ? "Opcional. JPG, PNG ou WEBP com até 4 MB." : undefined}
                    >
                        <ImageUploadField
                            id={id("image")}
                            name={`sections[${index}][image]`}
                            label="Imagem"
                            file={section.image}
                            currentUrl={section.image_url}
                            removed={section.remove_image}
                            previewAlt={section.image_alt}
                            disabled={disabled}
                            invalid={!!error("image")}
                            describedBy={describedBy("image", section.type === "about")}
                            onChange={(file) => onChange({ image: file, remove_image: false })}
                            onRemove={() => onChange({ image: null, remove_image: true })}
                            onError={onImageError}
                        />
                    </FormField>
                </FormGridWide>
            ) : null}
            {section.type === "about" ? (
                <FormGridWide>
                    <FormField
                        id={id("image_alt")}
                        label="Texto alternativo da imagem"
                        error={error("image_alt")}
                        hint="Descreva a imagem para quem usa leitor de tela. Obrigatório quando houver imagem."
                        requiredIndicator={!!section.image || (!!section.image_url && !section.remove_image)}
                    >
                        <TextInput
                            id={id("image_alt")}
                            name={`sections[${index}][image_alt]`}
                            value={section.image_alt}
                            maxLength={255}
                            placeholder="Ex.: Equipe reunida na sala do núcleo"
                            invalid={!!error("image_alt")}
                            aria-describedby={describedBy("image_alt", true)}
                            onChange={(event) => onChange({ image_alt: event.target.value })}
                        />
                    </FormField>
                </FormGridWide>
            ) : null}
            {usesButton ? (
                <>
                    <FormField id={id("button_label")} label="Texto do botão" error={error("button_label")}>
                        <TextInput
                            id={id("button_label")}
                            name={`sections[${index}][button_label]`}
                            value={section.button_label}
                            maxLength={60}
                            placeholder="Ex.: Saiba mais"
                            invalid={!!error("button_label")}
                            aria-describedby={describedBy("button_label")}
                            onChange={(event) => onChange({ button_label: event.target.value })}
                        />
                    </FormField>
                    <FormField id={id("button_url")} label="Link do botão" error={error("button_url")}>
                        <TextInput
                            id={id("button_url")}
                            name={`sections[${index}][button_url]`}
                            value={section.button_url}
                            maxLength={500}
                            placeholder="/site/equipe"
                            invalid={!!error("button_url")}
                            aria-describedby={describedBy("button_url")}
                            onChange={(event) => onChange({ button_url: event.target.value })}
                        />
                    </FormField>
                </>
            ) : null}
            {usesLimit ? (
                <FormField
                    id={id("limit")}
                    label="Quantidade exibida"
                    error={error("limit")}
                    hint="Entre 1 e 12 itens."
                >
                    <TextInput
                        id={id("limit")}
                        name={`sections[${index}][limit]`}
                        type="number"
                        min={1}
                        max={12}
                        value={String(section.limit)}
                        invalid={!!error("limit")}
                        aria-describedby={describedBy("limit", true)}
                        onChange={(event) => onChange({ limit: Number(event.target.value) || 1 })}
                    />
                </FormField>
            ) : null}
            {section.type === "contact" ? (
                <FormGridWide>
                    <p className="websiteBuilder__note">
                        <i className="fa fa-info-circle" aria-hidden="true" /> Endereço, telefone, e-mail e redes sociais
                        vêm do item Identidade, o primeiro da lista de seções.
                    </p>
                </FormGridWide>
            ) : null}
            {usesLimit ? (
                <FormGridWide>
                    <p className="websiteBuilder__note">
                        <i className="fa fa-info-circle" aria-hidden="true" />{" "}
                        {section.type === "team"
                            ? "Mostra os profissionais ativos marcados como \"Aparece no site\" no cadastro de profissionais."
                            : section.type === "events"
                                ? "Mostra as publicações publicadas que possuem datas e ainda não terminaram."
                                : "Mostra as publicações mais recentes, com os destaques primeiro."}
                    </p>
                </FormGridWide>
            ) : null}
            {section.type === "highlights" || section.type === "features" ? (
                <FormGridWide>
                    <fieldset className="websiteBuilder__items">
                        <legend>{section.type === "highlights" ? "Números" : "Itens"}</legend>
                        {error("items") ? (
                            <p className="websiteBuilder__itemsError" id={`${id("items")}-error`}>{error("items")}</p>
                        ) : null}
                        <ol>
                            {section.items.map((item, itemIndex) => {
                                const itemId = (name: string) => `${id("item")}-${itemIndex}-${name}`;
                                const itemError = (name: string) => error(`items.${itemIndex}.${name}`);
                                const position = itemIndex + 1;

                                return (
                                    <li key={itemIndex} className="websiteBuilder__item">
                                        <div className="websiteBuilder__itemFields">
                                            {section.type === "highlights" ? (
                                                <FormField id={itemId("value")} label={`Número ${position}`} error={itemError("value")}>
                                                    <TextInput
                                                        id={itemId("value")}
                                                        name={`sections[${index}][items][${itemIndex}][value]`}
                                                        value={item.value}
                                                        maxLength={30}
                                                        placeholder="+100"
                                                        invalid={!!itemError("value")}
                                                        aria-describedby={itemError("value") ? `${itemId("value")}-error` : undefined}
                                                        onChange={(event) => updateItem(itemIndex, { value: event.target.value })}
                                                    />
                                                </FormField>
                                            ) : (
                                                <FormField id={itemId("icon")} label={`Ícone do item ${position}`} error={itemError("icon")}>
                                                    <SelectField
                                                        id={itemId("icon")}
                                                        name={`sections[${index}][items][${itemIndex}][icon]`}
                                                        label={`Ícone do item ${position}`}
                                                        value={item.icon}
                                                        placeholder="Sem ícone"
                                                        options={featureIconOptions}
                                                        iconOnly
                                                        aria-invalid={!!itemError("icon")}
                                                        aria-describedby={itemError("icon") ? `${itemId("icon")}-error` : undefined}
                                                        onChange={(event) => updateItem(itemIndex, { icon: event.target.value })}
                                                    />
                                                </FormField>
                                            )}
                                            <FormField
                                                id={itemId("title")}
                                                label={section.type === "highlights" ? `Descrição do número ${position}` : `Título do item ${position}`}
                                                error={itemError("title")}
                                            >
                                                <TextInput
                                                    id={itemId("title")}
                                                    name={`sections[${index}][items][${itemIndex}][title]`}
                                                    value={item.title}
                                                    maxLength={120}
                                                    invalid={!!itemError("title")}
                                                    aria-describedby={itemError("title") ? `${itemId("title")}-error` : undefined}
                                                    onChange={(event) => updateItem(itemIndex, { title: event.target.value })}
                                                />
                                            </FormField>
                                            {section.type === "features" ? (
                                                <div className="websiteBuilder__itemWide">
                                                    <FormField id={itemId("text")} label={`Texto do item ${position}`} error={itemError("text")}>
                                                        <TextArea
                                                            id={itemId("text")}
                                                            name={`sections[${index}][items][${itemIndex}][text]`}
                                                            rows={2}
                                                            value={item.text}
                                                            maxLength={300}
                                                            aria-invalid={!!itemError("text")}
                                                            aria-describedby={itemError("text") ? `${itemId("text")}-error` : undefined}
                                                            onChange={(event) => updateItem(itemIndex, { text: event.target.value })}
                                                        />
                                                    </FormField>
                                                </div>
                                            ) : null}
                                        </div>
                                        <div className="websiteBuilder__itemActions">
                                            <Button
                                                size="xs"
                                                tone="white"
                                                iconOnly
                                                leftIconClass="fa fa-arrow-up"
                                                aria-label={`Mover item ${position} para cima`}
                                                disabled={disabled || itemIndex === 0}
                                                onClick={() => moveItem(itemIndex, -1)}
                                            />
                                            <Button
                                                size="xs"
                                                tone="white"
                                                iconOnly
                                                leftIconClass="fa fa-arrow-down"
                                                aria-label={`Mover item ${position} para baixo`}
                                                disabled={disabled || itemIndex === section.items.length - 1}
                                                onClick={() => moveItem(itemIndex, 1)}
                                            />
                                            <Button
                                                size="xs"
                                                tone="dark-red"
                                                iconOnly
                                                leftIconClass="fa fa-trash-o"
                                                aria-label={`Remover item ${position}`}
                                                disabled={disabled}
                                                onClick={() => onChange({ items: section.items.filter((_, current) => current !== itemIndex) })}
                                            />
                                        </div>
                                    </li>
                                );
                            })}
                        </ol>
                        <Button
                            size="sm"
                            tone="list-action"
                            leftIconClass="fa fa-plus"
                            label="Adicionar item"
                            disabled={disabled || section.items.length >= 12}
                            onClick={() => onChange({ items: [...section.items, emptyItem(section.type)] })}
                        />
                    </fieldset>
                </FormGridWide>
            ) : null}
        </FormGrid>
    );
}
