import ClockPicker from "@/components/Forms/ClockPicker/ClockPicker";
import DatePicker from "@/components/Forms/DatePicker/DatePicker";
import FormField from "@/components/Forms/FormField/FormField";
import FormGrid, { FormGridWide } from "@/components/Forms/FormGrid/FormGrid";
import FormSection from "@/components/Forms/FormSection/FormSection";
import RemovableField from "@/components/Forms/RemovableField/RemovableField";
import SelectField from "@/components/Forms/SelectField/SelectField";
import SummernoteEditor from "@/components/Forms/SummernoteEditor/SummernoteEditor";
import TextArea from "@/components/Forms/TextArea/TextArea";
import TextInput from "@/components/Forms/TextInput/TextInput";
import ToggleControl from "@/components/Forms/ToggleControl/ToggleControl";
import Button from "@/components/UI/Button/Button";
import InfoTooltip from "@/components/UI/InfoTooltip/InfoTooltip";
import type { PublicationFormOptions, SpeakerRow } from "./Publication.types";
import { describedBy, type PublicationFieldsProps } from "./PublicationFields.types";

/** Valor da opção que representa o participante avulso digitado na busca. */
const guestOption = "__guest";

export function newSpeakerRow(values: Partial<Omit<SpeakerRow, "key">> = {}): SpeakerRow {
    return {
        key: `${Date.now()}-${Math.random().toString(36).slice(2)}`,
        id: "",
        name: "",
        guest: false,
        participation: "",
        ...values,
    };
}

/** Texto e classificação da publicação. */
export function PublicationTextFields({
    data,
    errors,
    disabled,
    update,
    options,
    onEditorError,
}: PublicationFieldsProps & {
    options: PublicationFormOptions;
    onEditorError: () => void;
}): React.JSX.Element {
    const tagError = errors.tag_ids
        ?? Object.entries(errors).find(([key]) => key.startsWith("tag_ids."))?.[1];

    return (
        <FormSection id="publication-content" title="Conteúdo" variant="inline" iconClass="fa fa-file-text-o">
            <div className="publicationForm__intro">
                <div className="publicationForm__introMain">
                    <FormField id="publication-title" label="Título" error={errors.title} requiredIndicator>
                        <TextInput
                            id="publication-title"
                            name="title"
                            value={data.title}
                            maxLength={255}
                            placeholder="Escreva um título claro e direto"
                            required
                            invalid={!!errors.title}
                            aria-describedby={describedBy(errors, "title", "publication-title")}
                            onChange={(event) => update("title", event.target.value)}
                        />
                    </FormField>
                    <FormField id="publication-summary" label="Resumo" error={errors.summary}>
                        <TextArea
                            id="publication-summary"
                            name="summary"
                            rows={4}
                            maxLength={500}
                            value={data.summary}
                            placeholder="Resumo curto que acompanha o título"
                            aria-invalid={!!errors.summary}
                            aria-describedby={describedBy(errors, "summary", "publication-summary")}
                            onChange={(event) => update("summary", event.target.value)}
                        />
                    </FormField>
                </div>
                <div className="publicationForm__introAside">
                    <FormField id="publication-author" label="Autor" error={errors.author_id}>
                        <SelectField
                            id="publication-author"
                            name="author_id"
                            label="Autor"
                            placeholder="Sem autor"
                            value={data.author_id}
                            options={options.authors}
                            aria-invalid={!!errors.author_id}
                            aria-describedby={describedBy(errors, "author_id", "publication-author")}
                            onChange={(event) => update("author_id", event.target.value)}
                        />
                    </FormField>
                    <FormField id="publication-type" label="Tipo" error={errors.type} requiredIndicator>
                        <SelectField
                            id="publication-type"
                            name="type"
                            label="Tipo"
                            required
                            value={data.type}
                            options={options.types}
                            aria-invalid={!!errors.type}
                            aria-describedby={describedBy(errors, "type", "publication-type")}
                            onChange={(event) => update("type", event.target.value)}
                        />
                    </FormField>
                    <FormField id="publication-tags" label="Etiquetas" error={tagError}>
                        <SelectField
                            id="publication-tags"
                            name="tag_ids[]"
                            label="Etiquetas"
                            placeholder="Selecione as etiquetas"
                            multiple
                            value={data.tag_ids}
                            options={options.tags}
                            aria-invalid={!!tagError}
                            aria-describedby={tagError ? "publication-tags-error" : undefined}
                            onChange={(event) => update(
                                "tag_ids",
                                Array.from(event.target.selectedOptions, (option) => option.value)
                                    .filter((value) => value !== ""),
                            )}
                        />
                    </FormField>
                </div>
            </div>
            <FormField id="publication-description-input" label="Texto da publicação" error={errors.description}>
                <SummernoteEditor
                    id="publication-description"
                    label="Texto da publicação"
                    defaultValue={data.description}
                    height={260}
                    disabled={disabled}
                    invalid={!!errors.description}
                    describedBy={errors.description ? "publication-description-input-error" : undefined}
                    onLoadError={onEditorError}
                    onChange={(html) => update("description", html)}
                />
            </FormField>
        </FormSection>
    );
}

/** Período do evento, local, organização e público. */
export function PublicationScheduleFields({
    data,
    errors,
    disabled,
    update,
}: PublicationFieldsProps): React.JSX.Element {
    return (
        <FormSection id="publication-schedule" title="Datas e local" variant="inline" iconClass="fa fa-calendar">
            <FormGrid columns={4}>
                <FormGridWide>
                    <ToggleControl
                        id="publication-has-schedule"
                        name="has_schedule"
                        size="small"
                        label="Tem data de início e término"
                        labelAddon={(
                            <InfoTooltip text="Marque para eventos: o site mostra se está em breve, acontecendo ou encerrado." />
                        )}
                        checked={data.has_schedule}
                        disabled={disabled}
                        onChange={(checked) => update("has_schedule", checked)}
                    />
                </FormGridWide>
                {data.has_schedule ? (
                    <>
                        <FormField id="publication-start-date" label="Data de início" error={errors.start_date} requiredIndicator>
                            <DatePicker
                                id="publication-start-date"
                                name="start_date"
                                label="Data de início"
                                value={data.start_date}
                                required
                                disabled={disabled}
                                invalid={!!errors.start_date}
                                describedBy={describedBy(errors, "start_date", "publication-start-date")}
                                onChange={(value) => update("start_date", value)}
                            />
                        </FormField>
                        <FormField id="publication-start-time" label="Horário de início" error={errors.start_time} requiredIndicator>
                            <ClockPicker
                                id="publication-start-time"
                                label="Horário de início"
                                defaultValue={data.start_time}
                                required
                                invalid={!!errors.start_time}
                                describedBy={describedBy(errors, "start_time", "publication-start-time")}
                                onChange={(value) => update("start_time", value)}
                            />
                        </FormField>
                        <FormField id="publication-end-date" label="Data de término" error={errors.end_date} requiredIndicator>
                            <DatePicker
                                id="publication-end-date"
                                name="end_date"
                                label="Data de término"
                                value={data.end_date}
                                required
                                disabled={disabled}
                                invalid={!!errors.end_date}
                                describedBy={describedBy(errors, "end_date", "publication-end-date")}
                                onChange={(value) => update("end_date", value)}
                            />
                        </FormField>
                        <FormField id="publication-end-time" label="Horário de término" error={errors.end_time} requiredIndicator>
                            <ClockPicker
                                id="publication-end-time"
                                label="Horário de término"
                                defaultValue={data.end_time}
                                required
                                invalid={!!errors.end_time}
                                describedBy={describedBy(errors, "end_time", "publication-end-time")}
                                onChange={(value) => update("end_time", value)}
                            />
                        </FormField>
                    </>
                ) : null}
            </FormGrid>
            <FormGrid columns={3} className="publicationForm__placeRow">
                <FormField
                    id="publication-location"
                    label="Local"
                    error={errors.location}
                    requiredIndicator={data.has_schedule}
                >
                    <TextInput
                        id="publication-location"
                        name="location"
                        value={data.location}
                        maxLength={255}
                        placeholder="Ex.: Auditório Central"
                        required={data.has_schedule}
                        invalid={!!errors.location}
                        aria-describedby={describedBy(errors, "location", "publication-location")}
                        onChange={(event) => update("location", event.target.value)}
                    />
                </FormField>
                <FormField id="publication-organizer" label="Organizador" error={errors.organizer}>
                    <TextInput
                        id="publication-organizer"
                        name="organizer"
                        value={data.organizer}
                        maxLength={255}
                        placeholder="Ex.: Coordenação do NAPNE"
                        invalid={!!errors.organizer}
                        aria-describedby={describedBy(errors, "organizer", "publication-organizer")}
                        onChange={(event) => update("organizer", event.target.value)}
                    />
                </FormField>
                <FormField id="publication-audience" label="Público" error={errors.audience}>
                    <TextInput
                        id="publication-audience"
                        name="audience"
                        value={data.audience}
                        maxLength={255}
                        placeholder="Ex.: Estudantes, professores e comunidade"
                        invalid={!!errors.audience}
                        aria-describedby={describedBy(errors, "audience", "publication-audience")}
                        onChange={(event) => update("audience", event.target.value)}
                    />
                </FormField>
            </FormGrid>
        </FormSection>
    );
}

/** Participantes em linhas dinâmicas e link complementar. */
export function PublicationPeopleFields({
    data,
    errors,
    disabled,
    update,
    updateWith,
    options,
}: PublicationFieldsProps & {
    options: PublicationFormOptions;
}): React.JSX.Element {
    const speakersError = errors.speakers;

    function updateRow(key: string, changes: Partial<SpeakerRow>): void {
        updateWith((current) => ({
            ...current,
            speaker_rows: current.speaker_rows.map((item) => (item.key === key ? { ...item, ...changes } : item)),
        }));
    }

    return (
        <FormSection id="publication-people" title="Participantes e link" variant="inline" iconClass="fa fa-microphone">
            <FormGrid>
                <FormGridWide>
                    <div
                        className="publicationForm__speakers"
                        role="group"
                        aria-labelledby="publication-speakers-label"
                        aria-describedby={speakersError ? "publication-speakers-error" : undefined}
                    >
                        <span id="publication-speakers-label" className="formField__label">Participantes</span>
                        {data.speaker_rows.length ? (
                            <ul className="publicationForm__speakerList">
                                {data.speaker_rows.map((row, index) => {
                                    const id = `publication-speaker-${row.key}`;
                                    const idError = errors[`speakers.${index}.id`];
                                    const nameError = errors[`speakers.${index}.name`];
                                    const participationError = errors[`speakers.${index}.participation`];
                                    const takenElsewhere = new Set(
                                        data.speaker_rows
                                            .filter((other) => other.key !== row.key && !other.guest && other.id)
                                            .map((other) => other.id),
                                    );

                                    return (
                                        <li key={row.key} className="publicationForm__speakerRow">
                                            <FormField id={id} label={`Participante ${index + 1}`} error={idError ?? nameError} requiredIndicator>
                                                <SelectField
                                                    id={id}
                                                    name={`speakers[${index}][id]`}
                                                    label={`Participante ${index + 1}`}
                                                    placeholder="Selecione ou digite o nome"
                                                    value={row.guest ? guestOption : row.id}
                                                    options={[
                                                        ...(row.guest ? [{ value: guestOption, label: `${row.name} (avulso)` }] : []),
                                                        ...options.speakers.filter((option) => !takenElsewhere.has(option.value)),
                                                    ]}
                                                    aria-invalid={!!(idError ?? nameError)}
                                                    aria-describedby={idError ?? nameError ? `${id}-error` : undefined}
                                                    onCreateOption={(text) => updateRow(row.key, { guest: true, id: "", name: text })}
                                                    onChange={(event) => {
                                                        if (event.target.value === guestOption) return;
                                                        updateRow(row.key, { guest: false, id: event.target.value, name: "" });
                                                    }}
                                                />
                                            </FormField>
                                            <FormField id={`${id}-participation`} label="Atuação" error={participationError}>
                                                <RemovableField
                                                    removeLabel={`Remover participante ${index + 1}`}
                                                    disabled={disabled}
                                                    onRemove={() => update(
                                                        "speaker_rows",
                                                        data.speaker_rows.filter((item) => item.key !== row.key),
                                                    )}
                                                >
                                                    <TextInput
                                                        id={`${id}-participation`}
                                                        value={row.participation}
                                                        maxLength={100}
                                                        placeholder="Ex.: Palestrante, mediador"
                                                        invalid={!!participationError}
                                                        aria-describedby={participationError ? `${id}-participation-error` : undefined}
                                                        onChange={(event) => updateRow(row.key, { participation: event.target.value })}
                                                    />
                                                </RemovableField>
                                            </FormField>
                                        </li>
                                    );
                                })}
                            </ul>
                        ) : null}
                        <Button
                            type="button"
                            tone="white"
                            className="removableField__add"
                            labelIconClass="fa fa-plus"
                            label="Participante"
                            title="Adicionar participante"
                            aria-label="Adicionar participante"
                            disabled={disabled}
                            onClick={() => update("speaker_rows", [...data.speaker_rows, newSpeakerRow()])}
                        />
                        {speakersError ? (
                            <span id="publication-speakers-error" className="formField__error">{speakersError}</span>
                        ) : null}
                    </div>
                </FormGridWide>
                <FormField
                    id="publication-link-url"
                    label="Link"
                    error={errors.link_url}
                    hint="Endereço para inscrição, transmissão ou material complementar da publicação."
                    hintLabel="Sobre o link"
                >
                    <TextInput
                        id="publication-link-url"
                        name="link_url"
                        type="url"
                        value={data.link_url}
                        maxLength={500}
                        placeholder="https://"
                        invalid={!!errors.link_url}
                        aria-describedby={describedBy(errors, "link_url", "publication-link-url")}
                        onChange={(event) => update("link_url", event.target.value)}
                    />
                </FormField>
                <FormField id="publication-link-label" label="Texto do link" error={errors.link_label}>
                    <TextInput
                        id="publication-link-label"
                        name="link_label"
                        value={data.link_label}
                        maxLength={80}
                        placeholder="Ex.: Assistir à transmissão"
                        invalid={!!errors.link_label}
                        aria-describedby={describedBy(errors, "link_label", "publication-link-label")}
                        onChange={(event) => update("link_label", event.target.value)}
                    />
                </FormField>
            </FormGrid>
        </FormSection>
    );
}
