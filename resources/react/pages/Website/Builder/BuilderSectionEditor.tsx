import FormField from "@/components/Forms/FormField/FormField";
import FormGrid, { FormGridWide } from "@/components/Forms/FormGrid/FormGrid";
import TextArea from "@/components/Forms/TextArea/TextArea";
import TextInput from "@/components/Forms/TextInput/TextInput";
import Button from "@/components/UI/Button/Button";
import type { PillarCardAppearance, WebsiteSectionItem } from "@/components/Website/Website.types";
import BuilderHeroAppearance from "./BuilderHeroAppearance";
import BuilderAboutEditor from "./BuilderAboutEditor";
import BuilderHeroSlides from "./BuilderHeroSlides";
import BuilderPillarsEditor from "./BuilderPillarsEditor";
import BuilderIconField from "./BuilderIconField";
import { emptyItem, type BuilderEditTarget, type BuilderSection } from "./websiteBuilder";

type Props = {
    pillarCardTemplate: PillarCardAppearance;
    editTarget: BuilderEditTarget | null;
    onEditTarget: (target: BuilderEditTarget) => void;
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
    pillarCardTemplate,
    editTarget,
    onEditTarget,
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
    const usesButton = section.type === "guidance";
    const usesItems = ["guidance", "faq", "resources"].includes(section.type);
    const usesIcon = section.type === "resources";
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

    if (section.type === "hero") {
        return (
            <FormGrid>
                <BuilderHeroAppearance
                    section={section}
                    index={index}
                    disabled={disabled}
                    errors={errors}
                    onChange={onChange}
                    onImageError={onImageError}
                />
                <BuilderHeroSlides
                    section={section}
                    index={index}
                    disabled={disabled}
                    errors={errors}
                    onChange={onChange}
                    onImageError={onImageError}
                />
            </FormGrid>
        );
    }

    if (section.type === "about") {
        return (
            <BuilderAboutEditor
                section={section}
                index={index}
                disabled={disabled}
                errors={errors}
                onChange={onChange}
                onImageError={onImageError}
                onEditorError={onEditorError}
            />
        );
    }

    if (["pillars", "audience", "features"].includes(section.type)) {
        return (
            <BuilderPillarsEditor
                section={section}
                index={index}
                disabled={disabled}
                errors={errors}
                template={pillarCardTemplate}
                editTarget={editTarget}
                onEditTarget={onEditTarget}
                onChange={onChange}
            />
        );
    }

    return (
        <FormGrid>
            <FormGridWide>
                <FormField
                    id={id("title")}
                    label="Título"
                    error={error("title")}
                    hint={`Para criar um link para esta seção, use #${section.id}.`}
                >
                    <TextInput
                        id={id("title")}
                        name={`sections[${index}][title]`}
                        value={section.title}
                        maxLength={160}
                        invalid={!!error("title")}
                        aria-describedby={describedBy("title", true)}
                        onChange={(event) => onChange({ title: event.target.value })}
                    />
                </FormField>
            </FormGridWide>
            <FormGridWide>
                <FormField
                    id={id("subtitle")}
                    label="Subtítulo"
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
                        Sem registros disponíveis, esta seção fica oculta no site público. Na prévia, continua disponível para edição.{" "}
                        {section.type === "team"
                            ? "Mostra os profissionais ativos marcados como \"Aparece no site\" no cadastro de profissionais."
                            : section.type === "events"
                                ? "Mostra as publicações publicadas que possuem datas e ainda não terminaram."
                                : "Mostra as publicações mais recentes, com os destaques primeiro."}
                    </p>
                </FormGridWide>
            ) : null}
            {usesItems ? (
                <FormGridWide>
                    <fieldset className="websiteBuilder__items">
                        <legend>{section.type === "faq" ? "Perguntas e respostas" : "Itens da seção"}</legend>
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
                                            {usesIcon ? (
                                                <div className="websiteBuilder__itemWide">
                                                    <BuilderIconField
                                                        id={itemId("icon")}
                                                        name={`sections[${index}][items][${itemIndex}][icon]`}
                                                        label={`Ícone do item ${position}`}
                                                        value={item.icon}
                                                        error={itemError("icon")}
                                                        disabled={disabled}
                                                        onChange={(icon) => updateItem(itemIndex, { icon })}
                                                    />
                                                </div>
                                            ) : null}
                                            <FormField
                                                id={itemId("title")}
                                                label={section.type === "faq" ? `Pergunta ${position}` : `Título do item ${position}`}
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
                                            <div className="websiteBuilder__itemWide">
                                                <FormField
                                                    id={itemId("text")}
                                                    label={section.type === "faq" ? `Resposta ${position}` : `Texto do item ${position}`}
                                                    error={itemError("text")}
                                                >
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
                                            {section.type === "resources" ? (
                                                <div className="websiteBuilder__itemWide">
                                                    <FormField
                                                        id={itemId("url")}
                                                        label={`Link do material ${position}`}
                                                        error={itemError("url")}
                                                        hint="Informe o endereço de um documento, formulário ou página institucional."
                                                    >
                                                        <TextInput
                                                            id={itemId("url")}
                                                            name={`sections[${index}][items][${itemIndex}][url]`}
                                                            value={item.url}
                                                            maxLength={500}
                                                            invalid={!!itemError("url")}
                                                            aria-describedby={[
                                                                `${itemId("url")}-hint`,
                                                                itemError("url") ? `${itemId("url")}-error` : null,
                                                            ].filter(Boolean).join(" ")}
                                                            onChange={(event) => updateItem(itemIndex, { url: event.target.value })}
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
                            tone="white"
                            className="removableField__add"
                            labelIconClass="fa fa-plus"
                            label="Item"
                            title="Adicionar item"
                            aria-label="Adicionar item"
                            disabled={disabled || section.items.length >= 12}
                            onClick={() => onChange({ items: [...section.items, emptyItem(section.type)] })}
                        />
                    </fieldset>
                </FormGridWide>
            ) : null}
        </FormGrid>
    );
}
