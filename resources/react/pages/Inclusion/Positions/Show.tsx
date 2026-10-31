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
import PositionPage, { positionsBase } from "./PositionPage";
import type { PositionDetailRecord } from "./Position.types";

export default function InclusionPositionsShowPage({
    position,
    can,
}: {
    position: PositionDetailRecord;
    can: { update: boolean; delete: boolean };
}): React.JSX.Element {
    const [deleteOpen, setDeleteOpen] = useState(false);
    const [deleting, setDeleting] = useState(false);
    const [failure, setFailure] = useState("");
    const permissions = position.permissions ?? [];
    const itemUrl = `${positionsBase}/${position.id}`;

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
                setFailure("Não foi possível excluir o cargo. Tente novamente.");
                return false;
            },
            onSuccess: () => setDeleteOpen(false),
            onFinish: () => setDeleting(false),
        });
    }

    return (
        <PositionPage
            title="Detalhes do Cargo"
            subtitle="Gerenciamento de funções e atribuições do suporte especializado."
            position={position}
        >
            <CardBox aria-busy={deleting}>
                <FormSection
                    id="position-show-information"
                    title="Informações do Cargo"
                    variant="inline"
                    iconClass="fa fa-briefcase"
                >
                    <dl className="positionShow__grid">
                        <DetailField label="Nome do Cargo / Função">
                            <strong>{position.name}</strong>
                        </DetailField>
                        <DetailField label="Status de Disponibilidade">
                            <TableLabel tone={position.is_active ? "success" : "inverse"}>
                                <i
                                    className={position.is_active ? "fa fa-check-circle" : "fa fa-ban"}
                                    aria-hidden="true"
                                />
                                {" "}
                                {position.is_active ? "Ativo" : "Desativado"}
                            </TableLabel>
                        </DetailField>
                    </dl>
                </FormSection>
                <FormSection
                    id="position-show-description"
                    title="Atribuições e Descrição"
                    variant="inline"
                    iconClass="fa fa-file-text-o"
                >
                    <dl className="positionShow__grid">
                        <DetailField
                            label="Descrição da Função"
                            wide
                        >
                            <span className="positionShow__text">
                                {position.description
                                    || "Nenhuma descrição detalhada foi fornecida para este cargo."}
                            </span>
                        </DetailField>
                    </dl>
                </FormSection>
                <FormSection
                    id="position-show-permissions"
                    title="Permissões Vinculadas ao Cargo"
                    variant="inline"
                    iconClass="fa fa-key"
                >
                    <dl className="positionShow__grid">
                        <DetailField
                            label="Permissões Vinculadas"
                            wide
                        >
                            {permissions.length ? (
                                <ul className="positionShow__tags">
                                    {permissions.map((permission) => (
                                        <li key={permission.id}>{permission.name}</li>
                                    ))}
                                </ul>
                            ) : (
                                "---"
                            )}
                        </DetailField>
                    </dl>
                </FormSection>
            </CardBox>
            <FormActions>
                <Button
                    href={positionsBase}
                    tone="cancel"
                    label="Voltar"
                    title="Voltar para cargos"
                    aria-label="Voltar para cargos"
                    disabled={deleting}
                />
                {can.update ? (
                    <Button
                        href={`${itemUrl}/edit`}
                        tone="warning"
                        leftIconClass="fa fa-pencil"
                        label="Editar"
                        title="Editar cargo"
                        aria-label="Editar cargo"
                        disabled={deleting}
                    />
                ) : null}
                {can.delete ? (
                    <Button
                        tone="dark-red"
                        leftIconClass="fa fa-trash-o"
                        label="Excluir"
                        title="Excluir cargo"
                        aria-label="Excluir cargo"
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
                title="Excluir Cargo"
                message={
                    <>
                        Excluir o cargo <strong>{position.name}</strong> pode afetar profissionais vinculados.
                    </>
                }
                processing={deleting}
                confirmLabel="Confirmar exclusão"
                processingLabel="Excluindo cargo..."
                confirmTone="dark-red"
                onClose={() => {
                    if (deleting) return;
                    setFailure("");
                    setDeleteOpen(false);
                }}
                onConfirm={remove}
            />
        </PositionPage>
    );
}
