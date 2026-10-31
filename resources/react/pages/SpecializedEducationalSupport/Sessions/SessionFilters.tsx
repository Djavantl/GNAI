import { useMemo } from "react";
import FormField from "@/components/Forms/FormField/FormField";
import FormLayout from "@/components/Forms/FormLayout/FormLayout";
import SelectField from "@/components/Forms/SelectField/SelectField";
import type { SelectFieldOption } from "@/components/Forms/SelectField/SelectField.types";
import {
    TableListClearButton,
    TableListFilterActions,
} from "@/components/Tables/TableList/TableList";
import useAutoListFilters from "@/hooks/useAutoListFilters";
import type {
    SessionListFilters,
    SessionListOptions,
    SessionTableFilterKey,
} from "./SessionList.types";

const filterLabels: Record<SessionTableFilterKey, string> = {
    student: "Aluno",
    professional: "Profissional",
    type: "Tipo",
    status: "Status",
};

function pick(
    filters: SessionListFilters,
    keys: SessionTableFilterKey[],
): Record<string, string> {
    return Object.fromEntries(keys.map((key) => [key, filters[key] ?? ""]));
}

export default function SessionFilters({
    fields,
    filters,
    options,
    onSearch,
}: {
    fields: SessionTableFilterKey[];
    filters: SessionListFilters;
    options: SessionListOptions;
    onSearch: (values: Record<string, string>) => void;
}): React.JSX.Element {
    // `fields` must be a stable (module-level) list to keep the memoized values.
    const initialValues = useMemo(
        () => pick(filters, fields),
        [filters, fields],
    );
    const emptyValues = useMemo(
        () => Object.fromEntries(fields.map((key) => [key, ""])),
        [fields],
    );
    const { values, update, clear, submit } = useAutoListFilters({
        initialValues,
        emptyValues,
        onSearch,
    });
    const fieldOptions: Record<SessionTableFilterKey, SelectFieldOption[]> = {
        student: options.students ?? [],
        professional: options.professionals ?? [],
        type: options.types,
        status: options.statuses,
    };

    return (
        <FormLayout
            aria-label="Filtrar agendamentos"
            onSubmit={(event) => {
                event.preventDefault();
                submit();
            }}
        >
            <div className={`sessionsPage__filters sessionsPage__filters--${fields.length}`}>
                {fields.map((name) => (
                    <FormField
                        key={name}
                        id={`session-filter-${name}`}
                        label={filterLabels[name]}
                    >
                        <SelectField
                            id={`session-filter-${name}`}
                            name={name}
                            label={filterLabels[name]}
                            placeholder={`${filterLabels[name]} (Todos)`}
                            value={values[name] ?? ""}
                            options={fieldOptions[name]}
                            onChange={(event) => update(name, event.target.value)}
                        />
                    </FormField>
                ))}
                <TableListFilterActions>
                    <TableListClearButton onClick={clear} />
                </TableListFilterActions>
            </div>
        </FormLayout>
    );
}
