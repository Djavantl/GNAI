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
import AccessibilityFeaturePage from "./AccessibilityFeaturePage";
import type {
    AccessibilityFeaturePaginator,
    AccessibilityFeatureRecord,
} from "./AccessibilityFeature.types";
import "./AccessibilityFeatures.css";

type Filters = { name: string; is_active: string };
type Props = {
    features: AccessibilityFeaturePaginator;
    filters: Partial<Filters>;
    can: { create: boolean; show: boolean; destroy: boolean };
};

const base = "/inclusive-radar/accessibility-features";
const emptyFilters: Filters = { name: "", is_active: "" };
const columns: TableListColumn[] = [
    { id: "name", label: "Nome", width: "56%", minWidth: 150, fixed: "start" },
    { id: "status", label: "Status", width: "24%", minWidth: 90 },
    { id: "actions", label: "Ações", width: "20%", minWidth: 86, fixed: "end" },
];

export default function InclusiveRadarAccessibilityFeaturesIndexPage({
    features,
    filters: initialFilters,
    can,
}: Props): React.JSX.Element {
    const pageErrors = usePage().props.errors as Record<string, string | undefined>;
    const [busy, setBusy] = useState(false);
    const [listLoading, setListLoading] = useState(false);
    const [failure, setFailure] = useState("");
    const [featureToDelete, setFeatureToDelete] = useState<AccessibilityFeatureRecord | null>(null);
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
                setFailure("Não foi possível atualizar os recursos de acessibilidade. Tente novamente.");
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
        if (pending.current || !featureToDelete) return;
        cancelScheduled();
        pending.current = true;
        setBusy(true);
        setFailure("");
        router.delete(`${base}/${featureToDelete.id}`, {
            preserveScroll: true,
            onNetworkError: () => {
                setFailure("Falha de conexão. Confira sua conexão e tente novamente.");
                return false;
            },
            onHttpException: () => {
                setFailure("Não foi possível excluir o recurso. Tente novamente.");
                return false;
            },
            onSuccess: () => setFeatureToDelete(null),
            onFinish: () => {
                pending.current = false;
                setBusy(false);
            },
        });
    }

    return (
        <AccessibilityFeaturePage
            title="Recursos de Acessibilidade"
            subtitle="Gerencie as categorias de recursos de acessibilidade disponíveis."
            actions={can.create ? (
                <TableListCreateButton href={`${base}/create`} ariaLabel="Cadastrar recurso de acessibilidade" />
            ) : undefined}
        >
            <TableListCard aria-label="Listagem de recursos de acessibilidade" aria-busy={busy} loading={listLoading} loadingLabel="Atualizando recursos de acessibilidade">
                <FormLayout onSubmit={(event) => { event.preventDefault(); submitFilters(); }}>
                    <div className="accessibilityFeatureIndex__filters">
                        <FormField id="accessibility-feature-filter-name" label="Nome" error={pageErrors.name}>
                            <TextInput
                                id="accessibility-feature-filter-name"
                                name="name"
                                value={filterValues.name}
                                maxLength={255}
                                placeholder="Filtrar por nome..."
                                disabled={busy}
                                invalid={!!pageErrors.name}
                                aria-describedby={pageErrors.name ? "accessibility-feature-filter-name-error" : undefined}
                                onChange={(event) => updateFilter("name", event.target.value)}
                            />
                        </FormField>
                        <FormField id="accessibility-feature-filter-status" label="Status" error={pageErrors.is_active}>
                            <SelectField
                                id="accessibility-feature-filter-status"
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
                                aria-describedby={pageErrors.is_active ? "accessibility-feature-filter-status-error" : undefined}
                                onChange={(event) => updateFilter("is_active", event.target.value)}
                            />
                        </FormField>
                        <TableListFilterActions>
                            <TableListClearButton disabled={busy} onClick={clearFilters} />
                        </TableListFilterActions>
                    </div>
                </FormLayout>
                <TableListScroll aria-label="Tabela de recursos de acessibilidade">
                    <TableListTable
                        className="accessibilityFeatureIndex__table"
                        columns={columns}
                        summary={`${features.from ?? 0}–${features.to ?? 0} de ${features.total} registros`}
                    >
                        {features.data.length ? features.data.map((feature) => (
                            <tr key={feature.id}>
                                <td>{feature.name}</td>
                                <td>
                                    <TableLabel tone={feature.is_active ? "success" : "inverse"}>
                                        {feature.is_active ? "Ativo" : "Inativo"}
                                    </TableLabel>
                                </td>
                                <td>
                                    <TableActions>
                                        {can.show ? (
                                            <Button
                                                href={`${base}/${feature.id}`}
                                                size="sm"
                                                tone="dark-blue"
                                                iconOnly
                                                leftIconClass="fa fa-eye"
                                                aria-label={`Visualizar ${feature.name}`}
                                            />
                                        ) : null}
                                        {can.destroy ? (
                                            <Button
                                                tone="dark-red"
                                                size="sm"
                                                iconOnly
                                                leftIconClass="fa fa-trash-o"
                                                aria-label={`Excluir ${feature.name}`}
                                                onClick={() => {
                                                    cancelScheduled();
                                                    setFailure("");
                                                    setFeatureToDelete(feature);
                                                }}
                                            />
                                        ) : null}
                                        {!can.show && !can.destroy ? <span>—</span> : null}
                                    </TableActions>
                                </td>
                            </tr>
                        )) : (
                            <tr><TableListEmpty colSpan={3}>Nenhum recurso de acessibilidade cadastrado.</TableListEmpty></tr>
                        )}
                    </TableListTable>
                </TableListScroll>
                <TableListFooter
                    summary={`${features.total} recurso(s) de acessibilidade`}
                    currentPage={features.current_page}
                    pageCount={features.last_page}
                    disabled={busy}
                    paginationLabel="Paginação de recursos de acessibilidade"
                    onSelect={(page) => visit(filterValues, page)}
                />
            </TableListCard>
            <ConfirmActionModal
                open={featureToDelete !== null}
                title="Excluir Recurso de Acessibilidade"
                message={<>O recurso <strong>{featureToDelete?.name}</strong> será excluído permanentemente.</>}
                processing={busy}
                confirmLabel="Confirmar exclusão"
                processingLabel="Excluindo recurso..."
                confirmTone="dark-red"
                error={failure || undefined}
                onClose={() => {
                    if (!busy) {
                        setFeatureToDelete(null);
                        setFailure("");
                    }
                }}
                onConfirm={remove}
            />
        </AccessibilityFeaturePage>
    );
}
