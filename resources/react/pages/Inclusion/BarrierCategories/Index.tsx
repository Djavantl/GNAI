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
import BarrierCategoryPage from "./BarrierCategoryPage";
import type {
    BarrierCategoryPaginator,
    BarrierCategoryRecord,
} from "./BarrierCategory.types";
import "./BarrierCategories.css";

type Filters = { name: string; is_active: string };
type Props = {
    categories: BarrierCategoryPaginator;
    filters: Partial<Filters>;
    can: { create: boolean; show: boolean; destroy: boolean };
};

const base = "/inclusive-radar/barrier-categories";
const emptyFilters: Filters = { name: "", is_active: "" };
const columns: TableListColumn[] = [
    { id: "name", label: "Nome", width: "42%", minWidth: 140, fixed: "start" },
    { id: "count", label: "Vínculos", width: "20%", minWidth: 80 },
    { id: "status", label: "Status", width: "18%", minWidth: 90 },
    { id: "description", label: "Descrição", width: "16%", defaultVisible: false },
    { id: "map", label: "Permite marcar no mapa", width: "16%", defaultVisible: false },
    { id: "actions", label: "Ações", width: "20%", minWidth: 86, fixed: "end" },
];

export default function InclusionBarrierCategoriesIndexPage({
    categories,
    filters: initialFilters,
    can,
}: Props): React.JSX.Element {
    const pageErrors = usePage().props.errors as Record<string, string | undefined>;
    const list = useTableListVisit({
        url: base,
        failureText: "Não foi possível atualizar as categorias de barreiras. Tente novamente.",
    });
    const removal = useTableListAction<BarrierCategoryRecord>({
        failureText: "Não foi possível excluir a categoria. Tente novamente.",
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
        <BarrierCategoryPage
            title="Categorias de Barreiras"
            subtitle="Classificação para o mapeamento de acessibilidade e identificação de obstáculos."
            actions={can.create ? (
                <TableListCreateButton href={`${base}/create`} ariaLabel="Cadastrar categoria de barreira" />
            ) : undefined}
        >
            <TableListCard aria-label="Listagem de categorias de barreiras" aria-busy={list.loading} loading={list.loading} loadingLabel="Atualizando categorias de barreiras">
                <FormLayout onSubmit={(event) => { event.preventDefault(); submitFilters(); }}>
                    <div className="barrierCategoryIndex__filters">
                        <FormField id="barrier-category-filter-name" label="Nome da Categoria" error={pageErrors.name}>
                            <TextInput
                                id="barrier-category-filter-name"
                                name="name"
                                value={filterValues.name}
                                maxLength={150}
                                placeholder="Filtrar por nome da categoria..."
                                invalid={!!pageErrors.name}
                                aria-describedby={pageErrors.name ? "barrier-category-filter-name-error" : undefined}
                                onChange={(event) => updateFilter("name", event.target.value)}
                            />
                        </FormField>
                        <FormField id="barrier-category-filter-status" label="Status" error={pageErrors.is_active}>
                            <SelectField
                                id="barrier-category-filter-status"
                                name="is_active"
                                label="Status"
                                placeholder="Todos"
                                value={filterValues.is_active}
                                options={[
                                    { value: "1", label: "Ativo" },
                                    { value: "0", label: "Inativo" },
                                ]}
                                aria-invalid={!!pageErrors.is_active}
                                aria-describedby={pageErrors.is_active ? "barrier-category-filter-status-error" : undefined}
                                onChange={(event) => updateFilter("is_active", event.target.value)}
                            />
                        </FormField>
                        <TableListFilterActions>
                            <TableListClearButton onClick={clearFilters} />
                        </TableListFilterActions>
                    </div>
                </FormLayout>
                <TableListScroll aria-label="Tabela de categorias de barreiras">
                    <TableListTable
                        className="barrierCategoryIndex__table"
                        columns={columns}
                        page={categories}
                    >
                        {categories.data.length ? categories.data.map((category) => (
                            <tr key={category.id}>
                                <td>{category.name}</td>
                                <td>{category.barriers_count ?? 0}</td>
                                <td>
                                    <TableLabel tone={category.is_active ? "success" : "inverse"}>
                                        {category.is_active ? "Ativo" : "Inativo"}
                                    </TableLabel>
                                </td>
                                <td>{formatTableListText(category.description)}</td>
                                <td>{category.blocks_map ? "Não" : "Sim"}</td>
                                <td>
                                    <TableActions>
                                        {can.show ? (
                                            <Button
                                                href={`${base}/${category.id}`}
                                                size="sm"
                                                tone="dark-blue"
                                                iconOnly
                                                leftIconClass="fa fa-eye"
                                                aria-label={`Visualizar ${category.name}`}
                                            />
                                        ) : null}
                                        {can.destroy ? (
                                            <Button
                                                tone="dark-red"
                                                size="sm"
                                                iconOnly
                                                leftIconClass="fa fa-trash-o"
                                                aria-label={`Excluir ${category.name}`}
                                                onClick={() => {
                                                    cancelScheduled();
                                                    removal.open(category);
                                                }}
                                            />
                                        ) : null}
                                        {!can.show && !can.destroy ? <span>Sem ações</span> : null}
                                    </TableActions>
                                </td>
                            </tr>
                        )) : (
                            <tr><TableListEmpty colSpan={columns.length}>Nenhuma categoria encontrada.</TableListEmpty></tr>
                        )}
                    </TableListTable>
                </TableListScroll>
                <TableListFooter
                    page={categories}
                    disabled={list.loading}
                    paginationLabel="Paginação de categorias de barreiras"
                    onSelect={(page) => list.visit(filterValues, page)}
                />
            </TableListCard>
            <ConfirmActionModal
                open={removal.target !== null}
                title="Excluir Categoria de Barreira"
                message={<>A categoria <strong>{removal.target?.name}</strong> será excluída permanentemente.</>}
                processing={removal.processing}
                confirmLabel="Confirmar exclusão"
                processingLabel="Excluindo categoria..."
                confirmTone="dark-red"
                onClose={removal.close}
                onConfirm={() => {
                    if (removal.target) removal.submit("delete", `${base}/${removal.target.id}`);
                }}
            />
        </BarrierCategoryPage>
    );
}
