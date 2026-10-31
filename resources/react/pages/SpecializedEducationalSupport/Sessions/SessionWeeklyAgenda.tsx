import { useMemo } from "react";
import DatePicker from "@/components/Forms/DatePicker/DatePicker";
import FormField from "@/components/Forms/FormField/FormField";
import FormLayout from "@/components/Forms/FormLayout/FormLayout";
import FormSection from "@/components/Forms/FormSection/FormSection";
import SelectField from "@/components/Forms/SelectField/SelectField";
import type { SelectFieldOption } from "@/components/Forms/SelectField/SelectField.types";
import {
    TableListCard,
    TableListClearButton,
    TableListFilterActions,
} from "@/components/Tables/TableList/TableList";
import Button from "@/components/UI/Button/Button";
import TableLabel from "@/components/UI/TableLabel/TableLabel";
import { dateLabel } from "@/components/Students/Students.utils";
import useAutoListFilters from "@/hooks/useAutoListFilters";
import { sessionShowUrl } from "./SessionForm.utils";
import type {
    SessionListFilters,
    SessionWeeklyAgendaData,
    SessionWeekNavigation,
} from "./SessionList.types";

function isFilterDate(value: string): boolean {
    return value === "" || /^\d{4}-\d{2}-\d{2}$/.test(value);
}

export default function SessionWeeklyAgenda({
    title,
    subtitle,
    agenda,
    weekNavigation,
    filters,
    students,
    professionals,
    canView,
    loading,
    onSearch,
    onNavigate,
}: {
    title: string;
    subtitle: string;
    agenda: SessionWeeklyAgendaData;
    weekNavigation: SessionWeekNavigation;
    filters: SessionListFilters;
    students: SelectFieldOption[];
    professionals?: SelectFieldOption[];
    canView: boolean;
    loading: boolean;
    onSearch: (values: Record<string, string>) => void;
    onNavigate: (url: string) => void;
}): React.JSX.Element {
    const withProfessional = professionals !== undefined;
    const initialValues = useMemo<Record<string, string>>(
        () => ({
            week: filters.week ?? "",
            student_agenda: filters.student_agenda ?? "",
            ...(withProfessional
                ? { professional_agenda: filters.professional_agenda ?? "" }
                : {}),
        }),
        [filters, withProfessional],
    );
    const emptyValues = useMemo<Record<string, string>>(
        () => ({
            week: "",
            student_agenda: "",
            ...(withProfessional ? { professional_agenda: "" } : {}),
        }),
        [withProfessional],
    );
    const { values, update, clear, submit } = useAutoListFilters({
        initialValues,
        emptyValues,
        onSearch,
    });

    return (
        <TableListCard
            className="sessionsPage__agendaCard"
            aria-label={title}
            loading={loading}
            loadingLabel="Atualizando agenda semanal"
        >
            <FormSection
                id="session-weekly-agenda"
                variant="inline"
                title={title}
                description={subtitle}
                iconClass="fa fa-calendar"
            >
                <div className="sessionsPage__agendaToolbar">
                    <FormLayout
                        aria-label="Filtrar agenda semanal"
                        onSubmit={(event) => {
                            event.preventDefault();
                            submit();
                        }}
                    >
                        <div className="sessionsPage__agendaFilters">
                            <FormField id="session-agenda-week" label="Dia de referência">
                                <DatePicker
                                    id="session-agenda-week"
                                    name="week"
                                    label="Dia de referência"
                                    value={values.week ?? ""}
                                    onChange={(value) => {
                                        if (isFilterDate(value) && value !== values.week) {
                                            update("week", value);
                                        }
                                    }}
                                />
                            </FormField>
                            <FormField id="session-agenda-student" label="Filtrar Aluno">
                                <SelectField
                                    id="session-agenda-student"
                                    name="student_agenda"
                                    label="Filtrar Aluno"
                                    placeholder="Todos os Alunos"
                                    value={values.student_agenda ?? ""}
                                    options={students}
                                    onChange={(event) => update("student_agenda", event.target.value)}
                                />
                            </FormField>
                            {professionals ? (
                                <FormField id="session-agenda-professional" label="Filtrar Professor">
                                    <SelectField
                                        id="session-agenda-professional"
                                        name="professional_agenda"
                                        label="Filtrar Professor"
                                        placeholder="Todos os Professores"
                                        value={values.professional_agenda ?? ""}
                                        options={professionals}
                                        onChange={(event) => update("professional_agenda", event.target.value)}
                                    />
                                </FormField>
                            ) : null}
                            <TableListFilterActions>
                                <TableListClearButton
                                    disabled={loading}
                                    onClick={clear}
                                />
                            </TableListFilterActions>
                        </div>
                    </FormLayout>
                    <nav className="sessionsPage__weekNav" aria-label="Navegação entre semanas">
                        <Button
                            type="button"
                            size="sm"
                            tone="inverse"
                            leftIconClass="fa fa-chevron-left"
                            label="Semana anterior"
                            disabled={loading}
                            onClick={() => onNavigate(weekNavigation.previous)}
                        />
                        <Button
                            type="button"
                            size="sm"
                            tone="list-action"
                            leftIconClass="fa fa-calendar-o"
                            label="Semana atual"
                            disabled={loading}
                            onClick={() => onNavigate(weekNavigation.current)}
                        />
                        <Button
                            type="button"
                            size="sm"
                            tone="inverse"
                            rightIconClass="fa fa-chevron-right"
                            label="Próxima semana"
                            disabled={loading}
                            onClick={() => onNavigate(weekNavigation.next)}
                        />
                    </nav>
                </div>

                <p className="sessionsPage__agendaSummary">
                    <span>
                        <strong>Período:</strong>
                        {" "}
                        {dateLabel(agenda.weekStart)} até {dateLabel(agenda.weekEnd)}
                    </span>
                    <span>
                        <strong>Total de agendamentos na semana:</strong>
                        {" "}
                        {agenda.total}
                    </span>
                </p>

                <ol className="sessionsPage__agendaDays">
                    {agenda.days.map((day) => (
                        <li key={day.date} className="sessionsPage__day">
                            <header className="sessionsPage__dayHeader">
                                <h3 className="sessionsPage__dayTitle">
                                    {day.label}
                                    <small>{dateLabel(day.date)}</small>
                                </h3>
                                <span
                                    className="sessionsPage__dayCount"
                                    aria-label={`${day.sessions.length} agendamento(s)`}
                                >
                                    {day.sessions.length}
                                </span>
                            </header>
                            {day.sessions.length ? (
                                <ul className="sessionsPage__daySessions">
                                    {day.sessions.map((session) => (
                                        <li key={session.id} className="sessionsPage__agendaSession">
                                            <div className="sessionsPage__agendaSessionHead">
                                                <strong>
                                                    {session.start_time ?? "--:--"} - {session.end_time ?? "--:--"}
                                                </strong>
                                                <TableLabel tone={session.status_color}>
                                                    {session.status_label}
                                                </TableLabel>
                                            </div>
                                            <p>
                                                <strong>Profissional:</strong> {session.professional_name}
                                            </p>
                                            <p>
                                                <strong>Aluno(s):</strong>
                                                {" "}
                                                {session.student_names.join(", ") || "Sem alunos"}
                                            </p>
                                            <p
                                                className={
                                                    session.has_record
                                                        ? "sessionsPage__record sessionsPage__record--done"
                                                        : "sessionsPage__record sessionsPage__record--missing"
                                                }
                                            >
                                                <i
                                                    className={session.has_record ? "fa fa-check-circle" : "fa fa-clock-o"}
                                                    aria-hidden="true"
                                                />
                                                {" "}
                                                {session.record_label}
                                            </p>
                                            {canView ? (
                                                <div className="sessionsPage__agendaSessionActions">
                                                    <Button
                                                        href={sessionShowUrl(session.id)}
                                                        size="sm"
                                                        tone="dark-blue"
                                                        leftIconClass="fa fa-eye"
                                                        label="Ver"
                                                        title={`Visualizar agendamento das ${session.start_time ?? ""} de ${dateLabel(day.date)}`}
                                                        aria-label={`Visualizar agendamento das ${session.start_time ?? ""} de ${dateLabel(day.date)}`}
                                                    />
                                                </div>
                                            ) : null}
                                        </li>
                                    ))}
                                </ul>
                            ) : (
                                <p className="sessionsPage__dayEmpty">
                                    <i className="fa fa-calendar-o" aria-hidden="true" />
                                    Nenhum agendamento neste dia.
                                </p>
                            )}
                        </li>
                    ))}
                </ol>
            </FormSection>
        </TableListCard>
    );
}
