import { useRef } from "react";
import { useForm } from "@inertiajs/react";
import BusinessPage from "@/components/Navigation/BusinessPage/BusinessPage";
import FormActions from "@/components/Forms/FormActions/FormActions";
import FormLayout from "@/components/Forms/FormLayout/FormLayout";
import FormSection from "@/components/Forms/FormSection/FormSection";
import Button from "@/components/UI/Button/Button";
import CardBox from "@/components/UI/CardBox/CardBox";
import DetailField from "@/components/UI/DetailField/DetailField";
import { notify } from "@/components/UI/NotificationToast/notify";
import "./PeiCreate.css";

const aeeBase = "/specialized-educational-support";

export default function InclusionPeisCreatePage({
    student,
    course,
    semester,
}: {
    student: { id: number; person?: { name: string } | null };
    course: { name: string };
    semester: { label: string };
}): React.JSX.Element {
    const busy = useRef(false);
    const form = useForm({});
    const studentName = student.person?.name ?? "";
    const studentHref = `${aeeBase}/students/${student.id}/show`;
    const peisHref = `${aeeBase}/students/${student.id}/peis`;

    function submit(event: React.FormEvent<HTMLFormElement>): void {
        event.preventDefault();
        if (busy.current || form.processing) return;

        busy.current = true;
        form.post(`${aeeBase}/peis/student/${student.id}/store`, {
            preserveScroll: true,
            onNetworkError: () => {
                notify({
                    tone: "error",
                    title: "Falha de conexão",
                    text: "Não foi possível conectar ao servidor. Confira sua conexão e tente novamente.",
                });
                return false;
            },
            onHttpException: () => {
                notify({
                    tone: "error",
                    title: "Não foi possível concluir",
                    text: "Não foi possível gerar o PEI. Tente novamente.",
                });
                return false;
            },
            onFinish: () => {
                busy.current = false;
            },
        });
    }

    return (
        <BusinessPage
            title="Gerar Novo PEI"
            subtitle="Revise as informações abaixo. O sistema criará automaticamente o PEI com base nesses dados."
            breadcrumbItems={[
                { label: "Home", href: "/auth/dashboard" },
                { label: "Alunos", href: `${aeeBase}/students` },
                { label: studentName, href: studentHref },
                { label: "PEIs", href: peisHref },
                { label: "Gerar Novo" },
            ]}
            className="peiCreatePage"
        >
            <FormLayout
                aria-label="Gerar novo PEI"
                aria-busy={form.processing}
                onSubmit={submit}
            >
                <CardBox>
                    <FormSection
                        id="pei-create-information"
                        title="Informações do PEI"
                        variant="inline"
                        iconClass="fa fa-file-text-o"
                    >
                        <dl className="peiCreate__details">
                            <DetailField label="Estudante">
                                <strong>{studentName}</strong>
                            </DetailField>
                            <DetailField label="Curso Vinculado">
                                <strong>{course.name}</strong>
                            </DetailField>
                            <DetailField label="Semestre Vigente">
                                <strong>{semester.label}</strong>
                            </DetailField>
                        </dl>
                    </FormSection>
                </CardBox>
                <FormActions
                    status={form.processing ? "Gerando PEI…" : undefined}
                >
                    <Button
                        href={peisHref}
                        tone="cancel"
                        label="Cancelar"
                        aria-label="Cancelar e voltar aos PEIs do aluno"
                        disabled={form.processing}
                    />
                    <Button
                        type="submit"
                        tone="save"
                        label="Gerar PEI"
                        title="Confirmar e gerar PEI"
                        aria-label="Confirmar e gerar PEI"
                        disabled={form.processing}
                    />
                </FormActions>
            </FormLayout>
        </BusinessPage>
    );
}
