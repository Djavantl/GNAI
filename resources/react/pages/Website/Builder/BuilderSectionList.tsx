import { useState } from "react";
import SelectField from "@/components/Forms/SelectField/SelectField";
import Button from "@/components/UI/Button/Button";
import type { WebsiteSectionType } from "@/components/Website/Website.types";
import { joinClassNames } from "@/utils/joinClassNames";
import { IDENTITY_ID, sectionTypeIcons, type BuilderSection } from "./websiteBuilder";

type Props = {
    sections: BuilderSection[];
    /** Nome do site exibido no item fixo da identidade (topo e rodapé). */
    siteName: string;
    identityInvalid: boolean;
    selectedId: string | null;
    sectionTypes: { value: string; label: string }[];
    invalidIds: Set<string>;
    disabled: boolean;
    onSelect: (id: string) => void;
    onMove: (index: number, offset: -1 | 1) => void;
    onToggle: (index: number) => void;
    onRemove: (index: number) => void;
    onAdd: (type: WebsiteSectionType) => void;
};

/**
 * Itens editáveis da página inicial: a identidade (topo e rodapé), sempre
 * primeira e fixa, seguida das seções com ordem, visibilidade e inclusão.
 * Selecionar um item abre o formulário dele na aba de edição.
 */
export default function BuilderSectionList({
    sections,
    siteName,
    identityInvalid,
    selectedId,
    sectionTypes,
    invalidIds,
    disabled,
    onSelect,
    onMove,
    onToggle,
    onRemove,
    onAdd,
}: Props): React.JSX.Element {
    const [newType, setNewType] = useState("");
    const labels = Object.fromEntries(sectionTypes.map((type) => [type.value, type.label]));
    const identitySelected = selectedId === IDENTITY_ID;

    return (
        <div className="websiteBuilder__structure">
            <ol className="websiteBuilder__sectionList" aria-label="Itens da página inicial">
                <li
                    className={joinClassNames(
                        "websiteBuilder__sectionRow",
                        identitySelected && "websiteBuilder__sectionRow--selected",
                    )}
                >
                    <button
                        type="button"
                        className="websiteBuilder__sectionSelect"
                        aria-pressed={identitySelected}
                        title="Editar identidade: topo e rodapé do site"
                        disabled={disabled}
                        onClick={() => onSelect(IDENTITY_ID)}
                    >
                        <i className="fa fa-flag" aria-hidden="true" />
                        <span className="websiteBuilder__sectionText">
                            <span className="websiteBuilder__sectionType">Identidade</span>
                            <span className="websiteBuilder__sectionTitle">{siteName || "Nome do site"}</span>
                        </span>
                        <span className="websiteBuilder__badge">Topo e rodapé</span>
                        {identityInvalid ? (
                            <span className="websiteBuilder__badge websiteBuilder__badge--error">
                                <i className="fa fa-exclamation-circle" aria-hidden="true" /> Revisar
                            </span>
                        ) : null}
                    </button>
                </li>
                {sections.map((section, index) => {
                    const label = labels[section.type] ?? section.type;
                    const name = section.title ? `${label}: ${section.title}` : label;
                    const selected = section.id === selectedId;

                    return (
                        <li
                            key={section.id}
                            className={joinClassNames(
                                "websiteBuilder__sectionRow",
                                selected && "websiteBuilder__sectionRow--selected",
                                !section.enabled && "websiteBuilder__sectionRow--hidden",
                            )}
                        >
                            <button
                                type="button"
                                className="websiteBuilder__sectionSelect"
                                aria-pressed={selected}
                                title={`Editar ${name}`}
                                disabled={disabled}
                                onClick={() => onSelect(section.id)}
                            >
                                <i className={sectionTypeIcons[section.type]} aria-hidden="true" />
                                <span className="websiteBuilder__sectionText">
                                    <span className="websiteBuilder__sectionType">{label}</span>
                                    <span className="websiteBuilder__sectionTitle">{section.title || "Sem título"}</span>
                                </span>
                                {!section.enabled ? <span className="websiteBuilder__badge">Oculta</span> : null}
                                {invalidIds.has(section.id) ? (
                                    <span className="websiteBuilder__badge websiteBuilder__badge--error">
                                        <i className="fa fa-exclamation-circle" aria-hidden="true" /> Revisar
                                    </span>
                                ) : null}
                            </button>
                            <div className="websiteBuilder__sectionActions">
                                <Button
                                    size="xs"
                                    tone="white"
                                    iconOnly
                                    leftIconClass="fa fa-arrow-up"
                                    aria-label={`Mover ${name} para cima`}
                                    disabled={disabled || index === 0}
                                    onClick={() => onMove(index, -1)}
                                />
                                <Button
                                    size="xs"
                                    tone="white"
                                    iconOnly
                                    leftIconClass="fa fa-arrow-down"
                                    aria-label={`Mover ${name} para baixo`}
                                    disabled={disabled || index === sections.length - 1}
                                    onClick={() => onMove(index, 1)}
                                />
                                <Button
                                    size="xs"
                                    tone="white"
                                    iconOnly
                                    leftIconClass={section.enabled ? "fa fa-eye" : "fa fa-eye-slash"}
                                    aria-label={section.enabled ? `Ocultar ${name} do site` : `Exibir ${name} no site`}
                                    disabled={disabled}
                                    onClick={() => onToggle(index)}
                                />
                                <Button
                                    size="xs"
                                    tone="dark-red"
                                    iconOnly
                                    leftIconClass="fa fa-trash-o"
                                    aria-label={`Remover ${name}`}
                                    disabled={disabled}
                                    onClick={() => onRemove(index)}
                                />
                            </div>
                        </li>
                    );
                })}
            </ol>
            {!sections.length ? (
                <p className="websiteBuilder__note">A página inicial ainda não tem seções. Adicione a primeira abaixo.</p>
            ) : null}
            <div className="websiteBuilder__add">
                <div className="websiteBuilder__addSelect">
                    <label htmlFor="website-new-section" className="websiteBuilder__addLabel">Nova seção</label>
                    <SelectField
                        id="website-new-section"
                        name="new_section"
                        label="Nova seção"
                        value={newType}
                        placeholder="Selecione o tipo"
                        options={sectionTypes}
                        disabled={disabled}
                        onChange={(event) => setNewType(event.target.value)}
                    />
                </div>
                <Button
                    tone="list-action"
                    leftIconClass="fa fa-plus"
                    label="Adicionar"
                    title="Adicionar a seção selecionada ao final da página"
                    disabled={disabled || !newType || sections.length >= 20}
                    onClick={() => {
                        onAdd(newType as WebsiteSectionType);
                        setNewType("");
                    }}
                />
            </div>
        </div>
    );
}
