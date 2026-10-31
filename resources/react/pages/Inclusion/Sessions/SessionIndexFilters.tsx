import { useMemo } from "react";
import DatePicker from "@/components/Forms/DatePicker/DatePicker";
import FormField from "@/components/Forms/FormField/FormField";
import FormLayout from "@/components/Forms/FormLayout/FormLayout";
import SelectField from "@/components/Forms/SelectField/SelectField";
import {
    TableListClearButton,
    TableListFilterActions,
} from "@/components/Tables/TableList/TableList";
import useAutoListFilters from "@/hooks/useAutoListFilters";
import type { SessionListFilters, SessionListOptions } from "./SessionList.types";

export type SessionIndexFilterValues = Pick<
    SessionListFilters,
    "week" | "student" | "professional" | "type" | "status"
>;

function currentDate(): string {
    const today = new Date();
    const year = today.getFullYear();
    const month = String(today.getMonth() + 1).padStart(2, "0");
    const day = String(today.getDate()).padStart(2, "0");
    return `${year}-${month}-${day}`;
}

export function sessionIndexFilterValues(
    filters: SessionListFilters,
): SessionIndexFilterValues {
    return {
        week: filters.week,
        student: filters.student,
        professional: filters.professional,
        type: filters.type,
        status: filters.status,
    };
}

export default function SessionIndexFilters({
    filters,
    options,
    showProfessional = true,
    onSearch,
}: {
    filters: SessionListFilters;
    options: SessionListOptions;
    showProfessional?: boolean;
    onSearch: (values: SessionIndexFilterValues) => void;
}): React.JSX.Element {
    const initialValues = useMemo(
        () => sessionIndexFilterValues(filters),
        [filters],
    );
    const emptyValues = useMemo<SessionIndexFilterValues>(
        () => ({
            week: currentDate(),
            student: "",
            professional: "",
            type: "",
            status: "",
        }),
        [],
    );
    const { values, update, clear, submit } = useAutoListFilters({
        initialValues,
        emptyValues,
        onSearch,
    });

    return (
        <FormLayout
            aria-label="Filtrar calendário e listagem de agendamentos"
            onSubmit={(event) => {
                event.preventDefault();
                submit();
            }}
        >
            <div
                className={`sessionsPage__sharedFilters${showProfessional ? "" : " sessionsPage__sharedFilters--compact"}`}
            >
                <FormField id="session-filter-reference-day" label="Dia de referência">
                    <DatePicker
                        id="session-filter-reference-day"
                        name="week"
                        label="Dia de referência"
                        value={values.week}
                        onChange={(value) => {
                            if (value !== values.week) update("week", value);
                        }}
                    />
                </FormField>
                <FormField id="session-filter-student" label="Aluno">
                    <SelectField
                        id="session-filter-student"
                        name="student"
                        label="Aluno"
                        placeholder="Todos os Alunos"
                        value={values.student}
                        options={options.students ?? []}
                        onChange={(event) => update("student", event.target.value)}
                    />
                </FormField>
                {showProfessional ? (
                    <FormField id="session-filter-professional" label="Professor">
                        <SelectField
                            id="session-filter-professional"
                            name="professional"
                            label="Professor"
                            placeholder="Todos os Professores"
                            value={values.professional}
                            options={options.professionals ?? []}
                            onChange={(event) => update("professional", event.target.value)}
                        />
                    </FormField>
                ) : null}
                <FormField id="session-filter-type" label="Tipo">
                    <SelectField
                        id="session-filter-type"
                        name="type"
                        label="Tipo"
                        placeholder="Todos os Tipos"
                        value={values.type}
                        options={options.types}
                        onChange={(event) => update("type", event.target.value)}
                    />
                </FormField>
                <FormField id="session-filter-status" label="Status">
                    <SelectField
                        id="session-filter-status"
                        name="status"
                        label="Status"
                        placeholder="Todos os Status"
                        value={values.status}
                        options={options.statuses}
                        onChange={(event) => update("status", event.target.value)}
                    />
                </FormField>
                <TableListFilterActions>
                    <TableListClearButton onClick={clear} />
                </TableListFilterActions>
            </div>
        </FormLayout>
    );
}
