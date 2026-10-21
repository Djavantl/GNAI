import { useForm } from "@inertiajs/react";
import CardBox from "@/components/UI/CardBox/CardBox";
import Button from "@/components/UI/Button/Button";
import FormActions from "@/components/Forms/FormActions/FormActions";
import FormField from "@/components/Forms/FormField/FormField";
import FormGrid from "@/components/Forms/FormGrid/FormGrid";
import FormLayout from "@/components/Forms/FormLayout/FormLayout";
import FormSection from "@/components/Forms/FormSection/FormSection";
import DatePicker from "@/components/Forms/DatePicker/DatePicker";
import ClockPicker from "@/components/Forms/ClockPicker/ClockPicker";
import TextArea from "@/components/Forms/TextArea/TextArea";
import TextInput from "@/components/Forms/TextInput/TextInput";
import ToggleControl from "@/components/Forms/ToggleControl/ToggleControl";
import InfoTooltip from "@/components/UI/InfoTooltip/InfoTooltip";
import type {
    InstitutionalEventFormData,
    InstitutionalEventRecord,
} from "./InstitutionalEvent.types";
import InstitutionalEventPage from "./InstitutionalEventPage";
import "./InstitutionalEvents.css";

const base = "/inclusive-radar/institutional-events";

export default function InstitutionalEventForm({
    event,
    backRoute = base,
    canSubmit,
}: {
    event?: InstitutionalEventRecord;
    backRoute?: string;
    canSubmit: boolean;
}): React.JSX.Element {
    const editing = Boolean(event);
    const form = useForm<InstitutionalEventFormData>({
        title: event?.title ?? "",
        description: event?.description ?? "",
        start_date: event?.start_date ?? "",
        end_date: event?.end_date ?? "",
        start_time: event?.start_time ?? "",
        end_time: event?.end_time ?? "",
        location: event?.location ?? "",
        organizer: event?.organizer ?? "",
        audience: event?.audience ?? "",
        is_active: event?.is_active ?? true,
    });
    const errors = form.errors as Record<string, string | undefined>;

    function submit(eventSubmit: React.FormEvent<HTMLFormElement>): void {
        eventSubmit.preventDefault();
        if (!canSubmit || form.processing) return;
        if (editing && event) {
            form.put(`${base}/${event.id}`);
        } else {
            form.post(`${base}/store`);
        }
    }

    const fieldError = (name: keyof InstitutionalEventFormData): string | undefined =>
        errors[name];

    return (
        <InstitutionalEventPage
            title={editing ? "Editar Registro da Agenda" : "Novo Registro na Agenda"}
            subtitle={
                editing
                    ? "Atualize as informações do evento ou compromisso na agenda institucional."
                    : "Cadastre eventos ou compromissos para a agenda institucional."
            }
            current={editing ? "Editar" : "Cadastrar"}
            backRoute={backRoute}
        >
            <FormLayout onSubmit={submit} className="institutionalEventForm">
                <CardBox className="institutionalEventForm__card">
                    <FormSection
                        id="institutional-event-main"
                        title="Informações Principais"
                        variant="inline"
                        iconClass="fa fa-calendar"
                    >
                        <FormGrid columns={2}>
                            <FormField
                                id="institutional-event-title"
                                label="Título"
                                error={fieldError("title")}
                                requiredIndicator
                                className="institutionalEventForm__wide"
                            >
                                <TextInput
                                    id="institutional-event-title"
                                    name="title"
                                    value={form.data.title}
                                    maxLength={255}
                                    placeholder="Ex.: Reunião de Coordenação"
                                    required
                                    invalid={!!errors.title}
                                    aria-describedby={errors.title ? "institutional-event-title-error" : undefined}
                                    onChange={(inputEvent) => form.setData("title", inputEvent.target.value)}
                                />
                            </FormField>
                            <FormField
                                id="institutional-event-description"
                                label="Descrição"
                                error={fieldError("description")}
                                className="institutionalEventForm__wide"
                            >
                                <TextArea
                                    id="institutional-event-description"
                                    name="description"
                                    rows={3}
                                    value={form.data.description}
                                    placeholder="Detalhes sobre o evento"
                                    aria-invalid={!!errors.description}
                                    aria-describedby={errors.description ? "institutional-event-description-error" : undefined}
                                    onChange={(inputEvent) => form.setData("description", inputEvent.target.value)}
                                />
                            </FormField>
                            <FormField
                                id="institutional-event-start-date"
                                label="Data de Início"
                                error={fieldError("start_date")}
                                requiredIndicator
                            >
                                <DatePicker
                                    id="institutional-event-start-date"
                                    name="start_date"
                                    label="Data de Início"
                                    value={form.data.start_date}
                                    required
                                    invalid={!!errors.start_date}
                                    describedBy={errors.start_date ? "institutional-event-start-date-error" : undefined}
                                    onChange={(value) => form.setData("start_date", value)}
                                />
                            </FormField>
                            <FormField
                                id="institutional-event-start-time"
                                label="Horário de Início"
                                error={fieldError("start_time")}
                                requiredIndicator
                            >
                                <ClockPicker
                                    id="institutional-event-start-time"
                                    label="Horário de Início"
                                    defaultValue={form.data.start_time}
                                    required
                                    invalid={!!errors.start_time}
                                    describedBy={errors.start_time ? "institutional-event-start-time-error" : undefined}
                                    onChange={(value) => form.setData("start_time", value)}
                                />
                            </FormField>
                            <FormField
                                id="institutional-event-end-date"
                                label="Data de Término"
                                error={fieldError("end_date")}
                                requiredIndicator
                            >
                                <DatePicker
                                    id="institutional-event-end-date"
                                    name="end_date"
                                    label="Data de Término"
                                    value={form.data.end_date}
                                    required
                                    invalid={!!errors.end_date}
                                    describedBy={errors.end_date ? "institutional-event-end-date-error" : undefined}
                                    onChange={(value) => form.setData("end_date", value)}
                                />
                            </FormField>
                            <FormField
                                id="institutional-event-end-time"
                                label="Horário de Término"
                                error={fieldError("end_time")}
                                requiredIndicator
                            >
                                <ClockPicker
                                    id="institutional-event-end-time"
                                    label="Horário de Término"
                                    defaultValue={form.data.end_time}
                                    required
                                    invalid={!!errors.end_time}
                                    describedBy={errors.end_time ? "institutional-event-end-time-error" : undefined}
                                    onChange={(value) => form.setData("end_time", value)}
                                />
                            </FormField>
                        </FormGrid>
                    </FormSection>
                    <FormSection
                        id="institutional-event-details"
                        title="Detalhes Adicionais"
                        variant="inline"
                        iconClass="fa fa-info-circle"
                    >
                        <FormGrid columns={2}>
                            <FormField
                                id="institutional-event-location"
                                label="Local"
                                error={fieldError("location")}
                                requiredIndicator
                            >
                                <TextInput
                                    id="institutional-event-location"
                                    name="location"
                                    value={form.data.location}
                                    maxLength={255}
                                    placeholder="Ex.: Sala de Reuniões 1"
                                    required
                                    invalid={!!errors.location}
                                    aria-describedby={errors.location ? "institutional-event-location-error" : undefined}
                                    onChange={(inputEvent) => form.setData("location", inputEvent.target.value)}
                                />
                            </FormField>
                            <FormField
                                id="institutional-event-organizer"
                                label="Organizador"
                                error={fieldError("organizer")}
                            >
                                <TextInput
                                    id="institutional-event-organizer"
                                    name="organizer"
                                    value={form.data.organizer}
                                    maxLength={255}
                                    placeholder="Ex.: Coordenação Pedagógica"
                                    invalid={!!errors.organizer}
                                    aria-describedby={errors.organizer ? "institutional-event-organizer-error" : undefined}
                                    onChange={(inputEvent) => form.setData("organizer", inputEvent.target.value)}
                                />
                            </FormField>
                            <FormField
                                id="institutional-event-audience"
                                label="Ouvintes"
                                error={fieldError("audience")}
                                className="institutionalEventForm__wide"
                            >
                                <TextInput
                                    id="institutional-event-audience"
                                    name="audience"
                                    value={form.data.audience}
                                    maxLength={255}
                                    placeholder="Ex.: Professores, Equipe Administrativa"
                                    invalid={!!errors.audience}
                                    aria-describedby={errors.audience ? "institutional-event-audience-error" : undefined}
                                    onChange={(inputEvent) => form.setData("audience", inputEvent.target.value)}
                                />
                            </FormField>
                        </FormGrid>
                    </FormSection>
                    <FormSection
                        id="institutional-event-settings"
                        title="Configurações"
                        variant="inline"
                        iconClass="fa fa-cog"
                    >
                        <ToggleControl
                            id="institutional-event-active"
                            name="is_active"
                            size="small"
                            label="Ativo"
                            labelAddon={(
                                <InfoTooltip text="Será exibido na agenda institucional." />
                            )}
                            checked={form.data.is_active}
                            onChange={(checked) => form.setData("is_active", checked)}
                        />
                    </FormSection>
                </CardBox>
                <FormActions>
                    <Button
                        href={backRoute}
                        tone="cancel"
                        label="Cancelar"
                        title="Cancelar e voltar para a agenda institucional"
                        aria-label="Cancelar e voltar para a agenda institucional"
                        disabled={form.processing}
                    />
                    {canSubmit ? (
                        <Button
                            type="submit"
                            tone="save"
                            label={editing ? "Salvar" : "Cadastrar"}
                            title={editing ? "Salvar alterações" : "Cadastrar evento"}
                            aria-label={editing ? "Salvar alterações" : "Cadastrar evento"}
                            disabled={form.processing}
                        />
                    ) : null}
                </FormActions>
            </FormLayout>
        </InstitutionalEventPage>
    );
}
