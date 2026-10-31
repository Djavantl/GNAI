import { useState } from "react";
import { router } from "@inertiajs/react";
import FormActions from "@/components/Forms/FormActions/FormActions";
import FormSection from "@/components/Forms/FormSection/FormSection";
import BusinessPage from "@/components/Navigation/BusinessPage/BusinessPage";
import Button from "@/components/UI/Button/Button";
import CardBox from "@/components/UI/CardBox/CardBox";
import ConfirmActionModal from "@/components/UI/ConfirmActionModal/ConfirmActionModal";
import DetailField from "@/components/UI/DetailField/DetailField";
import { notify } from "@/components/UI/NotificationToast/notify";
import TableLabel from "@/components/UI/TableLabel/TableLabel";
import "./Show.css";

type PendencyAction = "complete" | "delete";

interface PendencyShowProps {
    pendency: {
        id: number;
        title: string;
        description: string | null;
        priority_label: string | null;
        priority_color: string | null;
        due_date: string;
        is_completed: boolean;
        assigned_name: string;
        creator_name: string;
        created_at: string;
        updated_at: string;
    };
    can: {
        edit: boolean;
        complete: boolean;
        delete: boolean;
    };
}

const pendenciesBase = "/specialized-educational-support/pendencies";

const priorityTones: Record<string, string> = {
    info: "info",
    warning: "warning",
    danger: "danger",
    dark: "inverse",
};

function fail(text: string): void {
    notify({
        tone: "error",
        title: "Não foi possível concluir",
        text,
    });
}

export default function InclusionPendenciesShowPage({
    pendency,
    can,
}: PendencyShowProps): React.JSX.Element {
    const pendencyUrl = `${pendenciesBase}/${pendency.id}`;
    const [action, setAction] = useState<PendencyAction | null>(null);
    const [processing, setProcessing] = useState(false);

    function closeModal(): void {
        if (!processing) setAction(null);
    }

    function submit(): void {
        if (processing || !action) return;

        const failure = action === "complete"
            ? "Não foi possível concluir a pendência. Tente novamente."
            : "Não foi possível excluir a pendência. Tente novamente.";
        const options = {
            preserveScroll: true,
            onStart: () => setProcessing(true),
            onNetworkError: () => {
                fail("Falha de conexão. Confira sua conexão e tente novamente.");
                return false as const;
            },
            onHttpException: () => {
                fail(failure);
                return false as const;
            },
            onSuccess: () => setAction(null),
            onFinish: () => setProcessing(false),
        };

        if (action === "complete") {
            router.put(`${pendencyUrl}/complete`, {}, options);
            return;
        }

        router.delete(pendencyUrl, options);
    }

    return (
        <BusinessPage
            title={`Pendência, ${pendency.title}`}
            subtitle="Detalhes completos da pendência."
            breadcrumbItems={[
                { label: "Home", href: "/auth/dashboard" },
                { label: "Pendências", href: pendenciesBase },
                { label: pendency.title },
            ]}
            className="pendencyShow"
        >
            <div className="pendencyShow__content" aria-busy={processing}>
                <CardBox className="pendencyShow__panel">
                    <FormSection
                        id="pendency-data"
                        title="Dados da Pendência"
                        variant="inline"
                        iconClass="fa fa-tasks"
                    >
                        <dl className="pendencyShow__grid pendencyShow__grid--data">
                            <DetailField label="Título">
                                <strong>{pendency.title}</strong>
                            </DetailField>
                            <DetailField label="Profissional Responsável">
                                {pendency.assigned_name}
                            </DetailField>
                            <DetailField label="Prioridade">
                                {pendency.priority_label ? (
                                    <TableLabel
                                        tone={priorityTones[pendency.priority_color ?? ""] ?? "inverse"}
                                    >
                                        {pendency.priority_label}
                                    </TableLabel>
                                ) : (
                                    "Não informado"
                                )}
                            </DetailField>
                            <DetailField label="Vencimento">
                                {pendency.due_date}
                            </DetailField>
                            <DetailField label="Concluída">
                                <TableLabel tone={pendency.is_completed ? "success" : "danger"}>
                                    <i
                                        className={pendency.is_completed ? "fa fa-check-circle" : "fa fa-times-circle"}
                                        aria-hidden="true"
                                    />
                                    {" "}
                                    {pendency.is_completed ? "Sim" : "Não"}
                                </TableLabel>
                            </DetailField>
                            <DetailField label="Descrição" wide>
                                <span className="pendencyShow__text">
                                    {pendency.description || "Não informado"}
                                </span>
                            </DetailField>
                        </dl>
                    </FormSection>
                    <FormSection
                        id="pendency-system"
                        title="Informações do Sistema"
                        variant="inline"
                        iconClass="fa fa-info-circle"
                    >
                        <dl className="pendencyShow__grid">
                            <DetailField label="Criado por">
                                {pendency.creator_name}
                            </DetailField>
                            <DetailField label="Criado em">
                                {pendency.created_at}
                            </DetailField>
                            <DetailField label="Última atualização">
                                {pendency.updated_at}
                            </DetailField>
                        </dl>
                    </FormSection>
                </CardBox>
                <FormActions>
                    <Button
                        href={pendenciesBase}
                        tone="cancel"
                        label="Voltar"
                        title="Voltar para pendências"
                        aria-label="Voltar para pendências"
                        disabled={processing}
                    />
                    {can.edit ? (
                        <Button
                            href={`${pendencyUrl}/edit`}
                            tone="warning"
                            leftIconClass="fa fa-pencil"
                            label="Editar"
                            title="Editar pendência"
                            aria-label="Editar pendência"
                        />
                    ) : null}
                    {can.complete ? (
                        <Button
                            tone="success"
                            leftIconClass="fa fa-check"
                            label="Marcar como Concluída"
                            title="Marcar pendência como concluída"
                            aria-label="Marcar pendência como concluída"
                            disabled={processing}
                            onClick={() => setAction("complete")}
                        />
                    ) : null}
                    {can.delete ? (
                        <Button
                            tone="dark-red"
                            leftIconClass="fa fa-trash-o"
                            label="Excluir"
                            title="Excluir pendência"
                            aria-label="Excluir pendência"
                            disabled={processing}
                            onClick={() => setAction("delete")}
                        />
                    ) : null}
                </FormActions>
            </div>
            <ConfirmActionModal
                open={action === "complete"}
                title="Concluir Pendência"
                message="Marcar como concluída?"
                processing={processing}
                confirmLabel="Confirmar conclusão"
                processingLabel="Concluindo pendência..."
                confirmTone="success"
                onClose={closeModal}
                onConfirm={submit}
            />
            <ConfirmActionModal
                open={action === "delete"}
                title="Excluir Pendência"
                message={
                    <>
                        Deseja excluir a pendência <strong>{pendency.title}</strong>?
                    </>
                }
                processing={processing}
                confirmLabel="Confirmar exclusão"
                processingLabel="Excluindo pendência..."
                confirmTone="dark-red"
                onClose={closeModal}
                onConfirm={submit}
            />
        </BusinessPage>
    );
}
