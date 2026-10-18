import { useEffect, useMemo, useRef, useState } from "react";
import { router } from "@inertiajs/react";
import FormField from "@/components/Forms/FormField/FormField";
import FormLayout from "@/components/Forms/FormLayout/FormLayout";
import TextInput from "@/components/Forms/TextInput/TextInput";
import SelectField from "@/components/Forms/SelectField/SelectField";
import InlineAlert from "@/components/UI/InlineAlert/InlineAlert";
import {
    TableListClearButton,
    TableListFilterActions,
    TableListMoreFilters,
} from "@/components/Tables/TableList/TableList";
import useAutoListFilters, {
    compactListFilters,
} from "@/hooks/useAutoListFilters";
import { studentsBase } from "./Students.utils";
import type {
    StudentFilters as Filters,
    StudentIndexProps,
} from "./Students.types";
import { useStudentRequest } from "./useStudentRequest";
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
}: Pick<
    StudentIndexProps,
    "filters" | "courses" | "deficiencies" | "studentStatuses"
>): React.JSX.Element {
    const [processing, setProcessing] = useState(false);
    const [errors, setErrors] = useState<Record<string, string>>({});
    const cancel = useRef<(() => void) | undefined>(undefined);
    const request = useStudentRequest();
    const initialValues = useMemo(() => filterValues(filters), [filters]);
    const emptyValues = useMemo(() => filterValues({}), []);
    const { values, update, clear, submit } = useAutoListFilters({
        initialValues,
        emptyValues,
        onSearch: search,
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
    useEffect(
        () => () => {
            cancel.current?.();
        },
        [],
    );
    function search(next: Filters) {
        cancel.current?.();
        const data = compactListFilters(next);
        router.get(studentsBase, data, {
            ...request.options,
            preserveState: true,
            preserveScroll: true,
            replace: true,
            onCancelToken: (token) => {
                cancel.current = () => token.cancel();
            },
            onStart: () => {
                setProcessing(true);
                setErrors({});
            },
            onError: setErrors,
            onFinish: () => {
                setProcessing(false);
                cancel.current = undefined;
            },
        });
    }
    return (
        <FormLayout
            className="studentsPage__filterForm"
            aria-label="Filtrar alunos"
            aria-busy={processing}
            onSubmit={(event) => {
                event.preventDefault();
                submit();
            }}
        >
            {request.error ? (
                <div role="alert">
                    <InlineAlert variant="danger">{request.error}</InlineAlert>
                </div>
            ) : null}
            <div className="studentsPage__filters">
                {textFilterFields.slice(0, 1).map(renderTextFilter)}
                {selectFilterFields.slice(0, 2).map(renderSelectFilter)}
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
            <TableListFilterActions
                status={processing ? "Carregando alunos..." : undefined}
            >
                <TableListClearButton disabled={processing} onClick={clear} />
            </TableListFilterActions>
        </FormLayout>
    );
}
