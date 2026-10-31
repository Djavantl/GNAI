import { usePage } from "@inertiajs/react";
import { useMemo } from "react";
import FormField from "@/components/Forms/FormField/FormField";
import FormLayout from "@/components/Forms/FormLayout/FormLayout";
import SelectField from "@/components/Forms/SelectField/SelectField";
import TextInput from "@/components/Forms/TextInput/TextInput";
import TableActions from "@/components/Tables/TableActions/TableActions";
import {
    formatTableListText,
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
import TableLabel from "@/components/UI/TableLabel/TableLabel";
import useAutoListFilters from "@/hooks/useAutoListFilters";
import DisciplinePage, { disciplineBase } from "./DisciplinePage";
import type { DisciplineFilters, DisciplinePaginator, DisciplineRecord } from "./Discipline.types";
import "./Disciplines.css";

type Props = {
    disciplines: DisciplinePaginator;
    filters: Partial<DisciplineFilters>;
    can: { create: boolean; view: boolean; delete: boolean };
};

const emptyFilters: DisciplineFilters = { name: "", is_active: "" };
const statusOptions = [
    { value: "1", label: "Ativo" },
    { value: "0", label: "Inativo" },
];
const columns: TableListColumn[] = [
    { id: "name", label: "Nome", width: "60%", minWidth: 140, fixed: "start" },
    { id: "status", label: "Status", width: "25%", minWidth: 90 },
    { id: "description", label: "Descrição", width: "16%", defaultVisible: false },
    { id: "actions", label: "Ações", width: "15%", minWidth: 86, fixed: "end" },
];

export default function InclusionDisciplinesIndexPage({
    disciplines,
    filters: initialFilters,
    can,
}: Props): React.JSX.Element {
    const pageErrors = usePage().props.errors as Record<string, string | undefined>;
    const list = useTableListVisit({
        url: disciplineBase,
        failureText: "Não foi possível atualizar as disciplinas. Tente novamente.",
    });
    const removal = useTableListAction<DisciplineRecord>({
        failureText: "Não foi possível excluir a disciplina. Tente novamente.",
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

    return (
        <DisciplinePage
            title="Disciplinas"
            subtitle="Gerencie as disciplinas cadastradas no sistema."
            actions={can.create ? (
                <TableListCreateButton
                    href={`${disciplineBase}/create`}
                    ariaLabel="Cadastrar disciplina"
                />
            ) : undefined}
        >
            <TableListCard
                aria-label="Listagem de disciplinas"
                aria-busy={list.loading}
                loading={list.loading}
                loadingLabel="Atualizando disciplinas"
            >
                <FormLayout
                    onSubmit={(event) => {
                        event.preventDefault();
                        submit();
                    }}
                >
                    <div className="disciplineIndex__filters">
                        <FormField
                            id="discipline-filter-name"
                            label="Nome da Disciplina"
                            error={pageErrors.name}
                        >
                            <TextInput
                                id="discipline-filter-name"
                                name="name"
                                value={values.name}
                                maxLength={255}
                                placeholder="Nome da disciplina..."
                                invalid={!!pageErrors.name}
                                aria-describedby={pageErrors.name ? "discipline-filter-name-error" : undefined}
                                onChange={(event) => update("name", event.target.value)}
                            />
                        </FormField>
                        <FormField
                            id="discipline-filter-status"
                            label="Status"
                            error={pageErrors.is_active}
                        >
                            <SelectField
                                id="discipline-filter-status"
                                name="is_active"
                                label="Status"
                                placeholder="Todos"
                                value={values.is_active}
                                options={statusOptions}
                                aria-invalid={!!pageErrors.is_active}
                                aria-describedby={pageErrors.is_active ? "discipline-filter-status-error" : undefined}
                                onChange={(event) => update("is_active", event.target.value)}
                            />
                        </FormField>
                        <TableListFilterActions>
                            <TableListClearButton onClick={clear} />
                        </TableListFilterActions>
                    </div>
                </FormLayout>
                <TableListScroll aria-label="Tabela de disciplinas">
                    <TableListTable
                        className="disciplineIndex__table"
                        columns={columns}
                        page={disciplines}
                    >
                        {disciplines.data.length ? disciplines.data.map((discipline) => (
                            <tr key={discipline.id}>
                                <td>
                                    <span className="disciplineIndex__name">{discipline.name}</span>
                                </td>
                                <td>
                                    <TableLabel tone={discipline.is_active ? "success" : "inverse"}>
                                        {discipline.is_active ? "Ativo" : "Inativo"}
                                    </TableLabel>
                                </td>
                                <td>{formatTableListText(discipline.description)}</td>
                                <td>
                                    <TableActions>
                                        {can.view ? (
                                            <Button
                                                href={`${disciplineBase}/${discipline.id}/show`}
                                                size="sm"
                                                tone="dark-blue"
                                                iconOnly
                                                leftIconClass="fa fa-eye"
                                                aria-label={`Visualizar ${discipline.name}`}
                                            />
                                        ) : null}
                                        {can.delete ? (
                                            <Button
                                                tone="dark-red"
                                                size="sm"
                                                iconOnly
                                                leftIconClass="fa fa-trash-o"
                                                aria-label={`Excluir ${discipline.name}`}
                                                disabled={removal.processing}
                                                onClick={() => {
                                                    cancelScheduled();
                                                    removal.open(discipline);
                                                }}
                                            />
                                        ) : null}
                                        {!can.view && !can.delete ? <span>Sem ações</span> : null}
                                    </TableActions>
                                </td>
                            </tr>
                        )) : (
                            <tr>
                                <TableListEmpty colSpan={columns.length}>
                                    Nenhuma disciplina encontrada.
                                </TableListEmpty>
                            </tr>
                        )}
                    </TableListTable>
                </TableListScroll>
                <TableListFooter
                    page={disciplines}
                    disabled={list.loading}
                    paginationLabel="Paginação de disciplinas"
                    onSelect={(page) => list.visit(values, page)}
                />
            </TableListCard>
            <ConfirmActionModal
                open={removal.target !== null}
                title="Excluir Disciplina"
                message={(
                    <>
                        Deseja excluir a disciplina <strong>{removal.target?.name}</strong>?
                    </>
                )}
                processing={removal.processing}
                confirmLabel="Confirmar exclusão"
                processingLabel="Excluindo disciplina..."
                confirmTone="dark-red"
                onClose={removal.close}
                onConfirm={() => {
                    if (removal.target) removal.submit("delete", `${disciplineBase}/${removal.target.id}`);
                }}
            />
        </DisciplinePage>
    );
}
