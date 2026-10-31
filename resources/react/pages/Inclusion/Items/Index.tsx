import { usePage } from '@inertiajs/react';
import { useMemo } from 'react';
import BusinessPage from '@/components/Navigation/BusinessPage/BusinessPage';
import {
    TableListCard,
    TableListCreateButton,
    useTableListAction,
    useTableListVisit,
} from '@/components/Tables/TableList/TableList';
import ConfirmActionModal from '@/components/UI/ConfirmActionModal/ConfirmActionModal';
import useAutoListFilters from '@/hooks/useAutoListFilters';
import ItemFilters from './ItemFilters';
import ItemList from './ItemList';
import {
    emptyItemFilters,
    itemsBase,
    type ItemFilters as Filters,
    type ItemIndexProps,
    type ItemSummary,
} from './Items.types';

export default function InclusionItemsIndexPage({
    items,
    filters: initialFilters,
    itemTypeOptions,
    accessibilityFeatureOptions,
    can,
}: ItemIndexProps): React.JSX.Element {
    const { errors } = usePage().props;
    const list = useTableListVisit({
        url: itemsBase,
        failureText: 'Não foi possível concluir a operação. Tente novamente.',
    });
    const removal = useTableListAction<ItemSummary>({
        failureText: 'Não foi possível concluir a exclusão. Tente novamente.',
    });
    const initialValues = useMemo(
        () => ({ ...emptyItemFilters, ...initialFilters }),
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
        emptyValues: emptyItemFilters,
        onSearch: (values: Filters) => list.visit(values),
    });

    function selectPage(page: number): void {
        cancelScheduled();
        const params = new URLSearchParams(window.location.search);
        params.set('page', String(page));
        list.visitUrl(`${itemsBase}?${params.toString()}`);
    }

    return (
        <BusinessPage
            title="Itens"
            subtitle="Gestão de tecnologias assistivas e materiais pedagógicos acessíveis."
            breadcrumbItems={[
                { label: 'Home', href: '/auth/dashboard' },
                { label: 'Itens' },
            ]}
            actions={
                can.create ? (
                    <TableListCreateButton
                        href={`${itemsBase}/create`}
                        ariaLabel="Cadastrar item"
                    />
                ) : undefined
            }
            className="itemIndex"
        >
            <TableListCard
                aria-label="Listagem de itens"
                aria-busy={list.loading}
                loading={list.loading}
                loadingLabel="Atualizando itens"
            >
                <ItemFilters
                    values={filters}
                    itemTypeOptions={itemTypeOptions}
                    accessibilityFeatureOptions={accessibilityFeatureOptions}
                    errors={errors}
                    onUpdate={updateFilter}
                    onClear={clearFilters}
                    onSubmit={submitFilters}
                />
                <ItemList
                    items={items}
                    permissions={can}
                    loading={list.loading}
                    processing={removal.processing}
                    onDelete={removal.open}
                    onPageSelect={selectPage}
                />
            </TableListCard>
            <ConfirmActionModal
                open={removal.target !== null}
                title="Excluir Item"
                message={
                    <>
                        Excluir o item <strong>{removal.target?.name}</strong>?
                    </>
                }
                processing={removal.processing}
                confirmLabel="Confirmar exclusão"
                processingLabel="Excluindo item..."
                confirmTone="dark-red"
                onClose={removal.close}
                onConfirm={() => {
                    if (!removal.target) {
                        return;
                    }

                    cancelScheduled();
                    removal.submit(
                        'delete',
                        `${itemsBase}/${removal.target.id}`,
                    );
                }}
            />
        </BusinessPage>
    );
}
