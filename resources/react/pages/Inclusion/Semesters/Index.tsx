import { usePage } from "@inertiajs/react";
import { useMemo } from "react";
import FormField from "@/components/Forms/FormField/FormField";
import FormLayout from "@/components/Forms/FormLayout/FormLayout";
import SelectField from "@/components/Forms/SelectField/SelectField";
import TextInput from "@/components/Forms/TextInput/TextInput";
import TableActions from "@/components/Tables/TableActions/TableActions";
import {
    formatTableListDate,
    TableListCard,
    TableListClearButton,
    TableListCreateButton,
    TableListEmpty,
    TableListFilterActions,
    TableListFooter,
    TableListScroll,
    TableListTable,
    useTableListAction,
    useTableListVisit,
    type TableListColumn,
} from "@/components/Tables/TableList/TableList";
import Button from "@/components/UI/Button/Button";
import ConfirmActionModal from "@/components/UI/ConfirmActionModal/ConfirmActionModal";
import InlineAlert from "@/components/UI/InlineAlert/InlineAlert";
import TableLabel from "@/components/UI/TableLabel/TableLabel";
import useAutoListFilters from "@/hooks/useAutoListFilters";
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
    { id: "start", label: "Início", width: "16%", defaultVisible: false },
    { id: "end", label: "Fim", width: "16%", defaultVisible: false },
    { id: "actions", label: "Ações", width: "22%", minWidth: 120, fixed: "end" },
];

export default function InclusionSemestersIndexPage({
    semesters,
    filters: initialFilters,
    can,
}: Props): React.JSX.Element {
    const pageErrors = usePage().props.errors as Record<string, string | undefined>;
    const list = useTableListVisit({
        url: semesterBase,
        failureText: "Não foi possível atualizar os semestres. Tente novamente.",
    });
    const removal = useTableListAction<SemesterRecord>({
        failureText: "Não foi possível excluir o semestre. Tente novamente.",
    });
    const currentChange = useTableListAction<SemesterRecord>({
        failureText: "Não foi possível definir o semestre atual. Tente novamente.",
    });
    const initialValues = useMemo(
        () => ({ ...emptyFilters, ...initialFilters }),
        [initialFilters],
    );
    const { values, update, clear, submit, cancelScheduled } = useAutoListFilters({
        initialValues,
        emptyValues: emptyFilters,
        onSearch: (filters) => list.visit(filters),
    });

    function setCurrent(semester: SemesterRecord): void {
        cancelScheduled();
        currentChange.submit("patch", `${semesterBase}/${semester.id}/set-current`);
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
                aria-busy={list.loading}
                loading={list.loading}
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
                                aria-invalid={!!pageErrors.is_current}
                                aria-describedby={pageErrors.is_current ? "semester-filter-current-error" : undefined}
                                onChange={(event) => update("is_current", event.target.value)}
                            />
                        </FormField>
                        <TableListFilterActions>
                            <TableListClearButton onClick={clear} />
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
                                        <span className="semesterIndex__label">{semester.label || "Não informado"}</span>
                                    </td>
                                    <td>
                                        <TableLabel tone={semester.is_current ? "success" : "inverse"}>
                                            {semester.is_current ? "Semestre atual" : "Histórico"}
                                        </TableLabel>
                                    </td>
                                    <td>{formatTableListDate(semester.start_date)}</td>
                                    <td>{formatTableListDate(semester.end_date)}</td>
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
                                                    disabled={currentChange.processing}
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
                                                    disabled={removal.processing}
                                                    onClick={() => {
                                                        cancelScheduled();
                                                        removal.open(semester);
                                                    }}
                                                />
                                            ) : null}
                                            {!can.view && !canSetCurrent && !canDelete ? <span>Sem ações</span> : null}
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
                    disabled={list.loading}
                    paginationLabel="Paginação de semestres"
                    onSelect={(page) => list.visit(values, page)}
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
                open={removal.target !== null}
                title="Excluir Semestre"
                message={(
                    <>
                        Tem certeza que deseja excluir o semestre{" "}
                        <strong>{removal.target ? semesterLabel(removal.target) : ""}</strong>?
                    </>
                )}
                processing={removal.processing}
                confirmLabel="Confirmar exclusão"
                processingLabel="Excluindo semestre..."
                confirmTone="dark-red"
                onClose={removal.close}
                onConfirm={() => {
                    if (removal.target) removal.submit("delete", `${semesterBase}/${removal.target.id}`);
                }}
            />
        </SemesterPage>
    );
}
