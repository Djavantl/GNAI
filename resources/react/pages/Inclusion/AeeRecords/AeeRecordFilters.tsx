import type { CSSProperties } from "react";
import FormField from "@/components/Forms/FormField/FormField";
import FormLayout from "@/components/Forms/FormLayout/FormLayout";
import SelectField from "@/components/Forms/SelectField/SelectField";
import {
    TableListClearButton,
    TableListFilterActions,
} from "@/components/Tables/TableList/TableList";
import type {
    AeeFilterOptions,
    AeeListFilterName,
    AeeListFilters,
} from "./AeeRecordList.types";
import "./AeeRecordList.css";

interface AeeRecordFiltersProps extends Partial<AeeFilterOptions> {
    idPrefix: string;
    ariaLabel: string;
    fields: AeeListFilterName[];
    values: Partial<AeeListFilters>;
    errors: Record<string, string | undefined>;
    onChange: (name: AeeListFilterName, value: string) => void;
    onClear: () => void;
    onSubmit: () => void;
}

const fieldLabels: Record<AeeListFilterName, { label: string; placeholder: string }> = {
    student: { label: "Estudante", placeholder: "Estudante (Todos)" },
    professional_id: { label: "Profissional", placeholder: "Profissional (Todos)" },
    course_id: { label: "Curso", placeholder: "Curso (Todos)" },
    is_present: { label: "Presença", placeholder: "Presença (Todas)" },
};

/** Mantém somente os filtros aceitos pela tela, como strings controladas. */
export function aeeFilterValues(
    fields: AeeListFilterName[],
    filters: Partial<AeeListFilters> = {},
): Record<string, string> {
    return Object.fromEntries(
        fields.map((name) => [name, String(filters[name] ?? "")]),
    );
}

export default function AeeRecordFilters({
    idPrefix,
    ariaLabel,
    fields,
    values,
    errors,
    students = [],
    professionals = [],
    courses = [],
    onChange,
    onClear,
    onSubmit,
}: AeeRecordFiltersProps): React.JSX.Element {
    const options: Record<AeeListFilterName, { value: string; label: string }[]> = {
        student: students.map((student) => ({
            value: String(student.id),
            label: student.person?.name ?? `ID ${student.id}`,
        })),
        professional_id: professionals.map((professional) => ({
            value: String(professional.id),
            label: professional.person?.name ?? `ID ${professional.id}`,
        })),
        course_id: [
            { value: "0", label: "Sem curso" },
            ...courses.map((course) => ({
                value: String(course.id),
                label: course.name,
            })),
        ],
        is_present: [
            { value: "1", label: "Presente" },
            { value: "0", label: "Ausente" },
        ],
    };

    return (
        <FormLayout
            aria-label={ariaLabel}
            onSubmit={(event) => {
                event.preventDefault();
                onSubmit();
            }}
        >
            <div
                className="aeeRecordList__filters"
                style={{ "--aee-filter-count": fields.length } as CSSProperties}
            >
                {fields.map((name) => {
                    const id = `${idPrefix}-filter-${name.replace("_", "-")}`;
                    const error = errors[name];

                    return (
                        <FormField
                            key={name}
                            id={id}
                            label={fieldLabels[name].label}
                            error={error}
                        >
                            <SelectField
                                id={id}
                                name={name}
                                label={fieldLabels[name].label}
                                placeholder={fieldLabels[name].placeholder}
                                value={values[name] ?? ""}
                                options={options[name]}
                                aria-invalid={!!error}
                                aria-describedby={error ? `${id}-error` : undefined}
                                onChange={(event) => onChange(name, event.target.value)}
                            />
                        </FormField>
                    );
                })}
                <TableListFilterActions>
                    <TableListClearButton onClick={onClear} />
                </TableListFilterActions>
            </div>
        </FormLayout>
    );
}
