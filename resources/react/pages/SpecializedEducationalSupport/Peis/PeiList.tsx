import { useMemo, useRef, useState } from "react";
import { router, usePage } from "@inertiajs/react";
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
import { notify } from "@/components/UI/NotificationToast/notify";
import TableLabel from "@/components/UI/TableLabel/TableLabel";
import { aeeBase, dateLabel } from "@/components/Students/Students.utils";
import useAutoListFilters, { compactListFilters } from "@/hooks/useAutoListFilters";
import type {
    PeiListFilters,
    PeiListProps,
    PeiListRecord,
    PeiListVariant,
} from "./PeiList.types";
import "./PeiList.css";

export const peisBase = `${aeeBase}/peis`;
export const studentPeisUrl = (studentId: number): string =>
    `${aeeBase}/students/${studentId}/peis`;

const emptyFilters: PeiListFilters = {
    student_id: "",
    semester_id: "",
    version: "",
    is_finished: "",
};

const allColumns: TableListColumn[] = [
    { id: "student", label: "Estudante", width: "28%", minWidth: 130, fixed: "start" },
    { id: "semester", label: "Semestre", width: "14%", minWidth: 80 },
    { id: "course", label: "Curso", width: "22%", minWidth: 100 },
    { id: "status", label: "Status", width: "12%", minWidth: 80 },
    { id: "version", label: "Versão", width: "10%", minWidth: 64 },
    { id: "actions", label: "Ações", width: "14%", minWidth: 86, fixed: "end" },
];

const studentColumns: TableListColumn[] = [
    { id: "semester", label: "Semestre", width: "24%", minWidth: 120, fixed: "start" },
    { id: "course", label: "Curso", width: "26%", minWidth: 100 },
    { id: "status", label: "Status", width: "13%", minWidth: 80 },
    { id: "current", label: "Atual", width: "10%", minWidth: 64 },
    { id: "version", label: "Versão", width: "11%", minWidth: 64 },
    { id: "actions", label: "Ações", width: "16%", minWidth: 86, fixed: "end" },
];

const statusOptions = [
    { value: "0", label: "Em andamento" },
    { value: "1", label: "Finalizado" },
];

function versionLabel(pei: PeiListRecord): string {
    return `V${pei.version ?? "N/A"}`;
}

export default function PeiList({
    variant,
    studentId,
    peis,
    semesters,
    students = [],
    filters: initialFilters,
    can,
}: PeiListProps & {
    variant: PeiListVariant;
    studentId?: number;
}): React.JSX.Element {
    const isStudent = variant === "student";
    const listUrl = isStudent && studentId ? studentPeisUrl(studentId) : peisBase;
    const columns = isStudent ? studentColumns : allColumns;
    const pageErrors = usePage().props.errors as Record<string, string | undefined>;
    const pending = useRef(false);
    const [busy, setBusy] = useState(false);
    const [listLoading, setListLoading] = useState(false);
    const [peiToDelete, setPeiToDelete] = useState<PeiListRecord | null>(null);
    const initialValues = useMemo(
        () => ({ ...emptyFilters, ...initialFilters }),
        [initialFilters],
    );
    const { values, update, clear, submit, cancelScheduled } = useAutoListFilters({
        initialValues,
        emptyValues: emptyFilters,
        onSearch: (next) => visit(next),
    });

    function fail(text: string): void {
        notify({
            tone: "error",
            title: "Não foi possível concluir",
            text,
        });
    }

    function visit(next: PeiListFilters, page = 1): void {
        if (pending.current) return;

        pending.current = true;
        setBusy(true);
        setListLoading(true);
        router.get(
            listUrl,
            {
                ...compactListFilters(isStudent ? { ...next, student_id: "" } : next),
                page,
            },
            {
                preserveState: true,
                preserveScroll: true,
                onNetworkError: () => {
                    fail("Falha de conexão. Confira sua conexão e tente novamente.");
                    return false;
                },
                onHttpException: () => {
                    fail("Não foi possível atualizar os PEIs. Tente novamente.");
                    return false;
                },
                onFinish: () => {
                    pending.current = false;
                    setBusy(false);
                    setListLoading(false);
                },
            },
        );
    }

    function remove(): void {
        if (pending.current || !peiToDelete) return;

        cancelScheduled();
        pending.current = true;
        setBusy(true);
        router.delete(`${peisBase}/${peiToDelete.id}`, {
            preserveScroll: true,
            onNetworkError: () => {
                fail("Falha de conexão. Confira sua conexão e tente novamente.");
                return false;
            },
            onHttpException: () => {
                fail("Não foi possível excluir o PEI. Tente novamente.");
                return false;
            },
            onSuccess: () => setPeiToDelete(null),
            onFinish: () => {
                pending.current = false;
                setBusy(false);
            },
        });
    }

    function describedBy(name: keyof PeiListFilters, id: string): string | undefined {
        return pageErrors[name] ? `${id}-error` : undefined;
    }

    function peiName(pei: PeiListRecord): string {
        const owner = pei.student?.person?.name;

        return `PEI ${versionLabel(pei)}${owner ? ` de ${owner}` : ""}`;
    }

    return (
        <>
            <TableListCard
                aria-label={isStudent ? "Histórico de PEIs do aluno" : "Listagem geral de PEIs"}
                aria-busy={busy}
                loading={listLoading}
                loadingLabel="Atualizando PEIs"
            >
                <FormLayout
                    onSubmit={(event) => {
                        event.preventDefault();
                        submit();
                    }}
                >
                    <div
                        className={
                            isStudent
                                ? "peiList__filters peiList__filters--student"
                                : "peiList__filters"
                        }
                    >
                        {!isStudent ? (
                            <FormField
                                id="pei-filter-student"
                                label="Estudante"
                                error={pageErrors.student_id}
                            >
                                <SelectField
                                    id="pei-filter-student"
                                    name="student_id"
                                    label="Estudante"
                                    placeholder="Todos"
                                    value={values.student_id}
                                    options={students}
                                    disabled={busy}
                                    aria-invalid={Boolean(pageErrors.student_id)}
                                    aria-describedby={describedBy("student_id", "pei-filter-student")}
                                    onChange={(event) => update("student_id", event.target.value)}
                                />
                            </FormField>
                        ) : null}
                        <FormField
                            id="pei-filter-semester"
                            label="Semestre"
                            error={pageErrors.semester_id}
                        >
                            <SelectField
                                id="pei-filter-semester"
                                name="semester_id"
                                label="Semestre"
                                placeholder="Todos"
                                value={values.semester_id}
                                options={semesters}
                                disabled={busy}
                                aria-invalid={Boolean(pageErrors.semester_id)}
                                aria-describedby={describedBy("semester_id", "pei-filter-semester")}
                                onChange={(event) => update("semester_id", event.target.value)}
                            />
                        </FormField>
                        <FormField
                            id="pei-filter-version"
                            label="Versão"
                            error={pageErrors.version}
                        >
                            <TextInput
                                id="pei-filter-version"
                                name="version"
                                value={values.version}
                                inputMode="numeric"
                                placeholder="Versão..."
                                disabled={busy}
                                invalid={Boolean(pageErrors.version)}
                                aria-describedby={describedBy("version", "pei-filter-version")}
                                onChange={(event) => update("version", event.target.value)}
                            />
                        </FormField>
                        <FormField
                            id="pei-filter-status"
                            label="Status"
                            error={pageErrors.is_finished}
                        >
                            <SelectField
                                id="pei-filter-status"
                                name="is_finished"
                                label="Status"
                                placeholder="Todos"
                                value={values.is_finished}
                                options={statusOptions}
                                disabled={busy}
                                aria-invalid={Boolean(pageErrors.is_finished)}
                                aria-describedby={describedBy("is_finished", "pei-filter-status")}
                                onChange={(event) => update("is_finished", event.target.value)}
                            />
                        </FormField>
                        <TableListFilterActions>
                            <TableListClearButton
                                disabled={busy}
                                onClick={clear}
                            />
                        </TableListFilterActions>
                    </div>
                </FormLayout>
                <TableListScroll aria-label="Tabela de PEIs">
                    <TableListTable
                        columns={columns}
                        page={peis}
                    >
                        {peis.data.length ? (
                            peis.data.map((pei) => (
                                <tr key={pei.id}>
                                    {isStudent ? (
                                        <td>
                                            <div className="peiList__identity">
                                                <strong>{pei.semester?.label || "N/A"}</strong>
                                                <small className="peiList__meta">
                                                    Criado em: {dateLabel(pei.created_at)}
                                                </small>
                                            </div>
                                        </td>
                                    ) : (
                                        <>
                                            <td>
                                                <div className="peiList__identity">
                                                    <strong>{pei.student?.person?.name || "—"}</strong>
                                                    <small className="peiList__meta">
                                                        Matrícula: {pei.student?.registration || "—"}
                                                    </small>
                                                </div>
                                            </td>
                                            <td>
                                                <strong>{pei.semester?.label || "N/A"}</strong>
                                            </td>
                                        </>
                                    )}
                                    <td>
                                        <strong>{pei.course?.name || "N/A"}</strong>
                                    </td>
                                    <td>
                                        <TableLabel tone={pei.is_finished ? "success" : "warning"}>
                                            {pei.is_finished ? "Finalizado" : "Em aberto"}
                                        </TableLabel>
                                    </td>
                                    {isStudent ? (
                                        <td>
                                            <TableLabel tone={pei.is_current ? "success" : "inverse"}>
                                                {pei.is_current ? "Sim" : "Não"}
                                            </TableLabel>
                                        </td>
                                    ) : null}
                                    <td>
                                        <strong>{versionLabel(pei)}</strong>
                                    </td>
                                    <td>
                                        <TableActions>
                                            {can.view ? (
                                                <Button
                                                    href={`${peisBase}/${pei.id}/show`}
                                                    size="sm"
                                                    tone="dark-blue"
                                                    iconOnly
                                                    leftIconClass="fa fa-eye"
                                                    aria-label={`Visualizar ${peiName(pei)}`}
                                                />
                                            ) : null}
                                            {can.delete ? (
                                                <Button
                                                    tone="dark-red"
                                                    size="sm"
                                                    iconOnly
                                                    leftIconClass="fa fa-trash-o"
                                                    aria-label={`Excluir ${peiName(pei)}`}
                                                    disabled={busy}
                                                    onClick={() => {
                                                        cancelScheduled();
                                                        setPeiToDelete(pei);
                                                    }}
                                                />
                                            ) : null}
                                            {!can.view && !can.delete ? (
                                                <span>Nenhuma ação</span>
                                            ) : null}
                                        </TableActions>
                                    </td>
                                </tr>
                            ))
                        ) : (
                            <tr>
                                <TableListEmpty colSpan={columns.length}>
                                    {isStudent
                                        ? "Nenhum PEI do aluno encontrado no sistema."
                                        : "Nenhum PEI encontrado no sistema."}
                                </TableListEmpty>
                            </tr>
                        )}
                    </TableListTable>
                </TableListScroll>
                <TableListFooter
                    page={peis}
                    disabled={busy}
                    paginationLabel="Paginação de PEIs"
                    onSelect={(page) => visit(values, page)}
                />
            </TableListCard>
            {can.delete ? (
                <ConfirmActionModal
                    open={peiToDelete !== null}
                    title="Excluir PEI"
                    message="Deseja remover este PEI?"
                    details={peiToDelete ? <strong>{peiName(peiToDelete)}</strong> : undefined}
                    processing={busy}
                    confirmLabel="Confirmar exclusão"
                    processingLabel="Excluindo PEI..."
                    confirmTone="dark-red"
                    onClose={() => {
                        if (!busy) setPeiToDelete(null);
                    }}
                    onConfirm={remove}
                />
            ) : null}
        </>
    );
}
