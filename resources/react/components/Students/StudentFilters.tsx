import { useEffect, useRef, useState } from "react";
import { router } from "@inertiajs/react";
import FormField from "@/components/Forms/FormField/FormField";
import FormLayout from "@/components/Forms/FormLayout/FormLayout";
import TextInput from "@/components/Forms/TextInput/TextInput";
import SelectField from "@/components/Forms/SelectField/SelectField";
import Button from "@/components/UI/Button/Button";
import InlineAlert from "@/components/UI/InlineAlert/InlineAlert";
import { studentsBase } from "./Students.utils";
import type {
    StudentFilters as Filters,
    StudentIndexProps,
} from "./Students.types";
import { useStudentRequest } from "./useStudentRequest";

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
export default function StudentFilters({
    filters,
    courses,
    deficiencies,
    studentStatuses,
}: Pick<
    StudentIndexProps,
    "filters" | "courses" | "deficiencies" | "studentStatuses"
>): React.JSX.Element {
    const [values, setValues] = useState(() => filterValues(filters));
    const [processing, setProcessing] = useState(false);
    const [errors, setErrors] = useState<Record<string, string>>({});
    const timer = useRef<ReturnType<typeof setTimeout> | undefined>(undefined);
    const cancel = useRef<(() => void) | undefined>(undefined);
    const request = useStudentRequest();
    useEffect(() => {
        setValues(filterValues(filters));
    }, [filters]);
    useEffect(
        () => () => {
            clearTimeout(timer.current);
            cancel.current?.();
        },
        [],
    );
    function search(next: Filters) {
        clearTimeout(timer.current);
        cancel.current?.();
        const data = Object.fromEntries(
            Object.entries(next).filter(([, value]) => value !== ""),
        );
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
    function update(key: keyof Filters, value: string) {
        const next = { ...values, [key]: value };
        setValues(next);
        clearTimeout(timer.current);
        timer.current = setTimeout(() => search(next), 500);
    }
    return (
        <FormLayout
            aria-label="Filtrar alunos"
            aria-busy={processing}
            onSubmit={(event) => {
                event.preventDefault();
                search(values);
            }}
        >
            {request.error ? (
                <div role="alert">
                    <InlineAlert variant="danger">{request.error}</InlineAlert>
                </div>
            ) : null}
            <div className="studentsPage__filters">
                {(
                    [
                        ["name", "Nome do aluno"],
                        ["phone", "Telefone"],
                        ["email", "E-mail"],
                    ] as const
                ).map(([name, label]) => (
                    <FormField
                        key={name}
                        id={`filter-${name}`}
                        label={label}
                        error={errors[name]}
                    >
                        <TextInput
                            id={`filter-${name}`}
                            name={name}
                            value={values[name]}
                            placeholder={`${label}...`}
                            maxLength={name === "phone" ? 15 : 255}
                            invalid={!!errors[name]}
                            aria-describedby={
                                errors[name]
                                    ? `filter-${name}-error`
                                    : undefined
                            }
                            onChange={(event) =>
                                update(name, event.target.value)
                            }
                        />
                    </FormField>
                ))}
                {(
                    [
                        {
                            name: "status",
                            label: "Status",
                            options: Object.entries(studentStatuses).map(
                                ([value, label]) => ({ value, label }),
                            ),
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
                    ] as const
                ).map(({ name, label, options }) => (
                    <FormField
                        key={name}
                        id={`filter-${name}`}
                        label={label}
                        error={errors[name]}
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
                                errors[name]
                                    ? `filter-${name}-error`
                                    : undefined
                            }
                            onChange={(event) =>
                                update(name, event.target.value)
                            }
                        />
                    </FormField>
                ))}
            </div>
            <div className="studentsPage__actions">
                <Button type="submit" tone="primary" disabled={processing}>
                    Filtrar
                </Button>
                <Button
                    type="button"
                    onClick={() => {
                        const empty = filterValues({});
                        setValues(empty);
                        search(empty);
                    }}
                >
                    Limpar filtros
                </Button>
                <span role="status">
                    {processing ? "Carregando alunos..." : ""}
                </span>
            </div>
        </FormLayout>
    );
}
