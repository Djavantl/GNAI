import { router } from "@inertiajs/react";
import { useEffect, useState } from "react";
import Button from "@/components/UI/Button/Button";
import CardBox from "@/components/UI/CardBox/CardBox";
import FormActions from "@/components/Forms/FormActions/FormActions";
import FormSection from "@/components/Forms/FormSection/FormSection";
import ConfirmActionModal from "@/components/UI/ConfirmActionModal/ConfirmActionModal";
import DetailField from "@/components/UI/DetailField/DetailField";
import { notify } from "@/components/UI/NotificationToast/notify";
import BackupPage from "./BackupPage";
import BackupStatusLabel from "./BackupStatusLabel";
import type { BackupRecord } from "./Backup.types";
import "./Backup.css";

const base = "/backup/backups";

export default function BackupShowPage({
    backup,
    can,
}: {
    backup: BackupRecord;
    can: { download: boolean; destroy: boolean };
}): React.JSX.Element {
    const [deleteOpen, setDeleteOpen] = useState(false);
    const [deleting, setDeleting] = useState(false);
    const [failure, setFailure] = useState("");

    useEffect(() => {
        if (!failure) return;
        notify({ tone: "error", title: "Não foi possível concluir", text: failure });
    }, [failure]);

    function remove(): void {
        if (deleting) return;
        setFailure("");
        setDeleting(true);
        router.delete(`${base}/${backup.id}`, {
            preserveScroll: true,
            onNetworkError: () => {
                setFailure("Falha de conexão. Confira sua conexão e tente novamente.");
                return false;
            },
            onHttpException: () => {
                setFailure("Não foi possível excluir o backup. Tente novamente.");
                return false;
            },
            onSuccess: () => setDeleteOpen(false),
            onFinish: () => setDeleting(false),
        });
    }

    return (
        <BackupPage
            title="Detalhes do Registro de Backup"
            subtitle="Informações técnicas e metadados da cópia de segurança."
            currentFile={backup.file_name}
        >
            <CardBox aria-busy={deleting}>
                <FormSection
                    id="backup-file-identification"
                    title="Identificação do Arquivo Físico"
                    variant="inline"
                    iconClass="fa fa-file-archive-o"
                >
                    <dl className="backupShow__grid">
                        <DetailField label="Arquivo ZIP" wide>
                            <strong>{backup.file_name}</strong>
                        </DetailField>
                        <DetailField label="Caminho no Servidor" wide>
                            <code>{backup.file_path}</code>
                        </DetailField>
                    </dl>
                </FormSection>
                <FormSection
                    id="backup-display-metadata"
                    title="Metadados para Exibição"
                    variant="inline"
                    iconClass="fa fa-info-circle"
                >
                    <dl className="backupShow__grid">
                        <DetailField label="Nome de Exibição (Alias)" wide>
                            {backup.file_name}
                        </DetailField>
                        <DetailField label="Tamanho do Arquivo">
                            {backup.size}
                        </DetailField>
                        <DetailField label="Data de Geração">
                            {backup.created_at_label}
                        </DetailField>
                        <DetailField label="Responsável">
                            {backup.user?.name ?? "Sistema"}
                        </DetailField>
                    </dl>
                </FormSection>
                <FormSection
                    id="backup-governance"
                    title="Status e Governança"
                    variant="inline"
                    iconClass="fa fa-shield"
                >
                    <dl className="backupShow__grid">
                        <DetailField label="Status do Backup">
                            <BackupStatusLabel status={backup.status} />
                        </DetailField>
                        <DetailField label="ID do Registro">#{backup.id}</DetailField>
                        <DetailField label="Regra de Retenção" wide>
                            O sistema mantém os 30 registros de backup mais recentes. Registros excedentes podem ser removidos automaticamente com seus arquivos físicos.
                        </DetailField>
                    </dl>
                </FormSection>
            </CardBox>
            <FormActions
                secondary={can.download ? (
                    <Button
                        href={`${base}/${backup.id}/download`}
                        download
                        tone="success"
                        leftIconClass="fa fa-download"
                        label="Baixar Backup"
                        aria-label="Baixar backup"
                        disabled={deleting}
                    />
                ) : undefined}
            >
                <Button
                    href={base}
                    tone="cancel"
                    label="Voltar"
                    aria-label="Voltar para backups"
                    disabled={deleting}
                />
                {can.destroy ? (
                    <Button
                        tone="dark-red"
                        leftIconClass="fa fa-trash-o"
                        label="Excluir"
                        aria-label="Excluir backup"
                        disabled={deleting}
                        onClick={() => setDeleteOpen(true)}
                    />
                ) : null}
            </FormActions>
            <ConfirmActionModal
                open={deleteOpen}
                title="Excluir Backup"
                message={<>O arquivo <strong>{backup.file_name}</strong> será apagado do servidor e o registro removido do histórico.</>}
                processing={deleting}
                confirmLabel="Confirmar exclusão"
                processingLabel="Excluindo backup..."
                confirmTone="dark-red"
                error={failure || undefined}
                onClose={() => { if (!deleting) { setDeleteOpen(false); setFailure(""); } }}
                onConfirm={remove}
            />
        </BackupPage>
    );
}
