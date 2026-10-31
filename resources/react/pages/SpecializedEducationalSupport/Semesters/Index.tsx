import { router, usePage } from "@inertiajs/react";
import { useEffect, useMemo, useRef, useState } from "react";
import FormField from "@/components/Forms/FormField/FormField";
import FormLayout from "@/components/Forms/FormLayout/FormLayout";
import SelectField from "@/components/Forms/SelectField/SelectField";
import TextInput from "@/components/Forms/TextInput/TextInput";
import TableActions from "@/components/Tables/TableActions/TableActions";
import {
    TableListCard,
    TableListClearButton,
    TableListCreateButton,
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
import TableLabel from "@/components/UI/TableLabel/TableLabel";
import useAutoListFilters, { compactListFilters } from "@/hooks/useAutoListFilters";
import SemesterPage, { semesterBase, semesterLabel } from "./SemesterPage";
import type { SemesterFilters, SemesterPaginator, SemesterRecord } from "./Semester.types";
import "./Semesters.css";

type Props = {
    semesters: SemesterPaginator;
    filters: Partial<SemesterFilters>;
    can: { create: boolean; view: boolean; update: boolean; delete: boolean };
};

const emptyFilters: SemesterFilters = { year: "", term: "", label: "", is_current: "" };
const termOptions = [
    { value: "1", label: "1º semestre" },
    { value: "2", label: "2º semestre" },
];
const currentOptions = [
    { value: "1", label: "Atual" },
    { value: "0", label: "Não atual" },
];
const columns: TableListColumn[] = [
    { id: "period", label: "Ano / Período", width: "28%", minWidth: 130, fixed: "start" },
    { id: "label", label: "Rótulo Identificador", width: "28%", minWidth: 110 },
    { id: "status", label: "Status", width: "22%", minWidth: 100 },
    { id: "actions", label: "Ações", width: "22%", minWidth: 120, fixed: "end" },
];
const connectionFailure = "Falha de conexão. Confira sua conexão e tente novamente.";

export default function SpecializedEducationalSupportSemestersIndexPage({
    semesters,
    filters: initialFilters,
    can,
}: Props): React.JSX.Element {
    const pageErrors = usePage().props.errors as Record<string, string | undefined>;
    const pending = useRef(false);
    const [busy, setBusy] = useState(false);
    const [listLoading, setListLoading] = useState(false);
    const [failure, setFailure] = useState("");
    const [semesterToDelete, setSemesterToDelete] = useState<SemesterRecord | null>(null);
    const initialValues = useMemo(
        () => ({ ...emptyFilters, ...initialFilters }),
        [initialFilters],
    );
    const { values, update, clear, submit, cancelScheduled } = useAutoListFilters({
        initialValues,
        emptyValues: emptyFilters,
        onSearch: (filters) => visit(filters),
    });

    useEffect(() => {
        if (failure) {
            notify({ tone: "error", title: "Não foi possível concluir", text: failure });
        }
    }, [failure]);

    function visit(filters: SemesterFilters, page = 1): void {
        if (pending.current) return;
        pending.current = true;
        setBusy(true);
        setListLoading(true);
        setFailure("");
        router.get(semesterBase, { ...compactListFilters(filters), page }, {
            preserveState: true,
            preserveScroll: true,
            onNetworkError: () => {
                setFailure(connectionFailure);
                return false;
            },
            onHttpException: () => {
                setFailure("Não foi possível atualizar os semestres. Tente novamente.");
                return false;
            },
            onFinish: () => {
                pending.current = false;
                setBusy(false);
                setListLoading(false);
            },
        });
    }

    function setCurrent(semester: SemesterRecord): void {
        if (pending.current) return;
        cancelScheduled();
        pending.current = true;
        setBusy(true);
        setFailure("");
        router.patch(`${semesterBase}/${semester.id}/set-current`, {}, {
            preserveScroll: true,
            onNetworkError: () => {
                setFailure(connectionFailure);
                return false;
            },
            onHttpException: () => {
                setFailure("Não foi possível definir o semestre atual. Tente novamente.");
                return false;
            },
            onFinish: () => {
                pending.current = false;
                setBusy(false);
            },
        });
    }

    function remove(): void {
        if (pending.current || !semesterToDelete) return;
        cancelScheduled();
        pending.current = true;
        setBusy(true);
        setFailure("");
        router.delete(`${semesterBase}/${semesterToDelete.id}`, {
            preserveScroll: true,
            onNetworkError: () => {
                setFailure(connectionFailure);
                return false;
            },
            onHttpException: () => {
                setFailure("Não foi possível excluir o semestre. Tente novamente.");
                return false;
            },
            onSuccess: () => setSemesterToDelete(null),
            onFinish: () => {
                pending.current = false;
                setBusy(false);
            },
        });
    }

    return (
        <SemesterPage
            title="Semestres Letivos"
            subtitle="Configuração de períodos para organização dos atendimentos e relatórios."
            actions={can.create ? (
                <TableListCreateButton
                    href={`${semesterBase}/create`}
                    ariaLabel="Cadastrar semestre"
                />
            ) : undefined}
        >
            <TableListCard
                aria-label="Listagem de semestres"
                aria-busy={busy}
                loading={listLoading}
                loadingLabel="Atualizando semestres"
            >
                <FormLayout
                    onSubmit={(event) => {
                        event.preventDefault();
                        submit();
                    }}
                >
                    <div className="semesterIndex__filters">
                        <FormField
                            id="semester-filter-year"
                            label="Ano"
                            error={pageErrors.year}
                        >
                            <TextInput
                                id="semester-filter-year"
                                name="year"
                                value={values.year}
                                inputMode="numeric"
                                maxLength={4}
                                placeholder="Ano..."
                                disabled={busy}
                                invalid={!!pageErrors.year}
                                aria-describedby={pageErrors.year ? "semester-filter-year-error" : undefined}
                                onChange={(event) => update("year", event.target.value)}
                            />
                        </FormField>
                        <FormField
                            id="semester-filter-term"
                            label="Período"
                            error={pageErrors.term}
                        >
                            <SelectField
                                id="semester-filter-term"
                                name="term"
                                label="Período"
                                placeholder="Todos"
                                value={values.term}
                                options={termOptions}
                                disabled={busy}
                                aria-invalid={!!pageErrors.term}
                                aria-describedby={pageErrors.term ? "semester-filter-term-error" : undefined}
                                onChange={(event) => update("term", event.target.value)}
                            />
                        </FormField>
                        <FormField
                            id="semester-filter-label"
                            label="Rótulo"
                            error={pageErrors.label}
                        >
                            <TextInput
                                id="semester-filter-label"
                                name="label"
                                value={values.label}
                                maxLength={255}
                                placeholder="Rótulo..."
                                disabled={busy}
                                invalid={!!pageErrors.label}
                                aria-describedby={pageErrors.label ? "semester-filter-label-error" : undefined}
                                onChange={(event) => update("label", event.target.value)}
                            />
                        </FormField>
                        <FormField
                            id="semester-filter-current"
                            label="Status"
                            error={pageErrors.is_current}
                        >
                            <SelectField
                                id="semester-filter-current"
                                name="is_current"
                                label="Status"
                                placeholder="Todos"
                                value={values.is_current}
                                options={currentOptions}
                                disabled={busy}
                                aria-invalid={!!pageErrors.is_current}
                                aria-describedby={pageErrors.is_current ? "semester-filter-current-error" : undefined}
                                onChange={(event) => update("is_current", event.target.value)}
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
                <TableListScroll aria-label="Tabela de semestres">
                    <TableListTable
                        className="semesterIndex__table"
                        columns={columns}
                        page={semesters}
                    >
                        {semesters.data.length ? semesters.data.map((semester) => {
                            const name = semesterLabel(semester);
                            const canSetCurrent = can.update && !semester.is_current;
                            const canDelete = can.delete && !semester.is_current;

                            return (
                                <tr
                                    key={semester.id}
                                    className={semester.is_current ? "semesterIndex__row--current" : undefined}
                                >
                                    <td>
                                        <span className="semesterIndex__period">
                                            <strong>{semester.year}</strong>
                                            <small>{semester.term}º Período</small>
                                        </span>
                                    </td>
                                    <td>
                                        <span className="semesterIndex__label">{semester.label || "—"}</span>
                                    </td>
                                    <td>
                                        <TableLabel tone={semester.is_current ? "success" : "inverse"}>
                                            {semester.is_current ? "Semestre atual" : "Histórico"}
                                        </TableLabel>
                                    </td>
                                    <td>
                                        <TableActions>
                                            {can.view ? (
                                                <Button
                                                    href={`${semesterBase}/${semester.id}/show`}
                                                    size="sm"
                                                    tone="dark-blue"
                                                    iconOnly
                                                    leftIconClass="fa fa-eye"
                                                    aria-label={`Visualizar semestre ${name}`}
                                                />
                                            ) : null}
                                            {canSetCurrent ? (
                                                <Button
                                                    size="sm"
                                                    tone="success"
                                                    iconOnly
                                                    leftIconClass="fa fa-check"
                                                    aria-label={`Definir semestre ${name} como atual`}
                                                    disabled={busy}
                                                    onClick={() => setCurrent(semester)}
                                                />
                                            ) : null}
                                            {canDelete ? (
                                                <Button
                                                    tone="dark-red"
                                                    size="sm"
                                                    iconOnly
                                                    leftIconClass="fa fa-trash-o"
                                                    aria-label={`Excluir semestre ${name}`}
                                                    disabled={busy}
                                                    onClick={() => {
                                                        cancelScheduled();
                                                        setFailure("");
                                                        setSemesterToDelete(semester);
                                                    }}
                                                />
                                            ) : null}
                                            {!can.view && !canSetCurrent && !canDelete ? <span>—</span> : null}
                                        </TableActions>
                                    </td>
                                </tr>
                            );
                        }) : (
                            <tr>
                                <TableListEmpty colSpan={columns.length}>
                                    Nenhum semestre encontrado.
                                </TableListEmpty>
                            </tr>
                        )}
                    </TableListTable>
                </TableListScroll>
                <TableListFooter
                    page={semesters}
                    disabled={busy}
                    paginationLabel="Paginação de semestres"
                    onSelect={(page) => visit(values, page)}
                />
            </TableListCard>
            <InlineAlert
                variant="info"
                className="semesterIndex__info"
            >
                <i className="fa fa-info-circle" aria-hidden="true" />{" "}
                O <strong>Semestre Atual</strong> determina qual período será selecionado por padrão em novos registros do sistema.
            </InlineAlert>
            <ConfirmActionModal
                open={semesterToDelete !== null}
                title="Excluir Semestre"
                message={(
                    <>
                        Tem certeza que deseja excluir o semestre{" "}
                        <strong>{semesterToDelete ? semesterLabel(semesterToDelete) : ""}</strong>?
                    </>
                )}
                processing={busy}
                confirmLabel="Confirmar exclusão"
                processingLabel="Excluindo semestre..."
                confirmTone="dark-red"
                onClose={() => {
                    if (busy) return;
                    setSemesterToDelete(null);
                    setFailure("");
                }}
                onConfirm={remove}
            />
        </SemesterPage>
    );
}
