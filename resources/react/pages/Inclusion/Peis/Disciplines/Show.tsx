import FormActions from "@/components/Forms/FormActions/FormActions";
import FormSection from "@/components/Forms/FormSection/FormSection";
import BusinessPage from "@/components/Navigation/BusinessPage/BusinessPage";
import {
    aeeBase,
    studentUrl,
    studentsBase,
} from "@/components/Students/Students.utils";
import Button from "@/components/UI/Button/Button";
import CardBox from "@/components/UI/CardBox/CardBox";
import DetailField from "@/components/UI/DetailField/DetailField";
import PeiConfirmAction from "@/pages/Inclusion/Peis/PeiConfirmAction";
import PeiRichText from "@/pages/Inclusion/Peis/PeiRichText";
import type {
    PeiDisciplineDetails,
    PeiDisciplineShowProps,
} from "@/pages/Inclusion/Peis/PeiShow.types";
import "@/components/Students/Students.css";
import "@/pages/Inclusion/Peis/PeiShow.css";

const fields: Array<{
    name: keyof Pick<
        PeiDisciplineDetails,
        | "specific_objectives_html"
        | "content_programmatic_html"
        | "methodologies_html"
        | "evaluations_html"
        | "complementary_records_html"
        | "opinion_html"
    >;
    label: string;
}> = [
    { name: "specific_objectives_html", label: "Objetivos Específicos" },
    { name: "content_programmatic_html", label: "Conteúdo Programático" },
    { name: "methodologies_html", label: "Metodologias e Estratégias" },
    { name: "evaluations_html", label: "Processo de Avaliação" },
    { name: "complementary_records_html", label: "Registros Complementares" },
    { name: "opinion_html", label: "Parecer" },
];

export default function InclusionPeisDisciplinesShowPage({
    pei,
    student,
    peiDiscipline,
    can,
}: PeiDisciplineShowProps): React.JSX.Element {
    const peiUrl = `${aeeBase}/peis/${pei.id}`;
    const disciplineUrl = `${peiUrl}/disciplines/${peiDiscipline.id}`;
    const disciplineName = peiDiscipline.discipline_name || "Não informado";

    return (
        <BusinessPage
            title={`Planejamento da Disciplina ${disciplineName}`}
            subtitle={`Visualize a adaptação para a disciplina ${disciplineName}.`}
            breadcrumbItems={[
                { label: "Home", href: "/auth/dashboard" },
                { label: "Alunos", href: studentsBase },
                { label: student.name, href: studentUrl(student.id) },
                { label: `PEI #${pei.id}`, href: `${peiUrl}/show?tab=disciplines` },
                { label: disciplineName },
            ]}
            className="studentsPage peiShow"
        >
            <CardBox>
                <FormSection
                    id="pei-discipline-details"
                    title="Adaptações Razoáveis e/ou Acessibilidades Curriculares"
                    iconClass="fa fa-book"
                    variant="inline"
                >
                    <dl className="peiShow__grid">
                        {fields.map((field) => (
                            <DetailField
                                key={field.name}
                                label={field.label}
                                wide
                            >
                                <PeiRichText html={peiDiscipline[field.name]} />
                            </DetailField>
                        ))}
                    </dl>
                </FormSection>
            </CardBox>
            <FormActions
                secondary={can.pdf ? (
                    <Button
                        href={`${peiUrl}/disciplinas/${peiDiscipline.id}/pdf`}
                        target="_blank"
                        rel="noopener noreferrer"
                        tone="dark-red"
                        leftIconClass="fa fa-file-pdf-o"
                        label="PDF"
                        title="Gerar PDF da adaptação"
                        aria-label="Gerar PDF da adaptação"
                    />
                ) : undefined}
            >
                <Button
                    href={`${peiUrl}/show?tab=disciplines`}
                    tone="cancel"
                    label="Voltar"
                    title="Voltar para o PEI"
                    aria-label="Voltar para o PEI"
                />
                {can.update ? (
                    <Button
                        href={`${disciplineUrl}/edit`}
                        tone="warning"
                        leftIconClass="fa fa-pencil"
                        label="Editar"
                        title="Editar adaptação"
                        aria-label="Editar adaptação"
                    />
                ) : null}
                {can.delete ? (
                    <PeiConfirmAction
                        method="delete"
                        action={disciplineUrl}
                        label="Excluir"
                        ariaLabel="Excluir adaptação"
                        tone="dark-red"
                        iconClass="fa fa-trash-o"
                        modalTitle="Excluir Adaptação"
                        message="Deseja realmente excluir esta adaptação?"
                        confirmLabel="Confirmar exclusão"
                        processingLabel="Excluindo adaptação..."
                    />
                ) : null}
            </FormActions>
        </BusinessPage>
    );
}
