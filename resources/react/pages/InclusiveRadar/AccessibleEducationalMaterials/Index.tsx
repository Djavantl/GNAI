import { usePage } from '@inertiajs/react';
import { useMemo } from 'react';
import MaterialFilters from '@/components/AccessibleEducationalMaterials/MaterialFilters';
import MaterialList from '@/components/AccessibleEducationalMaterials/MaterialList';
import {
    emptyMaterialFilters,
    materialsBase,
    type AccessibleEducationalMaterial,
    type MaterialFilters as Filters,
    type MaterialIndexProps,
} from '@/components/AccessibleEducationalMaterials/AccessibleEducationalMaterials.types';
import BusinessPage from '@/components/Navigation/BusinessPage/BusinessPage';
import {
    TableListCard,
    TableListCreateButton,
    useTableListAction,
    useTableListVisit,
} from '@/components/Tables/TableList/TableList';
import ConfirmActionModal from '@/components/UI/ConfirmActionModal/ConfirmActionModal';
import useAutoListFilters from '@/hooks/useAutoListFilters';

export default function InclusiveRadarAccessibleEducationalMaterialsIndexPage({
    materials,
    filters: initialFilters,
    accessibilityFeatureOptions,
    can,
}: MaterialIndexProps): React.JSX.Element {
    const { errors } = usePage().props;
    const list = useTableListVisit({
        url: materialsBase,
        failureText: 'Não foi possível concluir a operação. Tente novamente.',
    });
    const removal = useTableListAction<AccessibleEducationalMaterial>({
        failureText: 'Não foi possível concluir a exclusão. Tente novamente.',
    });
    const initialValues = useMemo(
        () => ({ ...emptyMaterialFilters, ...initialFilters }),
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
        emptyValues: emptyMaterialFilters,
        onSearch: (values: Filters) => list.visit(values),
    });

    function selectPage(page: number): void {
        cancelScheduled();
        const params = new URLSearchParams(window.location.search);
        params.set('page', String(page));
        list.visitUrl(`${materialsBase}?${params.toString()}`);
    }

    return (
        <BusinessPage
            title="Materiais Pedagógicos Acessíveis"
            subtitle="Gestão de recursos didáticos, livros e jogos adaptados."
            breadcrumbItems={[
                { label: 'Home', href: '/auth/dashboard' },
                { label: 'Materiais Pedagógicos Acessíveis' },
            ]}
            actions={
                can.create ? (
                    <TableListCreateButton
                        href={`${materialsBase}/create`}
                        ariaLabel="Cadastrar material pedagógico acessível"
                    />
                ) : undefined
            }
            className="materialIndex"
        >
            <TableListCard
                aria-label="Listagem de materiais pedagógicos acessíveis"
                aria-busy={list.loading}
                loading={list.loading}
                loadingLabel="Atualizando materiais pedagógicos acessíveis"
            >
                <MaterialFilters
                    values={filters}
                    accessibilityFeatureOptions={accessibilityFeatureOptions}
                    errors={errors}
                    onUpdate={updateFilter}
                    onClear={clearFilters}
                    onSubmit={submitFilters}
                />
                <MaterialList
                    materials={materials}
                    permissions={can}
                    loading={list.loading}
                    processing={removal.processing}
                    onDelete={removal.open}
                    onPageSelect={selectPage}
                />
            </TableListCard>
            <ConfirmActionModal
                open={removal.target !== null}
                title="Excluir Material Pedagógico"
                message={
                    <>
                        Excluir o material{' '}
                        <strong>{removal.target?.name}</strong>?
                    </>
                }
                processing={removal.processing}
                confirmLabel="Confirmar exclusão"
                processingLabel="Excluindo material..."
                confirmTone="dark-red"
                onClose={removal.close}
                onConfirm={() => {
                    if (!removal.target) {
                        return;
                    }

                    cancelScheduled();
                    removal.submit(
                        'delete',
                        `${materialsBase}/${removal.target.id}`,
                    );
                }}
            />
        </BusinessPage>
    );
}
