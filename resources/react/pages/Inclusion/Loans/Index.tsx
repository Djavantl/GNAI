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
    TableListMoreFilters,
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
import LoanPage from "./LoanPage";
import type { LoanPaginator, LoanRecord } from "./Loan.types";
import "./Loan.css";

type Filters = {
    item: string;
    student: string;
    professional: string;
    status: string;
};

type Props = {
    loans: LoanPaginator;
    filters: Partial<Filters>;
    can: { create: boolean; show: boolean; destroy: boolean };
};

const base = "/inclusive-radar/loans";
const emptyFilters: Filters = {
    item: "",
    student: "",
    professional: "",
    status: "",
};
const statusOptions = [
    { value: "active", label: "Ativo (Com o Beneficiário)" },
    { value: "returned", label: "Devolvido (No prazo)" },
    { value: "late", label: "Devolvido (Com atraso)" },
    { value: "damaged", label: "Devolvido (Com avaria)" },
];
const columns: TableListColumn[] = [
    { id: "item", label: "Item", width: "24%", minWidth: 130, fixed: "start" },
    { id: "beneficiary", label: "Beneficiário", width: "22%", minWidth: 120 },
    { id: "due", label: "Prazo", width: "14%", minWidth: 85 },
    { id: "status", label: "Status", width: "16%", minWidth: 90 },
    { id: "user", label: "Usuário", width: "12%", minWidth: 80 },
    { id: "type", label: "Tipo do item", width: "16%", defaultVisible: false },
    { id: "asset", label: "Patrimônio / Tombamento", width: "16%", defaultVisible: false },
    { id: "registration", label: "Matrícula do beneficiário", width: "16%", defaultVisible: false },
    { id: "departure", label: "Saída", width: "16%", defaultVisible: false },
    { id: "return", label: "Devolução", width: "16%", defaultVisible: false },
    { id: "observation", label: "Observações", width: "16%", defaultVisible: false },
    { id: "actions", label: "Ações", width: "12%", minWidth: 92, fixed: "end" },
];

export default function InclusionLoansIndexPage({
    loans,
    filters: initialFilters,
    can,
}: Props): React.JSX.Element {
    const { errors } = usePage().props;
    const list = useTableListVisit({
        url: base,
        failureText: "Não foi possível atualizar os empréstimos. Tente novamente.",
    });
    const removal = useTableListAction<LoanRecord>({
        failureText: "Não foi possível excluir o empréstimo. Tente novamente.",
    });
    const initialValues = useMemo(
        () => ({ ...emptyFilters, ...initialFilters }),
        [initialFilters],
    );
    const {
        values: filters,
        update: updateFilter,
        clear: clearFilters,
        submit: submitFilters,
        cancelScheduled,
    } = useAutoListFilters({
        initialValues,
        emptyValues: emptyFilters,
        onSearch: (values: Filters) => list.visit(values),
    });

    return (
        <LoanPage
            title="Empréstimos de Recursos"
            subtitle="Controle de saídas e devoluções de tecnologias e materiais pedagógicos."
            className="loanIndex"
            actions={
                can.create ? (
                    <TableListCreateButton
                        href={`${base}/create`}
                        ariaLabel="Cadastrar empréstimo"
                    />
                ) : undefined
            }
        >
            <TableListCard
                aria-label="Listagem de empréstimos"
                aria-busy={list.loading}
                loading={list.loading}
                loadingLabel="Atualizando empréstimos"
            >
                <FormLayout
                    onSubmit={(event) => {
                        event.preventDefault();
                        submitFilters();
                    }}
                >
                    <div className="loanIndex__filters">
                        <FormField id="loan-item-filter" label="Item" error={errors.item}>
                            <TextInput
                                id="loan-item-filter"
                                name="item"
                                value={filters.item}
                                maxLength={255}
                                placeholder="Filtrar por item..."
                                invalid={!!errors.item}
                                aria-describedby={errors.item ? "loan-item-filter-error" : undefined}
                                onChange={(event) => updateFilter("item", event.target.value)}
                            />
                        </FormField>
                        <FormField id="loan-student-filter" label="Estudante" error={errors.student}>
                            <TextInput
                                id="loan-student-filter"
                                name="student"
                                value={filters.student}
                                maxLength={255}
                                placeholder="Filtrar por estudante..."
                                invalid={!!errors.student}
                                aria-describedby={errors.student ? "loan-student-filter-error" : undefined}
                                onChange={(event) => updateFilter("student", event.target.value)}
                            />
                        </FormField>
                        <FormField id="loan-professional-filter" label="Profissional" error={errors.professional}>
                            <TextInput
                                id="loan-professional-filter"
                                name="professional"
                                value={filters.professional}
                                maxLength={255}
                                placeholder="Filtrar por profissional..."
                                invalid={!!errors.professional}
                                aria-describedby={errors.professional ? "loan-professional-filter-error" : undefined}
                                onChange={(event) => updateFilter("professional", event.target.value)}
                            />
                        </FormField>
                        <TableListFilterActions>
                            <TableListClearButton onClick={clearFilters} />
                        </TableListFilterActions>
                    </div>
                    <TableListMoreFilters active={Boolean(filters.status)}>
                        <div className="loanIndex__additionalFilters">
                            <FormField id="loan-status-filter" label="Status" error={errors.status}>
                                <SelectField
                                    id="loan-status-filter"
                                    name="status"
                                    label="Status"
                                    placeholder="Todos"
                                    value={filters.status}
                                    options={statusOptions}
                                    aria-invalid={!!errors.status}
                                    aria-describedby={errors.status ? "loan-status-filter-error" : undefined}
                                    onChange={(event) => updateFilter("status", event.target.value)}
                                />
                            </FormField>
                        </div>
                    </TableListMoreFilters>
                </FormLayout>
                <TableListScroll aria-label="Tabela de empréstimos">
                    <TableListTable
                        className="loanIndex__table"
                        columns={columns}
                        page={loans}
                    >
                        {loans.data.map((loan) => {
                            const beneficiary = loan.student ?? loan.professional;
                            return (
                                <tr key={loan.id}>
                                    <td>
                                        <a
                                            href={`/inclusive-radar/items/${loan.loanable_id}`}
                                            target="_blank"
                                            rel="noreferrer"
                                        >
                                            {loan.loanable.name}
                                        </a>
                                    </td>
                                    <td>
                                        <span className="loanIndex__beneficiary">
                                            <span className="loanIndex__beneficiaryName">
                                                {beneficiary?.name ?? "Não informado"}
                                            </span>
                                        </span>
                                    </td>
                                    <td className={loan.is_overdue ? "loanIndex__overdue" : undefined}>
                                        {loan.due_date_label}
                                    </td>
                                    <td>
                                        <TableLabel tone={loan.status_color}>
                                            {loan.status_label}
                                        </TableLabel>
                                    </td>
                                    <td>{loan.user.name}</td>
                                    <td>{loan.item_type_label}</td>
                                    <td>{loan.loanable.asset_code || "Não informado"}</td>
                                    <td>{loan.student?.registration || loan.professional?.registration || "Não informado"}</td>
                                    <td>{loan.loan_date_label}</td>
                                    <td>{loan.return_date_label || "Não informado"}</td>
                                    <td>{formatTableListText(loan.observation)}</td>
                                    <td>
                                        <TableActions>
                                            {can.show ? (
                                                <Button
                                                    href={`${base}/${loan.id}`}
                                                    size="sm"
                                                    tone="dark-blue"
                                                    iconOnly
                                                    leftIconClass="fa fa-eye"
                                                    aria-label={`Visualizar empréstimo de ${loan.loanable.name}`}
                                                />
                                            ) : null}
                                            {can.destroy ? (
                                                <Button
                                                    size="sm"
                                                    tone="dark-red"
                                                    iconOnly
                                                    leftIconClass="fa fa-trash-o"
                                                    disabled={removal.processing}
                                                    aria-label={`Excluir empréstimo de ${loan.loanable.name}`}
                                                    onClick={() => removal.open(loan)}
                                                />
                                            ) : null}
                                            {!can.show && !can.destroy ? <span>Nenhuma ação</span> : null}
                                        </TableActions>
                                    </td>
                                </tr>
                            );
                        })}
                        {!loans.data.length ? (
                            <tr>
                                <TableListEmpty colSpan={columns.length}>
                                    Nenhum empréstimo registrado.
                                </TableListEmpty>
                            </tr>
                        ) : null}
                    </TableListTable>
                </TableListScroll>
                <TableListFooter
                    page={loans}
                    disabled={list.loading}
                    paginationLabel="Páginas da listagem de empréstimos"
                    onSelect={(page) => {
                        cancelScheduled();
                        const params = new URLSearchParams(window.location.search);
                        params.set("page", String(page));
                        list.visitUrl(`${base}?${params.toString()}`);
                    }}
                />
            </TableListCard>
            <ConfirmActionModal
                open={removal.target !== null}
                title="Excluir Empréstimo"
                message={
                    <>
                        Excluir permanentemente o empréstimo de{" "}
                        <strong>{removal.target?.loanable.name}</strong>?
                    </>
                }
                processing={removal.processing}
                confirmLabel="Confirmar exclusão"
                processingLabel="Excluindo empréstimo..."
                confirmTone="dark-red"
                onClose={removal.close}
                onConfirm={() => {
                    if (!removal.target) return;

                    cancelScheduled();
                    removal.submit("delete", `${base}/${removal.target.id}`);
                }}
            />
        </LoanPage>
    );
}
