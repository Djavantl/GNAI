import FormField from "@/components/Forms/FormField/FormField";
import FormGrid, { FormGridWide } from "@/components/Forms/FormGrid/FormGrid";
import FormSection from "@/components/Forms/FormSection/FormSection";
import TextInput from "@/components/Forms/TextInput/TextInput";
import TextArea from "@/components/Forms/TextArea/TextArea";
import ToggleControl from "@/components/Forms/ToggleControl/ToggleControl";
import Button from "@/components/UI/Button/Button";
import type { ContactAppearance, TeamAppearance, PublicationsAppearance, PillarCardAppearance, WebsiteSectionItem } from "@/components/Website/Website.types";
import "@/components/Forms/RemovableField/RemovableField.css";
import {
    AppearanceColorField, AppearanceRangeField, AppearanceSelectField, appearanceAlignments,
} from "./BuilderAppearanceFields";
import BuilderLinkField, { type BuilderLinkOption } from "./BuilderLinkField";
import BuilderPillarItemEditor from "./BuilderPillarItemEditor";
import { emptyItem, type BuilderEditTarget, type BuilderSection } from "./websiteBuilder";

export default function BuilderPillarsEditor({
    section, index, errors, disabled, template, linkOptions, editTarget, onChange, onEditTarget,
}: {
    section: BuilderSection;
    linkOptions: BuilderLinkOption[];
    index: number;
    errors: Record<string, string | undefined>;
    disabled: boolean;
    template: PillarCardAppearance;
    editTarget: BuilderEditTarget | null;
    onEditTarget: (target: BuilderEditTarget) => void;
    onChange: (patch: Partial<BuilderSection>) => void;
}): React.JSX.Element | null {
    const contact = section.type === "contact";
    const team = section.type === "team";
    const events = section.type === "events";
    const publications = section.type === "publications" || events;
    const numbered = section.type === "guidance";
    const appearance = contact ? section.contact : team ? section.team : events ? section.events : publications ? section.publications : numbered ? section.guidance : section.pillars;
    if (!appearance) return null;

    const id = (key: string) => `website-section-${section.id}-${key}`;
    const error = (key: string) => errors[`sections.${index}.${key}`];
    const scope = contact ? "contact" : team ? "team" : events ? "events" : publications ? "publications" : numbered ? "guidance" : "pillars";
    const values: Partial<PublicationsAppearance & TeamAppearance & ContactAppearance> = appearance;

    function patch(changes: Partial<PublicationsAppearance & TeamAppearance & ContactAppearance>): void {
        if (contact) {
            onChange({ contact: { ...section.contact!, ...changes } });
            return;
        }
        if (team) {
            onChange({ team: { ...section.team!, ...changes } });
            return;
        }
        if (events) {
            onChange({ events: { ...section.events!, ...changes } });
            return;
        }
        if (publications) {
            onChange({ publications: { ...section.publications!, ...changes } });
            return;
        }
        if (numbered) {
            onChange({ guidance: { ...section.guidance!, ...changes } });
            return;
        }

        const { layout, ...settings } = changes;
        onChange({ pillars: {
            ...section.pillars!,
            ...settings,
            ...(layout === "grid" || layout === "list" ? { layout } : {}),
        } });
    }

    function color(key: keyof (PublicationsAppearance & TeamAppearance & ContactAppearance), label: string): React.JSX.Element {
        return (
            <AppearanceColorField
                id={id(`${scope}-${key}`)}
                label={label}
                value={String(values[key])}
                error={error(`${scope}.${key}`)}
                disabled={disabled}
                onChange={(value) => patch({ [key]: value })}
            />
        );
    }

    function range(key: keyof (PublicationsAppearance & TeamAppearance & ContactAppearance), label: string, min: number, max: number): React.JSX.Element {
        return (
            <AppearanceRangeField
                id={id(`${scope}-${key}`)}
                label={label}
                value={Number(values[key])}
                min={min}
                max={max}
                error={error(`${scope}.${key}`)}
                disabled={disabled}
                onChange={(value) => patch({ [key]: value })}
            />
        );
    }

    function select(key: keyof (PublicationsAppearance & TeamAppearance & ContactAppearance), label: string, options = appearanceAlignments): React.JSX.Element {
        return (
            <AppearanceSelectField
                id={id(`${scope}-${key}`)}
                name={`sections[${index}][${scope}][${key}]`}
                label={label}
                value={String(values[key])}
                options={options}
                error={error(`${scope}.${key}`)}
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
                                    id={id(`${scope}-title_divider`)}
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
                    {contact ? (
                        <FormSection title="Canais de contato" variant="inline" iconClass="fa fa-envelope-o">
                            <FormGrid>
                                <FormGridWide>
                                    <p className="websiteBuilder__note">
                                        Endereço, telefone, e-mail e redes sociais são gerenciados no item Identidade.
                                        Somente os canais preenchidos aparecem no site.
                                    </p>
                                </FormGridWide>
                                {([
                                    ["show_icons", "Exibir ícones"], ["show_social", "Exibir redes sociais"],
                                ] as const).map(([key, label]) => (
                                    <FormGridWide key={key}>
                                        <ToggleControl
                                            id={id(`contact-${key}`)}
                                            label={label}
                                            size="small"
                                            checked={section.contact![key]}
                                            disabled={disabled}
                                            onChange={(checked) => patch({ [key]: checked })}
                                        />
                                    </FormGridWide>
                                ))}
                                {range("icon_size", "Tamanho dos ícones (px)", 16, 64)}
                                {range("icon_radius", "Arredondamento dos ícones (px)", 0, 60)}
                                {range("icon_border_width", "Espessura da borda dos ícones (px)", 0, 12)}
                                {color("icon_color", "Cor dos ícones e suas bordas")}
                                {color("icon_background", "Fundo dos ícones")}
                                {select("card_align", "Alinhamento dos canais")}
                                {range("card_title_size", "Tamanho dos títulos dos canais (px)", 16, 32)}
                                {color("card_title_color", "Cor dos títulos dos canais")}
                                {range("card_text_size", "Tamanho das informações (px)", 14, 24)}
                                {color("card_text_color", "Cor das informações")}
                                {color("card_background", "Fundo dos cards")}
                                {color("card_border_color", "Cor das bordas dos cards")}
                                {range("card_radius", "Arredondamento dos cards (px)", 0, 60)}
                                {range("card_padding", "Espaçamento interno dos cards (px)", 8, 48)}
                            </FormGrid>
                        </FormSection>
                    ) : team ? (
                        <FormSection title="Profissionais" variant="inline" iconClass="fa fa-users">
                            <FormGrid>
                                <FormField
                                    id={id("limit")}
                                    label="Quantidade exibida"
                                    error={error("limit")}
                                    hint="Entre 1 e 12 profissionais."
                                >
                                    <TextInput
                                        id={id("limit")}
                                        type="number"
                                        min={1}
                                        max={12}
                                        value={String(section.limit)}
                                        disabled={disabled}
                                        invalid={!!error("limit")}
                                        aria-describedby={[`${id("limit")}-hint`, error("limit") ? `${id("limit")}-error` : null].filter(Boolean).join(" ")}
                                        onChange={(event) => onChange({ limit: Number(event.target.value) || 1 })}
                                    />
                                </FormField>
                                <FormGridWide>
                                    <p className="websiteBuilder__note">
                                        Mostra os profissionais ativos marcados como “Aparece no site”, em ordem alfabética.
                                        Fotos, nomes e cargos são gerenciados no cadastro de profissionais.
                                        Sem profissionais disponíveis, a seção fica oculta no site público e permanece editável na prévia.
                                    </p>
                                </FormGridWide>
                                {([
                                    ["show_photo", "Exibir foto"],
                                    ["show_role", "Exibir cargo"],
                                ] as const).map(([key, label]) => (
                                    <FormGridWide key={key}>
                                        <ToggleControl
                                            id={id(`team-${key}`)}
                                            label={label}
                                            size="small"
                                            checked={section.team![key]}
                                            disabled={disabled}
                                            onChange={(checked) => patch({ [key]: checked })}
                                        />
                                    </FormGridWide>
                                ))}
                                {range("photo_size", "Tamanho da foto (px)", 64, 240)}
                                {range("photo_radius", "Arredondamento da foto (px)", 0, 120)}
                                {range("photo_border_width", "Espessura da borda da foto (px)", 0, 12)}
                                {color("photo_border_color", "Cor da borda da foto")}
                                {select("card_align", "Alinhamento no card")}
                                {range("card_title_size", "Tamanho do nome (px)", 16, 32)}
                                {color("card_title_color", "Cor do nome")}
                                {range("card_text_size", "Tamanho do cargo (px)", 14, 24)}
                                {color("card_text_color", "Cor do cargo")}
                                {color("card_background", "Fundo do card")}
                                {color("card_border_color", "Cor da borda do card")}
                                {range("card_radius", "Arredondamento do card (px)", 0, 60)}
                                {range("card_padding", "Espaçamento interno do card (px)", 8, 48)}
                            </FormGrid>
                        </FormSection>
                    ) : publications ? (
                        <FormSection
                            title={events ? "Eventos" : "Publicações"}
                            variant="inline"
                            iconClass={events ? "fa fa-calendar" : "fa fa-newspaper-o"}
                        >
                            <FormGrid>
                                <FormField
                                    id={id("limit")}
                                    label="Quantidade exibida"
                                    error={error("limit")}
                                    hint={events ? "Entre 1 e 12 eventos." : "Entre 1 e 12 publicações."}
                                >
                                    <TextInput
                                        id={id("limit")}
                                        type="number"
                                        min={1}
                                        max={12}
                                        value={String(section.limit)}
                                        disabled={disabled}
                                        invalid={!!error("limit")}
                                        aria-describedby={[`${id("limit")}-hint`, error("limit") ? `${id("limit")}-error` : null].filter(Boolean).join(" ")}
                                        onChange={(event) => onChange({ limit: Number(event.target.value) || 1 })}
                                    />
                                </FormField>
                                <FormGridWide>
                                    <p className="websiteBuilder__note">
                                        {events
                                            ? "Mostra publicações publicadas com programação que ainda não terminou, em ordem de início. O conteúdo é gerenciado no cadastro de publicações."
                                            : "Mostra as publicações mais recentes, com os destaques primeiro. O conteúdo é gerenciado no cadastro de publicações."}{" "}
                                        Sem registros disponíveis, a seção fica oculta no site público e permanece editável na prévia.
                                    </p>
                                </FormGridWide>
                                {([
                                    ["show_image", "Exibir capa"], ["show_summary", "Exibir resumo"],
                                    ["show_date", "Exibir data"], ["show_tags", "Exibir tipo, marcadores e situação"],
                                ] as const).filter(([key]) => !events || !["show_image", "show_summary"].includes(key)).map(([key, label]) => (
                                    <FormGridWide key={key}>
                                        <ToggleControl
                                            id={id(`${scope}-${key}`)}
                                            label={events && key === "show_tags" ? "Exibir situação" : label}
                                            size="small"
                                            checked={(events ? section.events! : section.publications!)[key]}
                                            disabled={disabled}
                                            onChange={(checked) => patch({ [key]: checked })}
                                        />
                                    </FormGridWide>
                                ))}
                                {range("badge_radius", "Arredondamento das badges (px)", 0, 100)}
                                {!events ? range("image_height", "Altura da capa (px)", 120, 480) : null}
                                {range("card_title_size", "Tamanho do título do card (px)", 16, 32)}
                                {range("card_text_size", events ? "Tamanho das informações (px)" : "Tamanho do resumo (px)", 14, 24)}
                                {color("card_title_color", "Cor do título do card")}
                                {color("card_text_color", events ? "Cor das informações" : "Cor do resumo")}
                                {events ? (
                                    <>
                                        {range("date_badge_width", "Largura do destaque da data (px)", 48, 140)}
                                        {range("date_badge_radius", "Arredondamento do destaque da data (px)", 0, 60)}
                                        {color("date_badge_color", "Cor do texto da data")}
                                        {color("date_badge_background", "Fundo do destaque da data")}
                                    </>
                                ) : null}
                                {color("card_background", "Fundo do card")}
                                {color("card_border_color", "Cor da borda do card")}
                                {range("card_radius", "Arredondamento do card (px)", 0, 60)}
                                {range("card_padding", "Espaçamento interno do card (px)", 8, 48)}
                            </FormGrid>
                        </FormSection>
                    ) : (
                        <FormSection title={section.type === "faq" ? "Perguntas e respostas" : numbered ? "Etapas" : "Itens"} variant="inline" iconClass="fa fa-th-large">
                            {error("items") ? <p className="websiteBuilder__itemsError">{error("items")}</p> : null}
                            <ol className="websiteBuilder__pillarItems">
                                {section.items.map((item, itemIndex) => (
                                    <BuilderPillarItemEditor
                                        key={item.editor_key ?? itemIndex}
                                        numbered={numbered}
                                    faq={section.type === "faq"}
                                    resource={section.type === "resources"}
                                    linkOptions={linkOptions}
                                        timeline={numbered && appearance.layout === "timeline"}
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
                                label={section.type === "faq" ? "Pergunta" : numbered ? "Etapa" : "Item"}
                                title={section.type === "faq" ? "Adicionar pergunta" : numbered ? "Adicionar etapa" : "Adicionar item"}
                                aria-label={section.type === "faq" ? "Adicionar pergunta" : numbered ? "Adicionar etapa" : "Adicionar item"}
                                disabled={disabled || section.items.length >= 12}
                                onClick={() => {
                                    onChange({
                                        items: [...section.items, {
                                            ...emptyItem(section.type),
                                            editor_key: `item-${Date.now().toString(36)}`,
                                            appearance: { ...template },
                                        }],
                                    });
                                    onEditTarget({ sectionId: section.id, field: `item-${section.items.length}-${numbered || section.type === "faq" ? "title" : "icon"}` });
                                }}
                            />
                        </FormSection>
                    )}
                    {numbered || publications || team ? (
                        <FormSection title="Ação" variant="inline" iconClass="fa fa-link">
                            <FormGrid>
                                <FormField id={id("button_label")} label="Texto da ação" error={error("button_label")}>
                                    <TextInput
                                        id={id("button_label")}
                                        value={section.button_label}
                                        maxLength={60}
                                        disabled={disabled}
                                        invalid={!!error("button_label")}
                                        aria-describedby={error("button_label") ? `${id("button_label")}-error` : undefined}
                                        onChange={(event) => onChange({ button_label: event.target.value })}
                                    />
                                </FormField>
                                <BuilderLinkField
                                    id={id("button_url")}
                                    name={`sections[${index}][button_url]`}
                                    value={section.button_url}
                                    options={linkOptions}
                                    error={error("button_url")}
                                    disabled={disabled}
                                    onChange={(button_url) => onChange({ button_url })}
                                />
                                {select("action_style", "Exibir como", [{ value: "link", label: "Link" }, { value: "button", label: "Botão" }])}
                                {select("button_align", "Posição da ação")}
                                {range("button_size", "Tamanho do texto (px)", 14, 24)}
                                {color("button_color", "Cor do texto")}
                                {color("button_background", "Cor de fundo")}
                                {range("button_radius", "Arredondamento (px)", 0, 60)}
                                {range("button_shadow", "Sombra", 0, 40)}
                                <FormGridWide>
                                    <ToggleControl
                                        id={id(`${scope}-button_icon`)}
                                        label="Exibir seta"
                                        size="small"
                                        checked={values.button_icon ?? true}
                                        disabled={disabled}
                                        onChange={(checked) => patch({ button_icon: checked })}
                                    />
                                </FormGridWide>
                                <FormGridWide>
                                    <ToggleControl
                                        id={id(`${scope}-hover_enabled`)}
                                        label="Efeito ao passar o ponteiro"
                                        size="small"
                                        checked={values.hover_enabled ?? true}
                                        disabled={disabled}
                                        onChange={(checked) => patch({ hover_enabled: checked })}
                                    />
                                </FormGridWide>
                                {values.hover_enabled ? (
                                    <>
                                        {color("hover_color", "Cor do texto ao passar o ponteiro")}
                                        {color("hover_background", "Cor de fundo ao passar o ponteiro")}
                                        {range("hover_lift", "Elevação ao passar o ponteiro (px)", 0, 8)}
                                        {range("hover_shadow", "Sombra ao passar o ponteiro", 0, 40)}
                                    </>
                                ) : null}
                            </FormGrid>
                        </FormSection>
                    ) : null}
                    <FormSection title="Organização e fundo" variant="inline" iconClass="fa fa-adjust">
                        <FormGrid>
                            {select("layout", numbered ? "Exibição das etapas" : "Organização dos itens", [
                                { value: "grid", label: numbered ? "Grade de etapas" : "Grade de cards" },
                                { value: "list", label: "Lista" },
                                ...(numbered ? [{ value: "timeline", label: "Linha do tempo" }] : []),
                                ...(numbered || publications || team ? [{ value: "carousel", label: "Carrossel" }] : []),
                            ])}
                            {appearance.layout === "grid" ? range("columns", "Colunas", 1, 4) : null}
                            {appearance.layout !== "carousel" ? range("gap", numbered ? "Espaço entre etapas (px)" : "Espaço entre cards (px)", 8, numbered || publications || team || contact ? 64 : 48) : null}
                            {numbered && appearance.layout === "timeline" ? color("timeline_color", "Cor da linha do tempo") : null}
                            {(numbered || publications || team) && appearance.layout === "carousel" ? (
                                <>
                                    <FormGridWide>
                                        <ToggleControl
                                            id={id(`${scope}-autoplay`)}
                                            label="Avançar automaticamente"
                                            size="small"
                                            checked={values.autoplay ?? false}
                                            disabled={disabled}
                                            onChange={(checked) => patch({ autoplay: checked })}
                                        />
                                    </FormGridWide>
                                    {values.autoplay ? range("interval", "Intervalo entre itens (segundos)", 3, 20) : null}
                                </>
                            ) : null}
                            {range("padding_vertical", "Espaçamento vertical (px)", 24, 120)}
                            {color("background_color", "Cor de fundo da seção")}
                            <FormGridWide>
                                <ToggleControl
                                    id={id(`${scope}-gradient_enabled`)}
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
