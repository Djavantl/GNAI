import { Link, router, useForm, usePage } from "@inertiajs/react";
import { useEffect, useMemo, useRef, useState } from "react";
import FormField from "@/components/Forms/FormField/FormField";
import FormLayout from "@/components/Forms/FormLayout/FormLayout";
import SelectField from "@/components/Forms/SelectField/SelectField";
import TextInput from "@/components/Forms/TextInput/TextInput";
import TableActions from "@/components/Tables/TableActions/TableActions";
import {
    TableListCard,
    TableListClearButton,
    TableListEmpty,
    TableListFilterActions,
    TableListFooter,
    TableListScroll,
    TableListTable,
    type TableListColumn,
} from "@/components/Tables/TableList/TableList";
import Button from "@/components/UI/Button/Button";
import ConfirmActionModal from "@/components/UI/ConfirmActionModal/ConfirmActionModal";
import InlineAlert from "@/components/UI/InlineAlert/InlineAlert";
import { notify } from "@/components/UI/NotificationToast/notify";
import useAutoListFilters, { compactListFilters } from "@/hooks/useAutoListFilters";
import BackupPage from "./BackupPage";
import BackupStatusLabel from "./BackupStatusLabel";
import type { BackupFilters, BackupPaginator, BackupRecord } from "./Backup.types";
import "./Backup.css";

type BackupUser = { id: number; name: string };
type Props = {
    backups: BackupPaginator;
    users: BackupUser[];
    filters: Partial<BackupFilters>;
    backupDirectory: string;
    can: {
        store: boolean;
        upload: boolean;
        show: boolean;
        download: boolean;
        restore: boolean;
        destroy: boolean;
    };
};

type BackupAction = "restore" | "delete";
const base = "/backup/backups";
const emptyFilters: BackupFilters = { name: "", status: "", user_id: "" };
const statusOptions = [
    { value: "success", label: "Sucesso" },
    { value: "failed", label: "Falha" },
    { value: "archived", label: "Arquivado" },
];
const columns: TableListColumn[] = [
    { id: "file", label: "Arquivo ZIP", width: "27%", minWidth: 120, fixed: "start" },
    { id: "status", label: "Status", width: "12%", minWidth: 72 },
    { id: "size", label: "Tamanho", width: "11%", minWidth: 60 },
    { id: "created", label: "Criado em", width: "18%", minWidth: 100 },
    { id: "user", label: "Responsável", width: "14%", minWidth: 85 },
    { id: "actions", label: "Ações", width: "18%", minWidth: 108, fixed: "end" },
];

export default function BackupIndexPage({
    backups,
    users,
    filters: initialFilters,
    backupDirectory,
    can,
}: Props): React.JSX.Element {
    const pageErrors = usePage().props.errors as Record<string, string | undefined>;
    const fileInput = useRef<HTMLInputElement>(null);
    const pending = useRef(false);
    const [busy, setBusy] = useState(false);
    const [listLoading, setListLoading] = useState(false);
    const [failure, setFailure] = useState("");
    const [action, setAction] = useState<BackupAction | null>(null);
    const [selectedBackup, setSelectedBackup] = useState<BackupRecord | null>(null);
    const restoreForm = useForm({ password: "" });
    const initialValues = useMemo(() => ({ ...emptyFilters, ...initialFilters }), [initialFilters]);
    const {
        values: filterValues,
        update: updateFilter,
        clear: clearFilters,
        submit: submitFilters,
        cancelScheduled,
    } = useAutoListFilters({
        initialValues,
        emptyValues: emptyFilters,
        onSearch: (values) => visit(values),
    });

    useEffect(() => {
        if (!failure) return;
        notify({ tone: "error", title: "Não foi possível concluir", text: failure });
    }, [failure]);

    function visit(values: BackupFilters, page = 1): void {
        if (pending.current) return;
        pending.current = true;
        setBusy(true);
        setListLoading(true);
        setFailure("");
        router.get(base, { ...compactListFilters(values), page }, {
            preserveState: true,
            preserveScroll: true,
            onNetworkError: () => {
                setFailure("Falha de conexão. Confira sua conexão e tente novamente.");
                return false;
            },
            onHttpException: () => {
                setFailure("Não foi possível atualizar os backups. Tente novamente.");
                return false;
            },
            onFinish: () => {
                pending.current = false;
                setBusy(false);
                setListLoading(false);
            },
        });
    }

    function finishAction(): void {
        setAction(null);
        setSelectedBackup(null);
        restoreForm.reset();
        restoreForm.clearErrors();
    }

    function confirmAction(): void {
        if (pending.current || !selectedBackup || !action) return;
        pending.current = true;
        setFailure("");
        if (action === "restore") {
            restoreForm.post(`${base}/${selectedBackup.id}/restore`, {
                preserveScroll: true,
                onNetworkError: () => {
                    setFailure("Falha de conexão. Confira sua conexão e tente novamente.");
                    return false;
                },
                onHttpException: () => {
                    setFailure("Não foi possível restaurar o backup. Tente novamente.");
                    return false;
                },
                onSuccess: finishAction,
                onFinish: () => { pending.current = false; },
            });
            return;
        }

        router.delete(`${base}/${selectedBackup.id}`, {
            preserveScroll: true,
            onNetworkError: () => {
                setFailure("Falha de conexão. Confira sua conexão e tente novamente.");
                return false;
            },
            onHttpException: () => {
                setFailure("Não foi possível excluir o backup. Tente novamente.");
                return false;
            },
            onSuccess: finishAction,
            onFinish: () => { pending.current = false; },
        });
    }

    function upload(file: File): void {
        if (pending.current) return;
        pending.current = true;
        setBusy(true);
        setFailure("");
        router.post(`${base}/upload`, { backup_file: file }, {
            forceFormData: true,
            preserveScroll: true,
            onNetworkError: () => {
                setFailure("Falha de conexão. Confira sua conexão e tente novamente.");
                return false;
            },
            onHttpException: () => {
                setFailure("Não foi possível importar o backup. Tente novamente.");
                return false;
            },
            onFinish: () => {
                pending.current = false;
                setBusy(false);
                if (fileInput.current) fileInput.current.value = "";
            },
        });
    }

    function generate(): void {
        if (pending.current) return;
        pending.current = true;
        setBusy(true);
        setFailure("");
        router.post(`${base}/store`, {}, {
            preserveScroll: true,
            onNetworkError: () => {
                setFailure("Falha de conexão. Confira sua conexão e tente novamente.");
                return false;
            },
            onHttpException: () => {
                setFailure("Não foi possível gerar o backup. Tente novamente.");
                return false;
            },
            onFinish: () => {
                pending.current = false;
                setBusy(false);
            },
        });
    }

    function openAction(target: BackupRecord, nextAction: BackupAction): void {
        cancelScheduled();
        restoreForm.reset();
        restoreForm.clearErrors();
        setFailure("");
        setSelectedBackup(target);
        setAction(nextAction);
    }

    return (
        <BackupPage
            title="Gerenciamento de Backups"
            subtitle="Visualize e administre as cópias de segurança do sistema."
            actions={(
                <div className="backupIndex__headerActions">
                    {can.upload ? (
                        <>
                            <input
                                ref={fileInput}
                                className="backupIndex__fileInput"
                                type="file"
                                accept=".zip,application/zip"
                                aria-label="Selecionar arquivo ZIP para importar backup"
                                disabled={busy}
                                onChange={(event) => {
                                    const file = event.currentTarget.files?.[0];
                                    if (file) upload(file);
                                }}
                            />
                            <Button
                                tone="default"
                                leftIconClass="fa fa-upload"
                                label={busy ? "Processando..." : "Importar Backup"}
                                aria-label={busy ? "Importando backup..." : "Importar backup ZIP"}
                                aria-describedby={pageErrors.backup_file ? "backup-file-error" : undefined}
                                disabled={busy}
                                onClick={() => fileInput.current?.click()}
                            />
                        </>
                    ) : null}
                    {can.store ? (
                        <Button
                            tone="save"
                            leftIconClass="fa fa-database"
                            label={busy ? "Processando..." : "Gerar Novo"}
                            aria-label={busy ? "Gerando backup..." : "Gerar novo backup"}
                            disabled={busy}
                            onClick={generate}
                        />
                    ) : null}
                    {can.upload && pageErrors.backup_file ? (
                        <span id="backup-file-error" className="backupIndex__fileError" role="alert">
                            {pageErrors.backup_file}
                        </span>
                    ) : null}
                </div>
            )}
        >
            <TableListCard aria-label="Histórico de backups" aria-busy={busy || restoreForm.processing} loading={listLoading} loadingLabel="Atualizando histórico de backups">
                <FormLayout onSubmit={(event) => { event.preventDefault(); submitFilters(); }}>
                    <div className="backupIndex__filters">
                        <FormField id="backup-filter-name" label="Arquivo ZIP" error={pageErrors.name}>
                            <TextInput
                                id="backup-filter-name"
                                name="name"
                                value={filterValues.name}
                                maxLength={255}
                                placeholder="Filtrar por nome..."
                                disabled={busy}
                                invalid={!!pageErrors.name}
                                aria-describedby={pageErrors.name ? "backup-filter-name-error" : undefined}
                                onChange={(event) => updateFilter("name", event.target.value)}
                            />
                        </FormField>
                        <FormField id="backup-filter-status" label="Status" error={pageErrors.status}>
                            <SelectField
                                id="backup-filter-status"
                                name="status"
                                label="Status"
                                placeholder="Todos os status"
                                value={filterValues.status}
                                options={statusOptions}
                                disabled={busy}
                                aria-invalid={!!pageErrors.status}
                                aria-describedby={pageErrors.status ? "backup-filter-status-error" : undefined}
                                onChange={(event) => updateFilter("status", event.target.value)}
                            />
                        </FormField>
                        <FormField id="backup-filter-user" label="Responsável" error={pageErrors.user_id}>
                            <SelectField
                                id="backup-filter-user"
                                name="user_id"
                                label="Responsável"
                                placeholder="Todos os responsáveis"
                                value={filterValues.user_id}
                                options={users.map((user) => ({ value: String(user.id), label: user.name }))}
                                disabled={busy}
                                aria-invalid={!!pageErrors.user_id}
                                aria-describedby={pageErrors.user_id ? "backup-filter-user-error" : undefined}
                                onChange={(event) => updateFilter("user_id", event.target.value)}
                            />
                        </FormField>
                        <TableListFilterActions>
                            <TableListClearButton disabled={busy} onClick={clearFilters} />
                        </TableListFilterActions>
                    </div>
                </FormLayout>
                <TableListScroll aria-label="Tabela de backups">
                    <TableListTable
                        className="backupIndex__table"
                        columns={columns}
                        summary={`${backups.from ?? 0}–${backups.to ?? 0} de ${backups.total} registros`}
                    >
                        {backups.data.length ? backups.data.map((backup) => {
                            return (
                                <tr key={backup.id}>
                                    <td>
                                        {can.show ? (
                                            <Link className="backupIndex__fileLink" href={`${base}/${backup.id}`}>
                                                <i className="fa fa-file-archive-o" aria-hidden="true" />
                                                <span>{backup.file_name}</span>
                                            </Link>
                                        ) : (
                                            <span className="backupIndex__fileName">
                                                <i className="fa fa-file-archive-o" aria-hidden="true" />
                                                <span>{backup.file_name}</span>
                                            </span>
                                        )}
                                    </td>
                                    <td><BackupStatusLabel status={backup.status} /></td>
                                    <td>{backup.size}</td>
                                    <td>{backup.created_at_label}</td>
                                    <td>{backup.user?.name ?? "Sistema"}</td>
                                    <td>
                                        <TableActions>
                                            {can.download ? (
                                                <Button
                                                    href={`${base}/${backup.id}/download`}
                                                    download
                                                    size="sm"
                                                    tone="success"
                                                    iconOnly
                                                    leftIconClass="fa fa-download"
                                                    aria-label={`Baixar ${backup.file_name}`}
                                                />
                                            ) : null}
                                            {can.restore && backup.status === "success" ? (
                                                <Button
                                                    size="sm"
                                                    tone="warning"
                                                    iconOnly
                                                    leftIconClass="fa fa-history"
                                                    aria-label={`Restaurar ${backup.file_name}`}
                                                    disabled={busy}
                                                    onClick={() => openAction(backup, "restore")}
                                                />
                                            ) : null}
                                            {can.show ? (
                                                <Button
                                                    href={`${base}/${backup.id}`}
                                                    size="sm"
                                                    tone="dark-blue"
                                                    iconOnly
                                                    leftIconClass="fa fa-eye"
                                                    aria-label={`Visualizar ${backup.file_name}`}
                                                />
                                            ) : null}
                                            {can.destroy ? (
                                                <Button
                                                    size="sm"
                                                    tone="dark-red"
                                                    iconOnly
                                                    leftIconClass="fa fa-trash-o"
                                                    aria-label={`Excluir ${backup.file_name}`}
                                                    disabled={busy}
                                                    onClick={() => openAction(backup, "delete")}
                                                />
                                            ) : null}
                                            {!can.download && !can.restore && !can.show && !can.destroy ? <span>—</span> : null}
                                        </TableActions>
                                    </td>
                                </tr>
                            );
                        }) : (
                            <tr><TableListEmpty colSpan={6}>Nenhum backup encontrado no histórico.</TableListEmpty></tr>
                        )}
                    </TableListTable>
                </TableListScroll>
                <TableListFooter
                    summary={`${backups.total} backup(s)`}
                    currentPage={backups.current_page}
                    pageCount={backups.last_page}
                    disabled={busy}
                    paginationLabel="Paginação de backups"
                    onSelect={(page) => {
                        cancelScheduled();
                        visit(filterValues, page);
                    }}
                />
            </TableListCard>
            <InlineAlert variant="info" className="backupIndex__retention">
                <i className="fa fa-shield" aria-hidden="true" />{" "}
                <strong>Política de Armazenamento.</strong> Os backups são armazenados em{" "}
                <code>storage/app/private/{backupDirectory}</code>. O sistema mantém os 30 registros mais recentes; os excedentes são removidos automaticamente com seus arquivos físicos.
            </InlineAlert>
            <ConfirmActionModal
                open={action !== null}
                title={action === "restore" ? "Restaurar Backup" : "Excluir Backup"}
                message={action === "restore"
                    ? <>O backup <strong>{selectedBackup?.file_name}</strong> será usado para restaurar o banco de dados e os arquivos de mídia, sobrescrevendo o ambiente atual.</>
                    : <>O arquivo <strong>{selectedBackup?.file_name}</strong> será apagado do servidor e o registro removido do histórico.</>}
                details={action === "restore" ? (
                    <div className="backupIndex__restoreDetails">
                        <FormField
                            id="backup-restore-password"
                            label="Confirme sua senha"
                            requiredIndicator
                            error={restoreForm.errors.password}
                        >
                            <TextInput
                                id="backup-restore-password"
                                name="password"
                                type="password"
                                autoComplete="current-password"
                                required
                                value={restoreForm.data.password}
                                invalid={!!restoreForm.errors.password}
                                aria-describedby={restoreForm.errors.password ? "backup-restore-password-error" : undefined}
                                onChange={(event) => {
                                    restoreForm.clearErrors("password");
                                    restoreForm.setData("password", event.target.value);
                                }}
                            />
                        </FormField>
                        <small>Esta confirmação é obrigatória porque a restauração sobrescreve dados do sistema.</small>
                    </div>
                ) : undefined}
                processing={busy || restoreForm.processing}
                confirmLabel={action === "restore" ? "Confirmar restauração" : "Confirmar exclusão"}
                processingLabel={action === "restore" ? "Restaurando backup..." : "Excluindo backup..."}
                confirmTone={action === "restore" ? "warning" : "dark-red"}
                error={failure || undefined}
                onClose={() => { if (!pending.current) finishAction(); }}
                onConfirm={confirmAction}
            />
        </BackupPage>
    );
}
