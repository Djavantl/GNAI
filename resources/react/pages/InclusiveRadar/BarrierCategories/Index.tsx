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
import TableLabel from "@/components/UI/TableLabel/TableLabel";
import { notify } from "@/components/UI/NotificationToast/notify";
import useAutoListFilters, { compactListFilters } from "@/hooks/useAutoListFilters";
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
    { id: "actions", label: "Ações", width: "20%", minWidth: 86, fixed: "end" },
];

export default function InclusiveRadarBarrierCategoriesIndexPage({
    categories,
    filters: initialFilters,
    can,
}: Props): React.JSX.Element {
    const pageErrors = usePage().props.errors as Record<string, string | undefined>;
    const [busy, setBusy] = useState(false);
    const [listLoading, setListLoading] = useState(false);
    const [failure, setFailure] = useState("");
    const [categoryToDelete, setCategoryToDelete] = useState<BarrierCategoryRecord | null>(null);
    const pending = useRef(false);
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

    function visit(values: Filters, page = 1): void {
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
                setFailure("Não foi possível atualizar as categorias de barreiras. Tente novamente.");
                return false;
            },
            onFinish: () => {
                pending.current = false;
                setBusy(false);
                setListLoading(false);
            },
        });
    }

    function remove(): void {
        if (pending.current || !categoryToDelete) return;
        cancelScheduled();
        pending.current = true;
        setBusy(true);
        setFailure("");
        router.delete(`${base}/${categoryToDelete.id}`, {
            preserveScroll: true,
            onNetworkError: () => {
                setFailure("Falha de conexão. Confira sua conexão e tente novamente.");
                return false;
            },
            onHttpException: () => {
                setFailure("Não foi possível excluir a categoria. Tente novamente.");
                return false;
            },
            onSuccess: () => setCategoryToDelete(null),
            onFinish: () => {
                pending.current = false;
                setBusy(false);
            },
        });
    }

    return (
        <BarrierCategoryPage
            title="Categorias de Barreiras"
            subtitle="Classificação para o mapeamento de acessibilidade e identificação de obstáculos."
            actions={can.create ? (
                <TableListCreateButton href={`${base}/create`} ariaLabel="Cadastrar categoria de barreira" />
            ) : undefined}
        >
            <TableListCard aria-label="Listagem de categorias de barreiras" aria-busy={busy} loading={listLoading} loadingLabel="Atualizando categorias de barreiras">
                <FormLayout onSubmit={(event) => { event.preventDefault(); submitFilters(); }}>
                    <div className="barrierCategoryIndex__filters">
                        <FormField id="barrier-category-filter-name" label="Nome da Categoria" error={pageErrors.name}>
                            <TextInput
                                id="barrier-category-filter-name"
                                name="name"
                                value={filterValues.name}
                                maxLength={150}
                                placeholder="Filtrar por nome da categoria..."
                                disabled={busy}
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
                                disabled={busy}
                                aria-invalid={!!pageErrors.is_active}
                                aria-describedby={pageErrors.is_active ? "barrier-category-filter-status-error" : undefined}
                                onChange={(event) => updateFilter("is_active", event.target.value)}
                            />
                        </FormField>
                        <TableListFilterActions>
                            <TableListClearButton disabled={busy} onClick={clearFilters} />
                        </TableListFilterActions>
                    </div>
                </FormLayout>
                <TableListScroll aria-label="Tabela de categorias de barreiras">
                    <TableListTable
                        className="barrierCategoryIndex__table"
                        columns={columns}
                        summary={`${categories.from ?? 0}–${categories.to ?? 0} de ${categories.total} registros`}
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
                                                    setFailure("");
                                                    setCategoryToDelete(category);
                                                }}
                                            />
                                        ) : null}
                                        {!can.show && !can.destroy ? <span>—</span> : null}
                                    </TableActions>
                                </td>
                            </tr>
                        )) : (
                            <tr><TableListEmpty colSpan={4}>Nenhuma categoria encontrada.</TableListEmpty></tr>
                        )}
                    </TableListTable>
                </TableListScroll>
                <TableListFooter
                    summary={`${categories.total} categoria(s) de barreira`}
                    currentPage={categories.current_page}
                    pageCount={categories.last_page}
                    disabled={busy}
                    paginationLabel="Paginação de categorias de barreiras"
                    onSelect={(page) => visit(filterValues, page)}
                />
            </TableListCard>
            <ConfirmActionModal
                open={categoryToDelete !== null}
                title="Excluir Categoria de Barreira"
                message={<>A categoria <strong>{categoryToDelete?.name}</strong> será excluída permanentemente.</>}
                processing={busy}
                confirmLabel="Confirmar exclusão"
                processingLabel="Excluindo categoria..."
                confirmTone="dark-red"
                error={failure || undefined}
                onClose={() => {
                    if (!busy) {
                        setCategoryToDelete(null);
                        setFailure("");
                    }
                }}
                onConfirm={remove}
            />
        </BarrierCategoryPage>
    );
}
