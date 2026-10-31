import { usePage } from "@inertiajs/react";
import { useMemo } from "react";
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
    useTableListAction,
    useTableListVisit,
    type TableListColumn,
} from "@/components/Tables/TableList/TableList";
import Button from "@/components/UI/Button/Button";
import ConfirmActionModal from "@/components/UI/ConfirmActionModal/ConfirmActionModal";
import TableLabel from "@/components/UI/TableLabel/TableLabel";
import useAutoListFilters from "@/hooks/useAutoListFilters";
import TagPage from "./TagPage";
import type {
    TagPaginator,
    TagRecord,
} from "./Tag.types";
import "./Tags.css";

type Filters = { name: string; is_active: string };
type Props = {
    tags: TagPaginator;
    filters: Partial<Filters>;
    can: { create: boolean; show: boolean; destroy: boolean };
};

const base = "/inclusive-radar/tags";
const emptyFilters: Filters = { name: "", is_active: "" };
const columns: TableListColumn[] = [
    { id: "name", label: "Nome", width: "60%", minWidth: 150, fixed: "start" },
    { id: "status", label: "Status", width: "20%", minWidth: 90 },
    { id: "actions", label: "Ações", width: "20%", minWidth: 86, fixed: "end" },
];

export default function InclusionTagsIndexPage({
    tags,
    filters: initialFilters,
    can,
}: Props): React.JSX.Element {
    const pageErrors = usePage().props.errors as Record<string, string | undefined>;
    const list = useTableListVisit({
        url: base,
        failureText: "Não foi possível atualizar as etiquetas. Tente novamente.",
    });
    const removal = useTableListAction<TagRecord>({
        failureText: "Não foi possível excluir a etiqueta. Tente novamente.",
    });
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
        onSearch: (values) => list.visit(values),
    });

    return (
        <TagPage
            title="Etiquetas"
            subtitle="Gerencie as etiquetas disponíveis para classificar as publicações."
            actions={can.create ? (
                <TableListCreateButton href={`${base}/create`} ariaLabel="Cadastrar etiqueta" />
            ) : undefined}
        >
            <TableListCard aria-label="Listagem de etiquetas" aria-busy={list.loading} loading={list.loading} loadingLabel="Atualizando etiquetas">
                <FormLayout onSubmit={(event) => { event.preventDefault(); submitFilters(); }}>
                    <div className="tagIndex__filters">
                        <FormField id="tag-filter-name" label="Nome" error={pageErrors.name}>
                            <TextInput
                                id="tag-filter-name"
                                name="name"
                                value={filterValues.name}
                                maxLength={100}
                                placeholder="Filtrar por nome..."
                                invalid={!!pageErrors.name}
                                aria-describedby={pageErrors.name ? "tag-filter-name-error" : undefined}
                                onChange={(event) => updateFilter("name", event.target.value)}
                            />
                        </FormField>
                        <FormField id="tag-filter-status" label="Status" error={pageErrors.is_active}>
                            <SelectField
                                id="tag-filter-status"
                                name="is_active"
                                label="Status"
                                placeholder="Todos"
                                value={filterValues.is_active}
                                options={[
                                    { value: "1", label: "Ativo" },
                                    { value: "0", label: "Inativo" },
                                ]}
                                aria-invalid={!!pageErrors.is_active}
                                aria-describedby={pageErrors.is_active ? "tag-filter-status-error" : undefined}
                                onChange={(event) => updateFilter("is_active", event.target.value)}
                            />
                        </FormField>
                        <TableListFilterActions>
                            <TableListClearButton onClick={clearFilters} />
                        </TableListFilterActions>
                    </div>
                </FormLayout>
                <TableListScroll aria-label="Tabela de etiquetas">
                    <TableListTable
                        className="tagIndex__table"
                        columns={columns}
                        page={tags}
                    >
                        {tags.data.length ? tags.data.map((tag) => (
                            <tr key={tag.id}>
                                <td>{tag.name}</td>
                                <td>
                                    <TableLabel tone={tag.is_active ? "success" : "inverse"}>
                                        {tag.is_active ? "Ativo" : "Inativo"}
                                    </TableLabel>
                                </td>
                                <td>
                                    <TableActions>
                                        {can.show ? (
                                            <Button
                                                href={`${base}/${tag.id}`}
                                                size="sm"
                                                tone="dark-blue"
                                                iconOnly
                                                leftIconClass="fa fa-eye"
                                                aria-label={`Visualizar ${tag.name}`}
                                            />
                                        ) : null}
                                        {can.destroy ? (
                                            <Button
                                                tone="dark-red"
                                                size="sm"
                                                iconOnly
                                                leftIconClass="fa fa-trash-o"
                                                aria-label={`Excluir ${tag.name}`}
                                                onClick={() => {
                                                    cancelScheduled();
                                                    removal.open(tag);
                                                }}
                                            />
                                        ) : null}
                                        {!can.show && !can.destroy ? <span>Sem ações</span> : null}
                                    </TableActions>
                                </td>
                            </tr>
                        )) : (
                            <tr><TableListEmpty colSpan={columns.length}>Nenhuma etiqueta cadastrada.</TableListEmpty></tr>
                        )}
                    </TableListTable>
                </TableListScroll>
                <TableListFooter
                    page={tags}
                    disabled={list.loading}
                    paginationLabel="Paginação de etiquetas"
                    onSelect={(page) => list.visit(filterValues, page)}
                />
            </TableListCard>
            <ConfirmActionModal
                open={removal.target !== null}
                title="Excluir Etiqueta"
                message={<>A etiqueta <strong>{removal.target?.name}</strong> será excluída permanentemente.</>}
                processing={removal.processing}
                confirmLabel="Confirmar exclusão"
                processingLabel="Excluindo etiqueta..."
                confirmTone="dark-red"
                onClose={removal.close}
                onConfirm={() => {
                    if (removal.target) removal.submit("delete", `${base}/${removal.target.id}`);
                }}
            />
        </TagPage>
    );
}
