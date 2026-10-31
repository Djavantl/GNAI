import ClockPicker from "@/components/Forms/ClockPicker/ClockPicker";
import DatePicker from "@/components/Forms/DatePicker/DatePicker";
import FormField from "@/components/Forms/FormField/FormField";
import FormGrid, { FormGridWide } from "@/components/Forms/FormGrid/FormGrid";
import FormSection from "@/components/Forms/FormSection/FormSection";
import SelectField from "@/components/Forms/SelectField/SelectField";
import SummernoteEditor from "@/components/Forms/SummernoteEditor/SummernoteEditor";
import TextArea from "@/components/Forms/TextArea/TextArea";
import TextInput from "@/components/Forms/TextInput/TextInput";
import ToggleControl from "@/components/Forms/ToggleControl/ToggleControl";
import InfoTooltip from "@/components/UI/InfoTooltip/InfoTooltip";
import type { PublicationFormOptions } from "./InstitutionalEvent.types";
import { describedBy, type PublicationFieldsProps } from "./PublicationFields.types";

/** Aba Conteúdo: texto, classificação, datas do evento, pessoas e link. */
export default function PublicationContentFields({
    data,
    errors,
    disabled,
    update,
    updateWith,
    options,
    onEditorError,
}: PublicationFieldsProps & {
    options: PublicationFormOptions;
    onEditorError: () => void;
}): React.JSX.Element {
    const speakerError = errors.speaker_ids
        ?? Object.entries(errors).find(([key]) => key.startsWith("speaker_ids."))?.[1];

    return (
        <>
            <FormSection id="publication-content" title="Conteúdo" variant="inline" iconClass="fa fa-file-text-o">
                <FormGrid>
                    <FormGridWide>
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
                    </FormGridWide>
                    <FormGridWide>
                        <FormField
                            id="publication-summary"
                            label="Resumo"
                            error={errors.summary}
                            hint={`${data.summary.length}/500 caracteres. Aparece nos cartões do site e nos compartilhamentos.`}
                        >
                            <TextArea
                                id="publication-summary"
                                name="summary"
                                rows={3}
                                maxLength={500}
                                value={data.summary}
                                placeholder="Resumo curto que acompanha o título"
                                aria-invalid={!!errors.summary}
                                aria-describedby={describedBy(errors, "summary", "publication-summary", true)}
                                onChange={(event) => update("summary", event.target.value)}
                            />
                        </FormField>
                    </FormGridWide>
                    <FormField
                        id="publication-author"
                        label="Autor"
                        error={errors.author_id}
                        hint="Por padrão, quem está cadastrando a publicação."
                    >
                        <SelectField
                            id="publication-author"
                            name="author_id"
                            label="Autor"
                            placeholder="Sem autor"
                            value={data.author_id}
                            options={options.authors}
                            aria-invalid={!!errors.author_id}
                            aria-describedby={describedBy(errors, "author_id", "publication-author", true)}
                            onChange={(event) => update("author_id", event.target.value)}
                        />
                    </FormField>
                    <FormField
                        id="publication-type"
                        label="Tipo"
                        error={errors.type}
                        requiredIndicator
                    >
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
                    <FormGridWide>
                        <FormField
                            id="publication-category"
                            label="Categoria"
                            error={errors.category}
                            hint="Opcional. Digite uma nova categoria ou escolha uma das já usadas."
                        >
                            <TextInput
                                id="publication-category"
                                name="category"
                                list="publication-category-options"
                                value={data.category}
                                maxLength={100}
                                placeholder="Ex.: Acessibilidade, Formação"
                                invalid={!!errors.category}
                                aria-describedby={describedBy(errors, "category", "publication-category", true)}
                                onChange={(event) => update("category", event.target.value)}
                            />
                            <datalist id="publication-category-options">
                                {options.categories.map((category) => (
                                    <option key={category} value={category} />
                                ))}
                            </datalist>
                        </FormField>
                    </FormGridWide>
                    <FormGridWide>
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
                    </FormGridWide>
                </FormGrid>
            </FormSection>
            <FormSection id="publication-schedule" title="Datas e local" variant="inline" iconClass="fa fa-calendar">
                <FormGrid>
                    <FormGridWide>
                        <ToggleControl
                            id="publication-has-schedule"
                            name="has_schedule"
                            size="small"
                            label="Tem data de início e término"
                            labelAddon={(
                                <InfoTooltip text="Marque para eventos: o site mostra se está em breve, acontecendo ou encerrado, e os usuários recebem os lembretes da agenda." />
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
                    <FormGridWide>
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
                    </FormGridWide>
                </FormGrid>
            </FormSection>
            <FormSection id="publication-people" title="Palestrantes e link" variant="inline" iconClass="fa fa-microphone">
                <FormGrid>
                    <FormGridWide>
                        <FormField
                            id="publication-speakers"
                            label="Palestrantes"
                            error={speakerError}
                            hint="Opcional. Profissionais da equipe que participam da publicação; aparecem com foto no site."
                        >
                            <SelectField
                                id="publication-speakers"
                                name="speaker_ids[]"
                                label="Palestrantes"
                                placeholder="Selecione os palestrantes"
                                multiple
                                value={data.speaker_ids}
                                options={options.speakers}
                                aria-invalid={!!speakerError}
                                aria-describedby={[
                                    "publication-speakers-hint",
                                    speakerError ? "publication-speakers-error" : null,
                                ].filter(Boolean).join(" ")}
                                onChange={(event) => updateWith((current) => ({
                                    ...current,
                                    speaker_ids: Array.from(event.target.selectedOptions, (option) => option.value)
                                        .filter((value) => value !== ""),
                                }))}
                            />
                        </FormField>
                    </FormGridWide>
                    <FormField
                        id="publication-link-url"
                        label="Link"
                        error={errors.link_url}
                        hint="Opcional. Inscrição, transmissão ou material complementar."
                    >
                        <TextInput
                            id="publication-link-url"
                            name="link_url"
                            type="url"
                            value={data.link_url}
                            maxLength={500}
                            placeholder="https://"
                            invalid={!!errors.link_url}
                            aria-describedby={describedBy(errors, "link_url", "publication-link-url", true)}
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
        </>
    );
}
