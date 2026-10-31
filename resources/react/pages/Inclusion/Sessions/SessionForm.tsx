import { useRef, useState } from "react";
import { useForm } from "@inertiajs/react";
import BusinessPage from "@/components/Navigation/BusinessPage/BusinessPage";
import type { PageBreadcrumbItem } from "@/components/Navigation/PageBreadcrumb/PageBreadcrumb";
import DatePicker from "@/components/Forms/DatePicker/DatePicker";
import FormActions from "@/components/Forms/FormActions/FormActions";
import FormField from "@/components/Forms/FormField/FormField";
import FormGrid, { FormGridWide } from "@/components/Forms/FormGrid/FormGrid";
import FormLayout from "@/components/Forms/FormLayout/FormLayout";
import FormSection from "@/components/Forms/FormSection/FormSection";
import SelectField from "@/components/Forms/SelectField/SelectField";
import TextArea from "@/components/Forms/TextArea/TextArea";
import TextInput from "@/components/Forms/TextInput/TextInput";
import Button from "@/components/UI/Button/Button";
import CardBox from "@/components/UI/CardBox/CardBox";
import DetailField from "@/components/UI/DetailField/DetailField";
import { notify } from "@/components/UI/NotificationToast/notify";
import SessionAvailability from "./SessionAvailability";
import SessionNotificationModal from "./SessionNotificationModal";
import type {
    SessionCreateProps,
    SessionEditProps,
    SessionFormData,
} from "./SessionForm.types";
import {
    adjustEndTime,
    endTimeOptions,
    localToday,
    mapOptions,
    personName,
    sessionShowUrl,
    sessionsBase,
} from "./SessionForm.utils";
import "./SessionForm.css";

type SessionFormProps =
    | ({ mode: "create" } & SessionCreateProps)
    | ({ mode: "edit" } & SessionEditProps);

const PEDAGOGICAL = "pedagogical";
const GROUP = "group";
const INDIVIDUAL = "individual";

export default function SessionForm(props: SessionFormProps): React.JSX.Element {
    const session = props.mode === "edit" ? props.session : undefined;
    const editing = props.mode === "edit";
    const element = useRef<HTMLFormElement>(null);
    const busy = useRef(false);
    const nextRowKey = useRef(1);
    const [rowKeys, setRowKeys] = useState<number[]>([0]);
    const [confirmOpen, setConfirmOpen] = useState(false);

    const form = useForm<SessionFormData>(
        props.mode === "edit"
            ? {
                attendance_type: props.sessionForm.attendance_type,
                type: props.session.type,
                professional_id: String(props.session.professional_id),
                student_ids: props.session.students.map(student => String(student.id)),
                session_date: props.sessionForm.session_date,
                start_time: props.sessionForm.start_time,
                end_time: adjustEndTime(
                    props.endTimes,
                    props.sessionForm.start_time,
                    props.sessionForm.end_time,
                ),
                location: props.session.location ?? "",
                session_objective: props.session.session_objective ?? "",
            }
            : {
                attendance_type: props.defaults.attendance_type,
                type: props.defaults.type,
                professional_id: "",
                student_ids: [""],
                session_date: "",
                start_time: "",
                end_time: "",
                location: "",
                session_objective: "",
            },
    );
    const errors = form.errors as Record<string, string | undefined>;
    const isPedagogical = form.data.attendance_type === PEDAGOGICAL;
    const canAddStudents = !editing && !isPedagogical && form.data.type === GROUP;
    const cancelHref = session ? sessionShowUrl(session.id) : sessionsBase;

    const describedBy = (name: string, id: string): string | undefined =>
        errors[name] ? `${id}-error` : undefined;

    function changeAttendanceType(value: string): void {
        form.setData(data => {
            if (value !== PEDAGOGICAL) return { ...data, attendance_type: value };

            return {
                ...data,
                attendance_type: value,
                type: INDIVIDUAL,
                student_ids: data.student_ids.slice(0, 1),
            };
        });

        if (value === PEDAGOGICAL) setRowKeys(keys => keys.slice(0, 1));
    }

    function changeStudent(index: number, value: string): void {
        form.setData(data => ({
            ...data,
            student_ids: data.student_ids.map((id, position) => (position === index ? value : id)),
        }));
        form.clearErrors("student_ids", `student_ids.${index}` as const);
    }

    function addStudent(): void {
        const key = nextRowKey.current;
        nextRowKey.current += 1;
        setRowKeys(keys => [...keys, key]);
        form.setData(data => ({ ...data, student_ids: [...data.student_ids, ""] }));
    }

    function removeStudent(index: number): void {
        if (form.data.student_ids.length <= 1) return;

        setRowKeys(keys => keys.filter((_, position) => position !== index));
        form.setData(data => ({
            ...data,
            student_ids: data.student_ids.filter((_, position) => position !== index),
        }));
    }

    function changeStartTime(value: string): void {
        form.setData(data => ({
            ...data,
            start_time: value,
            end_time: adjustEndTime(props.endTimes, value, data.end_time),
        }));
    }

    function submit(sendNotification?: boolean): void {
        if (busy.current || form.processing) return;

        busy.current = true;
        form.transform(data => {
            const payload: Record<string, unknown> = {
                ...data,
                type: data.attendance_type === PEDAGOGICAL ? INDIVIDUAL : data.type,
                student_ids: data.student_ids.filter(Boolean),
            };

            if (session) payload.status = session.status;
            if (sendNotification !== undefined) payload.send_notification = sendNotification;

            return payload;
        });

        const options = {
            preserveScroll: true,
            onError: () =>
                requestAnimationFrame(() =>
                    element.current
                        ?.querySelector<HTMLElement>('[aria-invalid="true"]')
                        ?.focus(),
                ),
            onNetworkError: () => {
                notify({
                    tone: "error",
                    title: "Falha de conexão",
                    text: "Não foi possível conectar ao servidor. Confira sua conexão e tente novamente.",
                });
                return false;
            },
            onHttpException: () => {
                notify({
                    tone: "error",
                    title: "Não foi possível concluir",
                    text: "Não foi possível salvar o agendamento. Atualize a página e tente novamente.",
                });
                return false;
            },
            onFinish: () => {
                busy.current = false;
            },
        };

        if (session) {
            form.put(`${sessionsBase}/${session.id}`, options);
        } else {
            form.post(`${sessionsBase}/store`, options);
        }
    }

    function handleSubmit(event: React.FormEvent<HTMLFormElement>): void {
        event.preventDefault();
        if (busy.current || form.processing) return;

        // Paridade com o legado: no cadastro, datas passadas são salvas sem perguntar
        // sobre o e-mail; na edição, a escolha é sempre solicitada.
        if (!editing && form.data.session_date < localToday()) {
            submit();
            return;
        }

        setConfirmOpen(true);
    }

    const breadcrumbItems: PageBreadcrumbItem[] = [
        { label: "Home", href: "/auth/dashboard" },
        { label: "Agendamentos", href: sessionsBase },
    ];

    if (session) {
        breadcrumbItems.push({
            label: `Agendamento #${session.id}`,
            href: sessionShowUrl(session.id),
        });
    }

    breadcrumbItems.push({ label: editing ? "Editar" : "Cadastrar" });

    const timeFields = (
        <FormGrid columns={2} className="sessionForm__times">
            <FormField
                id="session-start-time"
                label="Início"
                requiredIndicator
                error={errors.start_time}
            >
                <SelectField
                    id="session-start-time"
                    name="start_time"
                    label="Início"
                    required
                    disabled={form.processing}
                    value={form.data.start_time}
                    options={mapOptions(props.startTimes)}
                    aria-invalid={!!errors.start_time}
                    aria-describedby={describedBy("start_time", "session-start-time")}
                    onChange={event => changeStartTime(event.target.value)}
                />
            </FormField>
            <FormField
                id="session-end-time"
                label="Fim"
                requiredIndicator
                error={errors.end_time}
            >
                <SelectField
                    id="session-end-time"
                    name="end_time"
                    label="Fim"
                    required
                    disabled={form.processing}
                    value={form.data.end_time}
                    options={endTimeOptions(props.endTimes, form.data.start_time)}
                    aria-invalid={!!errors.end_time}
                    aria-describedby={describedBy("end_time", "session-end-time")}
                    onChange={event => form.setData("end_time", event.target.value)}
                />
            </FormField>
        </FormGrid>
    );

    const dateField = (
        <FormField
            id="session-date"
            label="Data do Agendamento"
            requiredIndicator
            error={errors.session_date}
        >
            <DatePicker
                id="session-date"
                name="session_date"
                label="Data do Agendamento"
                required
                disabled={form.processing}
                value={form.data.session_date}
                invalid={!!errors.session_date}
                describedBy={describedBy("session_date", "session-date")}
                onChange={value => form.setData("session_date", value)}
            />
        </FormField>
    );

    const firstStudentId = `session-student-${rowKeys[0]}`;

    return (
        <BusinessPage
            title={session ? `Editar Agendamento #${session.id}` : "Novo Agendamento"}
            subtitle={editing
                ? "Ajuste os dados de agendamento, local ou objetivo deste agendamento."
                : "Preencha os dados para agendar o atendimento especializado."}
            breadcrumbItems={breadcrumbItems}
            className="sessionFormPage"
        >
            <FormLayout
                ref={element}
                onSubmit={handleSubmit}
                aria-label={editing ? "Editar agendamento" : "Cadastrar agendamento"}
                aria-busy={form.processing}
            >
                <CardBox>
                    <fieldset disabled={form.processing}>
                        {props.mode === "create" ? (
                            <FormSection
                                id="session-participants"
                                title="Participantes e Horário"
                                variant="inline"
                                iconClass="fa fa-users"
                            >
                                <FormGrid columns={2}>
                                    <FormField
                                        id="session-attendance-type"
                                        label="Tipo de Atendimento"
                                        requiredIndicator
                                        error={errors.attendance_type}
                                    >
                                        <SelectField
                                            id="session-attendance-type"
                                            name="attendance_type"
                                            label="Tipo de Atendimento"
                                            required
                                            disabled={form.processing}
                                            value={form.data.attendance_type}
                                            options={mapOptions(props.attendanceTypes)}
                                            aria-invalid={!!errors.attendance_type}
                                            aria-describedby={describedBy("attendance_type", "session-attendance-type")}
                                            onChange={event => changeAttendanceType(event.target.value)}
                                        />
                                    </FormField>
                                    <FormField
                                        id="session-type"
                                        label="Formato"
                                        requiredIndicator
                                        error={errors.type}
                                    >
                                        <SelectField
                                            id="session-type"
                                            name="type"
                                            label="Formato"
                                            required
                                            disabled={form.processing || isPedagogical}
                                            value={isPedagogical ? INDIVIDUAL : form.data.type}
                                            options={mapOptions(props.sessionTypes)}
                                            aria-invalid={!!errors.type}
                                            aria-describedby={describedBy("type", "session-type")}
                                            onChange={event => form.setData("type", event.target.value)}
                                        />
                                    </FormField>
                                    <FormField
                                        id="session-professional"
                                        label="Profissional"
                                        requiredIndicator
                                        error={errors.professional_id}
                                    >
                                        <SelectField
                                            id="session-professional"
                                            name="professional_id"
                                            label="Profissional"
                                            required
                                            disabled={form.processing}
                                            value={form.data.professional_id}
                                            options={props.professionals.map(professional => ({
                                                value: String(professional.id),
                                                label: personName(professional),
                                            }))}
                                            aria-invalid={!!errors.professional_id}
                                            aria-describedby={describedBy("professional_id", "session-professional")}
                                            onChange={event => form.setData("professional_id", event.target.value)}
                                        />
                                    </FormField>
                                    <FormField
                                        id={firstStudentId}
                                        label="Alunos Participantes"
                                        requiredIndicator
                                        error={errors.student_ids}
                                    >
                                        <div className="sessionForm__students">
                                            {rowKeys.map((key, index) => {
                                                const rowId = `session-student-${key}`;
                                                const rowError = errors[`student_ids.${index}`];
                                                const describedIds = [
                                                    index === 0 && errors.student_ids ? `${firstStudentId}-error` : undefined,
                                                    rowError ? `${rowId}-row-error` : undefined,
                                                ].filter(Boolean).join(" ") || undefined;

                                                return (
                                                    <div key={key} className="sessionForm__studentRow">
                                                        <div className="sessionForm__studentSelect">
                                                            <SelectField
                                                                id={rowId}
                                                                name="student_ids[]"
                                                                label={`Aluno participante ${index + 1}`}
                                                                required
                                                                disabled={form.processing}
                                                                value={form.data.student_ids[index] ?? ""}
                                                                options={props.students.map(student => ({
                                                                    value: String(student.id),
                                                                    label: personName(student),
                                                                }))}
                                                                aria-invalid={!!(rowError || errors.student_ids)}
                                                                aria-describedby={describedIds}
                                                                onChange={event => changeStudent(index, event.target.value)}
                                                            />
                                                            {rowError ? (
                                                                <span
                                                                    id={`${rowId}-row-error`}
                                                                    className="formField__error"
                                                                >
                                                                    {rowError}
                                                                </span>
                                                            ) : null}
                                                        </div>
                                                        {rowKeys.length > 1 ? (
                                                            <Button
                                                                tone="danger"
                                                                leftIconClass="fa fa-trash"
                                                                label="Remover"
                                                                title={`Remover aluno participante ${index + 1}`}
                                                                aria-label={`Remover aluno participante ${index + 1}`}
                                                                disabled={form.processing}
                                                                onClick={() => removeStudent(index)}
                                                            />
                                                        ) : null}
                                                    </div>
                                                );
                                            })}
                                            {canAddStudents ? (
                                                <div>
                                                    <Button
                                                        tone="success"
                                                        size="sm"
                                                        leftIconClass="fa fa-plus"
                                                        label="Adicionar outro Aluno"
                                                        disabled={form.processing}
                                                        onClick={addStudent}
                                                    />
                                                </div>
                                            ) : null}
                                        </div>
                                    </FormField>
                                    {dateField}
                                    {timeFields}
                                </FormGrid>
                            </FormSection>
                        ) : (
                            <>
                                <FormSection
                                    id="session-participants"
                                    title="Participantes"
                                    variant="inline"
                                    iconClass="fa fa-users"
                                >
                                    <dl className="sessionForm__details">
                                        <DetailField label="Alunos">
                                            <ul className="sessionForm__studentList">
                                                {props.session.students.map(student => (
                                                    <li key={student.id}>{personName(student)}</li>
                                                ))}
                                            </ul>
                                        </DetailField>
                                        <DetailField label="Profissional Responsável">
                                            {personName(props.session.professional)}
                                        </DetailField>
                                    </dl>
                                    {errors.student_ids || errors.professional_id ? (
                                        <p className="formField__error" role="alert">
                                            {errors.student_ids ?? errors.professional_id}
                                        </p>
                                    ) : null}
                                </FormSection>
                                <FormSection
                                    id="session-schedule"
                                    title="Agendamento"
                                    variant="inline"
                                    iconClass="fa fa-calendar"
                                >
                                    <dl className="sessionForm__details">
                                        <DetailField label="Tipo de Atendimento">
                                            {props.attendanceTypes[props.sessionForm.attendance_type]
                                                ?? props.sessionForm.attendance_type}
                                        </DetailField>
                                        <DetailField label="Formato">
                                            {props.sessionTypes[props.session.type] ?? props.session.type}
                                        </DetailField>
                                    </dl>
                                    <FormGrid columns={2}>
                                        {dateField}
                                        {timeFields}
                                    </FormGrid>
                                </FormSection>
                            </>
                        )}
                        <FormSection
                            id="session-availability"
                            title="Disponibilidade do Dia"
                            variant="inline"
                            iconClass="fa fa-clock-o"
                        >
                            <div className="sessionAvailability">
                                <SessionAvailability
                                    date={form.data.session_date}
                                    professionalId={form.data.professional_id}
                                    studentIds={form.data.student_ids}
                                    selectedTime={form.data.start_time}
                                    disabled={form.processing}
                                    onSelect={changeStartTime}
                                />
                            </div>
                        </FormSection>
                        <FormSection
                            id="session-details"
                            title={editing ? "Detalhes Adicionais" : "Detalhes do Atendimento"}
                            variant="inline"
                            iconClass="fa fa-info-circle"
                        >
                            <FormGrid columns={2}>
                                <FormField
                                    id="session-location"
                                    label="Local"
                                    requiredIndicator
                                    error={errors.location}
                                    className={editing ? "formGrid__wide" : undefined}
                                >
                                    <TextInput
                                        id="session-location"
                                        name="location"
                                        required
                                        maxLength={255}
                                        value={form.data.location}
                                        invalid={!!errors.location}
                                        aria-describedby={describedBy("location", "session-location")}
                                        onChange={event => form.setData("location", event.target.value)}
                                    />
                                </FormField>
                                <FormGridWide>
                                    <FormField
                                        id="session-objective"
                                        label="Objetivo do Agendamento"
                                        requiredIndicator
                                        error={errors.session_objective}
                                    >
                                        <TextArea
                                            id="session-objective"
                                            name="session_objective"
                                            required
                                            rows={3}
                                            value={form.data.session_objective}
                                            aria-invalid={!!errors.session_objective}
                                            aria-describedby={describedBy("session_objective", "session-objective")}
                                            onChange={event => form.setData("session_objective", event.target.value)}
                                        />
                                    </FormField>
                                </FormGridWide>
                            </FormGrid>
                        </FormSection>
                    </fieldset>
                </CardBox>
                <FormActions
                    status={form.processing ? "Salvando agendamento..." : undefined}
                >
                    <Button
                        href={cancelHref}
                        tone="cancel"
                        label="Cancelar"
                        title="Cancelar e voltar"
                        aria-label="Cancelar e voltar"
                        disabled={form.processing}
                    />
                    <Button
                        type="submit"
                        tone="save"
                        leftIconClass="fa fa-check"
                        label="Salvar"
                        title="Salvar agendamento"
                        aria-label={form.processing ? "Salvando agendamento" : "Salvar agendamento"}
                        disabled={form.processing}
                    />
                </FormActions>
            </FormLayout>
            <SessionNotificationModal
                open={confirmOpen}
                title={editing ? "Confirmar Alterações" : "Confirmar Novo Agendamento"}
                message={editing
                    ? "Deseja enviar um e-mail aos participantes sobre as alterações deste agendamento?"
                    : "Deseja enviar um e-mail aos participantes sobre este agendamento?"}
                onClose={() => setConfirmOpen(false)}
                onChoose={sendNotification => {
                    setConfirmOpen(false);
                    submit(sendNotification);
                }}
            />
        </BusinessPage>
    );
}
