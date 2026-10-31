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
    { id: "description", label: "Descrição", width: "16%", defaultVisible: false },
    { id: "actions", label: "Ações", width: "20%", minWidth: 86, fixed: "end" },
];

export default function InclusionAccessibilityFeaturesIndexPage({
    features,
    filters: initialFilters,
    can,
}: Props): React.JSX.Element {
    const pageErrors = usePage().props.errors as Record<string, string | undefined>;
    const list = useTableListVisit({
        url: base,
        failureText: "Não foi possível atualizar os recursos de acessibilidade. Tente novamente.",
    });
    const removal = useTableListAction<AccessibilityFeatureRecord>({
        failureText: "Não foi possível excluir o recurso. Tente novamente.",
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
        <AccessibilityFeaturePage
            title="Recursos de Acessibilidade"
            subtitle="Gerencie as categorias de recursos de acessibilidade disponíveis."
            actions={can.create ? (
                <TableListCreateButton href={`${base}/create`} ariaLabel="Cadastrar recurso de acessibilidade" />
            ) : undefined}
        >
            <TableListCard aria-label="Listagem de recursos de acessibilidade" aria-busy={list.loading} loading={list.loading} loadingLabel="Atualizando recursos de acessibilidade">
                <FormLayout onSubmit={(event) => { event.preventDefault(); submitFilters(); }}>
                    <div className="accessibilityFeatureIndex__filters">
                        <FormField id="accessibility-feature-filter-name" label="Nome" error={pageErrors.name}>
                            <TextInput
                                id="accessibility-feature-filter-name"
                                name="name"
                                value={filterValues.name}
                                maxLength={255}
                                placeholder="Filtrar por nome..."
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
                                aria-invalid={!!pageErrors.is_active}
                                aria-describedby={pageErrors.is_active ? "accessibility-feature-filter-status-error" : undefined}
                                onChange={(event) => updateFilter("is_active", event.target.value)}
                            />
                        </FormField>
                        <TableListFilterActions>
                            <TableListClearButton onClick={clearFilters} />
                        </TableListFilterActions>
                    </div>
                </FormLayout>
                <TableListScroll aria-label="Tabela de recursos de acessibilidade">
                    <TableListTable
                        className="accessibilityFeatureIndex__table"
                        columns={columns}
                        page={features}
                    >
                        {features.data.length ? features.data.map((feature) => (
                            <tr key={feature.id}>
                                <td>{feature.name}</td>
                                <td>
                                    <TableLabel tone={feature.is_active ? "success" : "inverse"}>
                                        {feature.is_active ? "Ativo" : "Inativo"}
                                    </TableLabel>
                                </td>
                                <td>{formatTableListText(feature.description)}</td>
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
                                                    removal.open(feature);
                                                }}
                                            />
                                        ) : null}
                                        {!can.show && !can.destroy ? <span>Sem ações</span> : null}
                                    </TableActions>
                                </td>
                            </tr>
                        )) : (
                            <tr><TableListEmpty colSpan={columns.length}>Nenhum recurso de acessibilidade cadastrado.</TableListEmpty></tr>
                        )}
                    </TableListTable>
                </TableListScroll>
                <TableListFooter
                    page={features}
                    disabled={list.loading}
                    paginationLabel="Paginação de recursos de acessibilidade"
                    onSelect={(page) => list.visit(filterValues, page)}
                />
            </TableListCard>
            <ConfirmActionModal
                open={removal.target !== null}
                title="Excluir Recurso de Acessibilidade"
                message={<>O recurso <strong>{removal.target?.name}</strong> será excluído permanentemente.</>}
                processing={removal.processing}
                confirmLabel="Confirmar exclusão"
                processingLabel="Excluindo recurso..."
                confirmTone="dark-red"
                onClose={removal.close}
                onConfirm={() => {
                    if (removal.target) removal.submit("delete", `${base}/${removal.target.id}`);
                }}
            />
        </AccessibilityFeaturePage>
    );
}
