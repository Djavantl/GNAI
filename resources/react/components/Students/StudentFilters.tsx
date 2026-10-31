import { useMemo } from "react";
import { usePage } from "@inertiajs/react";
import FormField from "@/components/Forms/FormField/FormField";
import FormLayout from "@/components/Forms/FormLayout/FormLayout";
import TextInput from "@/components/Forms/TextInput/TextInput";
import SelectField from "@/components/Forms/SelectField/SelectField";
import {
    TableListClearButton,
    TableListFilterActions,
    TableListMoreFilters,
} from "@/components/Tables/TableList/TableList";
import useAutoListFilters from "@/hooks/useAutoListFilters";
import type {
    StudentFilters as Filters,
    StudentIndexProps,
} from "./Students.types";
import "./StudentFilters.css";

export function filterValues(filters: Partial<Filters>): Filters {
    return Object.fromEntries(
        [
            "name",
            "phone",
            "email",
            "status",
            "course_id",
            "deficiency_id",
            "registration",
            "per_page",
        ].map((key) => [key, String(filters[key as keyof Filters] ?? "")]),
    ) as Filters;
}

function filterClassName(name: string): string {
    return `studentsPage__filter studentsPage__filter--${name.replace("_", "-")}`;
}

export default function StudentFilters({
    filters,
    courses,
    deficiencies,
    studentStatuses,
    onSearch,
}: Pick<
    StudentIndexProps,
    "filters" | "courses" | "deficiencies" | "studentStatuses"
> & {
    onSearch: (filters: Filters) => void;
}): React.JSX.Element {
    const errors = usePage().props.errors as Record<string, string | undefined>;
    const initialValues = useMemo(() => filterValues(filters), [filters]);
    const emptyValues = useMemo(() => filterValues({}), []);
    const { values, update, clear, submit } = useAutoListFilters({
        initialValues,
        emptyValues,
        onSearch,
    });
    const textFilterFields = [
        ["name", "Nome do aluno"],
        ["phone", "Telefone"],
        ["email", "E-mail"],
    ] as const;
    const selectFilterFields = [
        {
            name: "status",
            label: "Status",
            options: Object.entries(studentStatuses).map(([value, label]) => ({
                value,
                label,
            })),
        },
        {
            name: "course_id",
            label: "Curso",
            options: [
                { value: "0", label: "Sem curso" },
                ...courses.map((item) => ({
                    value: String(item.id),
                    label: item.name,
                })),
            ],
        },
        {
            name: "deficiency_id",
            label: "Perfil de atendimento",
            options: [
                {
                    value: "0",
                    label: "Sem perfil de atendimento",
                },
                ...deficiencies.map((item) => ({
                    value: String(item.id),
                    label: item.name,
                })),
            ],
        },
    ] as const;

    function renderTextFilter(
        [name, label]: (typeof textFilterFields)[number],
    ): React.JSX.Element {
        return (
            <FormField
                key={name}
                id={`filter-${name}`}
                label={label}
                error={errors[name]}
                className={filterClassName(name)}
            >
                <TextInput
                    id={`filter-${name}`}
                    name={name}
                    value={values[name]}
                    placeholder={`${label}...`}
                    maxLength={name === "phone" ? 15 : 255}
                    invalid={!!errors[name]}
                    aria-describedby={
                        errors[name] ? `filter-${name}-error` : undefined
                    }
                    onChange={(event) => update(name, event.target.value)}
                />
            </FormField>
        );
    }

    function renderSelectFilter({
        name,
        label,
        options,
    }: (typeof selectFilterFields)[number]): React.JSX.Element {
        return (
            <FormField
                key={name}
                id={`filter-${name}`}
                label={label}
                error={errors[name]}
                className={filterClassName(name)}
            >
                <SelectField
                    id={`filter-${name}`}
                    name={name}
                    label={label}
                    value={values[name]}
                    placeholder={`${label} (Todos)`}
                    options={[...options]}
                    aria-invalid={!!errors[name]}
                    aria-describedby={
                        errors[name] ? `filter-${name}-error` : undefined
                    }
                    onChange={(event) => update(name, event.target.value)}
                />
            </FormField>
        );
    }

    return (
        <FormLayout
            className="studentsPage__filterForm"
            aria-label="Filtrar alunos"
            onSubmit={(event) => {
                event.preventDefault();
                submit();
            }}
        >
            <div className="studentsPage__filters studentsPage__filters--primary">
                {textFilterFields.slice(0, 1).map(renderTextFilter)}
                {selectFilterFields.slice(0, 2).map(renderSelectFilter)}
                <TableListFilterActions>
                    <TableListClearButton onClick={clear} />
                </TableListFilterActions>
            </div>
            <TableListMoreFilters
                active={Boolean(
                    values.phone || values.email || values.deficiency_id,
                )}
            >
                <div className="studentsPage__filters">
                    {textFilterFields.slice(1).map(renderTextFilter)}
                    {selectFilterFields.slice(2).map(renderSelectFilter)}
                </div>
            </TableListMoreFilters>
        </FormLayout>
    );
}
