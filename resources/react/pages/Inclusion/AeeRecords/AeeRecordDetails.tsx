import FormSection from "@/components/Forms/FormSection/FormSection";
import DetailField from "@/components/UI/DetailField/DetailField";
import TableLabel from "@/components/UI/TableLabel/TableLabel";
import type {
    AeeRecordRecord,
    AeeStudentEvaluationRecord,
} from "./AeeRecord.types";

/**
 * Textos de textarea são exibidos como texto simples, preservando as
 * quebras de linha digitadas, com o mesmo padrão do legado para vazios.
 */
export function AeeTextValue({
    value,
    fallback = "N/A",
}: {
    value?: string | null;
    fallback?: string;
}): React.JSX.Element {
    return (
        <p className="aeeRecordForm__text">
            {value?.trim() ? value : fallback}
        </p>
    );
}

export function AeeRecordExecutionSection({
    id,
    aeeRecord,
}: {
    id: string;
    aeeRecord: AeeRecordRecord;
}): React.JSX.Element {
    return (
        <FormSection
            id={id}
            title="Execução do Atendimento AEE"
            variant="inline"
            iconClass="fa fa-clipboard"
        >
            <dl className="aeeRecordForm__details aeeRecordForm__details--three">
                <DetailField label="Duração">
                    {aeeRecord.duration || "N/A"}
                </DetailField>
            </dl>
            <dl className="aeeRecordForm__details">
                <DetailField label="Atividades Realizadas" wide>
                    <AeeTextValue value={aeeRecord.activities_performed} />
                </DetailField>
                <DetailField label="Estratégias Utilizadas">
                    <AeeTextValue value={aeeRecord.strategies_used} />
                </DetailField>
                <DetailField label="Recursos Utilizados">
                    <AeeTextValue value={aeeRecord.resources_used} />
                </DetailField>
                <DetailField label="Observações Gerais" wide>
                    <AeeTextValue value={aeeRecord.general_observations} />
                </DetailField>
            </dl>
        </FormSection>
    );
}

export function AeeEvaluationDetails({
    evaluation,
    studentName,
}: {
    evaluation: AeeStudentEvaluationRecord;
    studentName: string;
}): React.JSX.Element {
    const present = Boolean(evaluation.is_present);

    return (
        <div className="aeeRecordForm__evaluation">
            <h3 className="aeeRecordForm__evaluationTitle">
                Avaliação: {studentName}
            </h3>
            <dl className="aeeRecordForm__details">
                <DetailField label="Presença" wide>
                    <TableLabel tone={present ? "success" : "danger"}>
                        {present ? "Presente" : "Aluno Ausente"}
                    </TableLabel>
                </DetailField>
                {present ? (
                    <>
                        <DetailField label="Participação" wide>
                            <AeeTextValue value={evaluation.student_participation} />
                        </DetailField>
                        <DetailField label="Adaptações Realizadas" wide>
                            <AeeTextValue
                                value={evaluation.adaptations_made}
                                fallback="Nenhuma adaptação informada."
                            />
                        </DetailField>
                        <DetailField label="Avaliação do Desenvolvimento" wide>
                            <AeeTextValue value={evaluation.development_evaluation} />
                        </DetailField>
                        <DetailField label="Indicadores de Progresso" wide>
                            <AeeTextValue value={evaluation.progress_indicators} />
                        </DetailField>
                        <DetailField label="Recomendações">
                            <AeeTextValue
                                value={evaluation.recommendations}
                                fallback="Nenhuma recomendação."
                            />
                        </DetailField>
                        <DetailField label="Ajustes para Próximo atendimento AEE">
                            <AeeTextValue value={evaluation.next_session_adjustments} />
                        </DetailField>
                    </>
                ) : (
                    <DetailField label="Motivo" wide>
                        <AeeTextValue
                            value={evaluation.absence_reason}
                            fallback="Não justificado."
                        />
                    </DetailField>
                )}
            </dl>
        </div>
    );
}
