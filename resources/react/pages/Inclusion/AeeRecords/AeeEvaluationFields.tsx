import FormField from "@/components/Forms/FormField/FormField";
import FormGrid from "@/components/Forms/FormGrid/FormGrid";
import { FormGridWide } from "@/components/Forms/FormGrid/FormGrid";
import TextArea from "@/components/Forms/TextArea/TextArea";
import ToggleControl from "@/components/Forms/ToggleControl/ToggleControl";
import type {
    AeeEvaluationFormData,
    AeeEvaluationTextField,
} from "./AeeRecord.types";

interface TextFieldDefinition {
    field: AeeEvaluationTextField;
    label: string;
    placeholder: string;
    required?: boolean;
    half?: boolean;
}

const presenceDefinitions: TextFieldDefinition[] = [
    {
        field: "student_participation",
        label: "Participação",
        placeholder: "Descreva o nível de participação do aluno...",
        required: true,
    },
    {
        field: "adaptations_made",
        label: "Adaptações para este Aluno",
        placeholder: "Adaptações realizadas durante o atendimento AEE...",
    },
    {
        field: "development_evaluation",
        label: "Avaliação do Desenvolvimento",
        placeholder: "Avaliação do desempenho e evolução do aluno...",
        required: true,
    },
    {
        field: "progress_indicators",
        label: "Indicadores de Progresso",
        placeholder: "Evidências observáveis de progresso...",
    },
    {
        field: "recommendations",
        label: "Recomendações",
        placeholder: "Sugestões para continuidade do trabalho...",
        half: true,
    },
    {
        field: "next_session_adjustments",
        label: "Ajustes para Próximo atendimento AEE",
        placeholder: "O que ajustar ou manter para o próximo atendimento AEE...",
        half: true,
    },
];

const absenceDefinition: TextFieldDefinition = {
    field: "absence_reason",
    label: "Motivo da Ausência",
    placeholder: "Informe o motivo da ausência do aluno...",
    required: true,
};

export default function AeeEvaluationFields({
    idPrefix,
    namePrefix,
    values,
    errorFor,
    disabled,
    onPresenceChange,
    onFieldChange,
}: {
    idPrefix: string;
    namePrefix?: string;
    values: AeeEvaluationFormData;
    errorFor: (field: keyof AeeEvaluationFormData) => string | undefined;
    disabled: boolean;
    onPresenceChange: (present: boolean) => void;
    onFieldChange: (field: AeeEvaluationTextField, value: string) => void;
}): React.JSX.Element {
    const nameFor = (field: string): string => (
        namePrefix ? `${namePrefix}[${field}]` : field
    );

    const renderTextArea = (definition: TextFieldDefinition): React.JSX.Element => {
        const id = `${idPrefix}-${definition.field.replace(/_/g, "-")}`;
        const error = errorFor(definition.field);

        return (
            <FormField
                id={id}
                label={definition.label}
                requiredIndicator={definition.required}
                error={error}
            >
                <TextArea
                    id={id}
                    name={nameFor(definition.field)}
                    rows={definition.field === "absence_reason" ? 2 : 3}
                    required={definition.required}
                    placeholder={definition.placeholder}
                    value={values[definition.field]}
                    aria-invalid={!!error}
                    aria-describedby={error ? `${id}-error` : undefined}
                    disabled={disabled}
                    onChange={(event) => onFieldChange(definition.field, event.target.value)}
                />
            </FormField>
        );
    };

    const presenceError = errorFor("is_present");

    return (
        <FormGrid columns={2}>
            <FormGridWide>
                <ToggleControl
                    id={`${idPrefix}-is-present`}
                    name={nameFor("is_present")}
                    size="small"
                    label="Aluno Presente"
                    description={presenceError}
                    checked={values.is_present}
                    disabled={disabled}
                    onChange={onPresenceChange}
                />
            </FormGridWide>
            {values.is_present ? (
                presenceDefinitions.map((definition) => (
                    definition.half ? (
                        <div key={definition.field}>
                            {renderTextArea(definition)}
                        </div>
                    ) : (
                        <FormGridWide key={definition.field}>
                            {renderTextArea(definition)}
                        </FormGridWide>
                    )
                ))
            ) : (
                <FormGridWide>
                    {renderTextArea(absenceDefinition)}
                </FormGridWide>
            )}
        </FormGrid>
    );
}
