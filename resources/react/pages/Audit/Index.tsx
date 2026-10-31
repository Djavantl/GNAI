import { usePage } from "@inertiajs/react";
import { useEffect, useMemo, useState } from "react";
import DatePicker from "@/components/Forms/DatePicker/DatePicker";
import FormField from "@/components/Forms/FormField/FormField";
import FormLayout from "@/components/Forms/FormLayout/FormLayout";
import SelectField from "@/components/Forms/SelectField/SelectField";
import TextInput from "@/components/Forms/TextInput/TextInput";
import BusinessPage from "@/components/Navigation/BusinessPage/BusinessPage";
import TableActions from "@/components/Tables/TableActions/TableActions";
import {
    TableListCard,
    TableListClearButton,
    TableListEmpty,
    TableListFilterActions,
    TableListFooter,
    TableListMoreFilters,
    TableListScroll,
    TableListTable,
    useTableListVisit,
    type TableListColumn,
} from "@/components/Tables/TableList/TableList";
import Button from "@/components/UI/Button/Button";
import TableLabel from "@/components/UI/TableLabel/TableLabel";
import useAutoListFilters from "@/hooks/useAutoListFilters";
import type { AuditFilters, AuditOptions, AuditPaginator, AuditRecord } from "./Audit.types";
import { auditDateTime, auditEventTone, isFilterDate } from "./Audit.utils";
import AuditRecordModal from "./AuditRecordModal";
import "./Audit.css";

type Props = {
    records: AuditPaginator;
    filters: Partial<AuditFilters>;
    options: AuditOptions;
};

const base = "/audit";
const emptyFilters: AuditFilters = {
    date_from: "",
    date_to: "",
    actor: "",
    type: "",
    event: "",
    search: "",
};
const columns: TableListColumn[] = [
    { id: "entity", label: "Registro", width: "30%", minWidth: 140, fixed: "start" },
    { id: "event", label: "Ação", width: "12%", minWidth: 90 },
    { id: "type", label: "Tipo", width: "16%", minWidth: 100 },
    { id: "author", label: "Quem fez", width: "18%", minWidth: 110 },
    { id: "date", label: "Quando", width: "14%", minWidth: 110 },
    { id: "actions", label: "Ações", width: "10%", minWidth: 70, fixed: "end" },
];

export default function AuditIndexPage({ records, filters: initialFilters, options }: Props): React.JSX.Element {
    const pageErrors = usePage().props.errors as Record<string, string | undefined>;
    const list = useTableListVisit({
        url: base,
        failureText: "Não foi possível atualizar o histórico. Tente novamente.",
    });
    const [selected, setSelected] = useState<AuditRecord | null>(null);
    const initialValues = useMemo(() => ({ ...emptyFilters, ...initialFilters }), [initialFilters]);
    const { values, update, clear, submit } = useAutoListFilters({
        initialValues,
        emptyValues: emptyFilters,
        onSearch: (filters) => list.visit(filters),
    });
    const hasFilters = Object.values(values).some((value) => value !== "");

    useEffect(() => {
        // Registro aberto que saiu da página atual (outra página ou outro filtro) fecha o detalhe.
        if (selected && !records.data.some((record) => record.id === selected.id)) setSelected(null);
    }, [records.data, selected]);

    function updateDate(name: "date_from" | "date_to", value: string): void {
        if (isFilterDate(value) && value !== values[name]) update(name, value);
    }

    return (
        <BusinessPage
            title="Histórico de Alterações"
            subtitle="Consulte quem criou, alterou, excluiu ou restaurou registros do sistema, e o que mudou em cada operação."
            breadcrumbItems={[
                { label: "Home", href: "/auth/dashboard" },
                { label: "Auditoria" },
            ]}
            className="auditIndex"
        >
            <TableListCard
                aria-label="Histórico de alterações"
                aria-busy={list.loading}
                loading={list.loading}
                loadingLabel="Atualizando histórico de alterações"
            >
                <FormLayout
                    onSubmit={(event) => {
                        event.preventDefault();
                        submit();
                    }}
                >
                    <div className="auditIndex__filters">
                        <FormField id="audit-filter-search" label="O que você procura?" error={pageErrors.search}>
                            <TextInput
                                id="audit-filter-search"
                                name="search"
                                value={values.search}
                                maxLength={100}
                                placeholder="Ex.: nome do aluno, sessão #12, nome de quem alterou"
                                invalid={!!pageErrors.search}
                                aria-describedby={pageErrors.search ? "audit-filter-search-error" : undefined}
                                onChange={(event) => update("search", event.target.value)}
                            />
                        </FormField>
                        <FormField id="audit-filter-date-from" label="De" error={pageErrors.date_from}>
                            <DatePicker
                                id="audit-filter-date-from"
                                name="date_from"
                                label="De"
                                value={values.date_from}
                                invalid={!!pageErrors.date_from}
                                describedBy={pageErrors.date_from ? "audit-filter-date-from-error" : undefined}
                                onChange={(value) => updateDate("date_from", value)}
                            />
                        </FormField>
                        <FormField id="audit-filter-date-to" label="Até" error={pageErrors.date_to}>
                            <DatePicker
                                id="audit-filter-date-to"
                                name="date_to"
                                label="Até"
                                value={values.date_to}
                                invalid={!!pageErrors.date_to}
                                describedBy={pageErrors.date_to ? "audit-filter-date-to-error" : undefined}
                                onChange={(value) => updateDate("date_to", value)}
                            />
                        </FormField>
                        <FormField id="audit-filter-actor" label="Quem fez" error={pageErrors.actor}>
                            <SelectField
                                id="audit-filter-actor"
                                name="actor"
                                label="Quem fez"
                                placeholder="Todos"
                                value={values.actor}
                                options={options.actors}
                                aria-invalid={!!pageErrors.actor}
                                aria-describedby={pageErrors.actor ? "audit-filter-actor-error" : undefined}
                                onChange={(event) => update("actor", event.target.value)}
                            />
                        </FormField>
                        <TableListFilterActions>
                            <TableListClearButton disabled={!hasFilters} onClick={clear} />
                        </TableListFilterActions>
                    </div>
                    <TableListMoreFilters active={Boolean(initialValues.type || initialValues.event)}>
                        <div className="auditIndex__additionalFilters">
                            <FormField id="audit-filter-type" label="Tipo de registro" error={pageErrors.type}>
                                <SelectField
                                    id="audit-filter-type"
                                    name="type"
                                    label="Tipo de registro"
                                    placeholder="Todos"
                                    value={values.type}
                                    options={options.types}
                                    aria-invalid={!!pageErrors.type}
                                    aria-describedby={pageErrors.type ? "audit-filter-type-error" : undefined}
                                    onChange={(event) => update("type", event.target.value)}
                                />
                            </FormField>
                            <FormField id="audit-filter-event" label="Ação" error={pageErrors.event}>
                                <SelectField
                                    id="audit-filter-event"
                                    name="event"
                                    label="Ação"
                                    placeholder="Todas"
                                    value={values.event}
                                    options={options.events}
                                    aria-invalid={!!pageErrors.event}
                                    aria-describedby={pageErrors.event ? "audit-filter-event-error" : undefined}
                                    onChange={(event) => update("event", event.target.value)}
                                />
                            </FormField>
                        </div>
                    </TableListMoreFilters>
                </FormLayout>
                <TableListScroll aria-label="Tabela do histórico de alterações">
                    <TableListTable
                        className="auditIndex__table"
                        columns={columns}
                        page={records}
                    >
                        {records.data.length ? records.data.map((record) => (
                            <tr key={record.id}>
                                <td title={record.plain.summary}>{record.plain.summary}</td>
                                <td>
                                    <TableLabel tone={auditEventTone(record.event)}>{record.plain.event}</TableLabel>
                                </td>
                                <td>{record.plain.type}</td>
                                <td>{record.plain.author}</td>
                                <td>
                                    <time dateTime={record.date ?? undefined}>{auditDateTime(record.date)}</time>
                                </td>
                                <td>
                                    <TableActions>
                                        <Button
                                            size="sm"
                                            tone="dark-blue"
                                            iconOnly
                                            leftIconClass="fa fa-eye"
                                            aria-label={`Ver detalhes: ${record.plain.summary}`}
                                            onClick={() => setSelected(record)}
                                        />
                                    </TableActions>
                                </td>
                            </tr>
                        )) : (
                            <tr>
                                <TableListEmpty colSpan={columns.length}>
                                    {hasFilters ? "Nenhum registro com esses filtros." : "Nenhuma alteração registrada ainda."}
                                </TableListEmpty>
                            </tr>
                        )}
                    </TableListTable>
                </TableListScroll>
                <TableListFooter
                    page={records}
                    disabled={list.loading}
                    paginationLabel="Paginação do histórico de alterações"
                    onSelect={(page) => list.visit(values, page)}
                />
            </TableListCard>
            <AuditRecordModal record={selected} onClose={() => setSelected(null)} />
        </BusinessPage>
    );
}
