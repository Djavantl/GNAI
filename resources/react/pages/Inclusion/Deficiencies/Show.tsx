import { router } from "@inertiajs/react";
import { useEffect, useState } from "react";
import FormActions from "@/components/Forms/FormActions/FormActions";
import FormSection from "@/components/Forms/FormSection/FormSection";
import Button from "@/components/UI/Button/Button";
import CardBox from "@/components/UI/CardBox/CardBox";
import ConfirmActionModal from "@/components/UI/ConfirmActionModal/ConfirmActionModal";
import DetailField from "@/components/UI/DetailField/DetailField";
import { notify } from "@/components/UI/NotificationToast/notify";
import TableLabel from "@/components/UI/TableLabel/TableLabel";
import DeficiencyPage, { deficiencyBase } from "./DeficiencyPage";
import type { DeficiencyRecord } from "./Deficiency.types";
import "./Deficiencies.css";

export default function InclusionDeficienciesShowPage({
    deficiency,
    can,
}: {
    deficiency: DeficiencyRecord;
    can: { update: boolean; delete: boolean };
}): React.JSX.Element {
    const [deleteOpen, setDeleteOpen] = useState(false);
    const [deleting, setDeleting] = useState(false);
    const [failure, setFailure] = useState("");
    const itemUrl = `${deficiencyBase}/${deficiency.id}`;

    useEffect(() => {
        if (failure) {
            notify({ tone: "error", title: "Não foi possível concluir", text: failure });
        }
    }, [failure]);

    function remove(): void {
        if (deleting) return;
        setFailure("");
        setDeleting(true);
        router.delete(itemUrl, {
            preserveScroll: true,
            onNetworkError: () => {
                setFailure("Falha de conexão. Confira sua conexão e tente novamente.");
                return false;
            },
            onHttpException: () => {
                setFailure("Não foi possível excluir o perfil de atendimento. Tente novamente.");
                return false;
            },
            onSuccess: () => setDeleteOpen(false),
            onFinish: () => setDeleting(false),
        });
    }

    return (
        <DeficiencyPage
            title="Detalhes do Perfil"
            subtitle="Informações sobre o perfil para acompanhamento e encaminhamentos pedagógicos."
            deficiency={deficiency}
        >
            <CardBox aria-busy={deleting}>
                <FormSection
                    id="deficiency-show-identification"
                    title="Identificação Clínica"
                    variant="inline"
                    iconClass="fa fa-id-card-o"
                >
                    <dl className="deficiencyShow__grid">
                        <DetailField label="Nome do Perfil">
                            <strong>{deficiency.name}</strong>
                        </DetailField>
                        <DetailField label="Código CID">
                            {deficiency.cid_code || "Não informado"}
                        </DetailField>
                        <DetailField label="Status no Sistema">
                            <TableLabel tone={deficiency.is_active ? "success" : "inverse"}>
                                {deficiency.is_active ? "Ativa" : "Inativa"}
                            </TableLabel>
                        </DetailField>
                    </dl>
                </FormSection>
                <FormSection
                    id="deficiency-show-description"
                    title="Descrição e Observações Técnicas"
                    variant="inline"
                    iconClass="fa fa-file-text-o"
                >
                    <dl className="deficiencyShow__grid">
                        <DetailField
                            label="Descrição Detalhada"
                            wide
                        >
                            <span className="deficiencyShow__text">
                                {deficiency.description
                                    || "Nenhuma descrição técnica foi cadastrada para este perfil."}
                            </span>
                        </DetailField>
                    </dl>
                </FormSection>
            </CardBox>
            <FormActions>
                <Button
                    href={deficiencyBase}
                    tone="cancel"
                    label="Voltar"
                    title="Voltar para perfis de atendimento"
                    aria-label="Voltar para perfis de atendimento"
                    disabled={deleting}
                />
                {can.update ? (
                    <Button
                        href={`${itemUrl}/edit`}
                        tone="warning"
                        leftIconClass="fa fa-pencil"
                        label="Editar"
                        title="Editar perfil de atendimento"
                        aria-label="Editar perfil de atendimento"
                        disabled={deleting}
                    />
                ) : null}
                {can.delete ? (
                    <Button
                        tone="dark-red"
                        leftIconClass="fa fa-trash-o"
                        label="Excluir"
                        title="Excluir perfil de atendimento"
                        aria-label="Excluir perfil de atendimento"
                        disabled={deleting}
                        onClick={() => {
                            setFailure("");
                            setDeleteOpen(true);
                        }}
                    />
                ) : null}
            </FormActions>
            <ConfirmActionModal
                open={deleteOpen}
                title="Excluir Perfil de Atendimento"
                message={
                    <>
                        Excluir o perfil <strong>{deficiency.name}</strong> pode afetar alunos vinculados a este registro.
                    </>
                }
                processing={deleting}
                confirmLabel="Confirmar exclusão"
                processingLabel="Excluindo perfil..."
                confirmTone="dark-red"
                onClose={() => {
                    if (deleting) return;
                    setFailure("");
                    setDeleteOpen(false);
                }}
                onConfirm={remove}
            />
        </DeficiencyPage>
    );
}
