import { router, usePage } from '@inertiajs/react';
import { useMemo, useRef, useState } from 'react';
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
} from '@/components/Tables/TableList/TableList';
import ConfirmActionModal from '@/components/UI/ConfirmActionModal/ConfirmActionModal';
import InlineAlert from '@/components/UI/InlineAlert/InlineAlert';
import useAutoListFilters, {
    compactListFilters,
} from '@/hooks/useAutoListFilters';

export default function InclusiveRadarAccessibleEducationalMaterialsIndexPage({
    materials,
    filters: initialFilters,
    accessibilityFeatureOptions,
    can,
}: MaterialIndexProps): React.JSX.Element {
    const { errors } = usePage().props;
    const [busy, setBusy] = useState(false);
    const [failure, setFailure] = useState('');
    const [materialToDelete, setMaterialToDelete] =
        useState<AccessibleEducationalMaterial | null>(null);
    const pending = useRef(false);
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
        onSearch: visit,
    });

    function visit(values: Filters, url = materialsBase): void {
        if (pending.current) {
            return;
        }

        pending.current = true;
        setBusy(true);
        setFailure('');
        router.get(
            url,
            url === materialsBase ? compactListFilters(values) : {},
            {
                preserveState: true,
                preserveScroll: true,
                onError: () =>
                    setFailure(
                        'Não foi possível filtrar os materiais. Confira os campos e tente novamente.',
                    ),
                onHttpException: () => {
                    setFailure(
                        'Não foi possível concluir a operação. Tente novamente.',
                    );
                    return false;
                },
                onNetworkError: () => {
                    setFailure(
                        'Falha de conexão. Confira sua conexão e tente novamente.',
                    );
                    return false;
                },
                onFinish: () => {
                    pending.current = false;
                    setBusy(false);
                },
            },
        );
    }

    function remove(): void {
        if (pending.current || !materialToDelete) {
            return;
        }

        cancelScheduled();
        pending.current = true;
        setBusy(true);
        setFailure('');
        router.delete(`${materialsBase}/${materialToDelete.id}`, {
            preserveScroll: true,
            onError: () =>
                setFailure(
                    'Não foi possível excluir o material. Tente novamente.',
                ),
            onHttpException: () => {
                setFailure(
                    'Não foi possível concluir a exclusão. Tente novamente.',
                );
                return false;
            },
            onNetworkError: () => {
                setFailure(
                    'Falha de conexão. Confira sua conexão e tente novamente.',
                );
                return false;
            },
            onSuccess: () => setMaterialToDelete(null),
            onFinish: () => {
                pending.current = false;
                setBusy(false);
            },
        });
    }

    function selectPage(page: number): void {
        cancelScheduled();
        const params = new URLSearchParams(window.location.search);
        params.set('page', String(page));
        visit(filters, `${materialsBase}?${params.toString()}`);
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
                aria-busy={busy}
            >
                <MaterialFilters
                    values={filters}
                    accessibilityFeatureOptions={accessibilityFeatureOptions}
                    errors={errors}
                    processing={busy}
                    onUpdate={updateFilter}
                    onClear={clearFilters}
                    onSubmit={submitFilters}
                />
                {failure && !materialToDelete ? (
                    <div role="alert">
                        <InlineAlert variant="danger">
                            {failure}
                        </InlineAlert>
                    </div>
                ) : null}
                <MaterialList
                    materials={materials}
                    permissions={can}
                    processing={busy}
                    onDelete={(material) => {
                        setFailure('');
                        setMaterialToDelete(material);
                    }}
                    onPageSelect={selectPage}
                />
            </TableListCard>
            <ConfirmActionModal
                open={materialToDelete !== null}
                title="Excluir Material Pedagógico"
                message={
                    <>
                        Excluir o material{' '}
                        <strong>{materialToDelete?.name}</strong>?
                    </>
                }
                error={failure || undefined}
                processing={busy}
                confirmLabel="Confirmar exclusão"
                processingLabel="Excluindo material..."
                confirmTone="dark-red"
                onClose={() => {
                    setFailure('');
                    setMaterialToDelete(null);
                }}
                onConfirm={remove}
            />
        </BusinessPage>
    );
}
