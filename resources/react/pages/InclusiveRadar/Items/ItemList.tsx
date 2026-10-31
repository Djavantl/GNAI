import TableActions from '@/components/Tables/TableActions/TableActions';
import {
    formatTableListDate,
    TableListEmpty,
    TableListFooter,
    TableListScroll,
    TableListTable,
    type TableListColumn,
} from '@/components/Tables/TableList/TableList';
import Button from '@/components/UI/Button/Button';
import TableLabel from '@/components/UI/TableLabel/TableLabel';
import {
    itemsBase,
    itemTypeLabels,
    type ItemPaginator,
    type ItemPermissions,
    type ItemSummary,
} from './Items.types';

const itemColumns: TableListColumn[] = [
    {
        id: 'name',
        label: 'Descrição',
        width: '28%',
        minWidth: 120,
        fixed: 'start',
    },
    { id: 'type', label: 'Tipo', width: '20%', minWidth: 110 },
    { id: 'nature', label: 'Natureza', width: '12%', minWidth: 72 },
    { id: 'stock', label: 'Estoque', width: '12%', minWidth: 72 },
    { id: 'status', label: 'Status', width: '12%', minWidth: 72 },
    { id: 'asset', label: 'Patrimônio / Tombamento', width: '16%', defaultVisible: false },
    { id: 'quantity', label: 'Quantidade total', width: '16%', defaultVisible: false },
    { id: 'available', label: 'Quantidade disponível', width: '16%', defaultVisible: false },
    { id: 'conservation', label: 'Conservação', width: '16%', defaultVisible: false },
    { id: 'resourceStatus', label: 'Status do recurso', width: '16%', defaultVisible: false },
    { id: 'loanable', label: 'Permite empréstimos', width: '16%', defaultVisible: false },
    { id: 'audience', label: 'Público-alvo', width: '16%', defaultVisible: false },
    { id: 'features', label: 'Recursos presentes no item', width: '16%', defaultVisible: false },
    { id: 'created', label: 'Criado em', width: '16%', defaultVisible: false },
    { id: 'updated', label: 'Atualizado em', width: '16%', defaultVisible: false },
    {
        id: 'actions',
        label: 'Ações',
        width: '16%',
        minWidth: 92,
        fixed: 'end',
    },
];

interface ItemListProps {
    items: ItemPaginator;
    permissions: ItemPermissions;
    loading: boolean;
    processing: boolean;
    onDelete: (item: ItemSummary) => void;
    onPageSelect: (page: number) => void;
}

export default function ItemList({
    items,
    permissions,
    loading,
    processing,
    onDelete,
    onPageSelect,
}: ItemListProps): React.JSX.Element {
    return (
        <>
            <TableListScroll aria-label="Tabela de itens">
                <TableListTable
                    className="itemList"
                    columns={itemColumns}
                    page={items}
                >
                    {items.data.map((item) => {
                        const exhausted =
                            !item.is_digital &&
                            (item.quantity_available ?? 0) <= 0;

                        return (
                            <tr key={item.id}>
                                <td>{item.name}</td>
                                <td>{itemTypeLabels[item.item_type]}</td>
                                <td>
                                    {item.is_digital ? 'Digital' : 'Físico'}
                                </td>
                                <td>
                                    {item.is_digital
                                        ? 'Ilimitado'
                                        : `${item.quantity_available ?? 0} / ${item.quantity ?? 0}`}
                                </td>
                                <td>
                                    <TableLabel
                                        tone={
                                            exhausted
                                                ? 'danger'
                                                : item.is_active
                                                    ? 'success'
                                                    : 'inverse'
                                        }
                                    >
                                        {exhausted
                                            ? 'Esgotado'
                                            : item.is_active
                                                ? 'Ativo'
                                                : 'Inativo'}
                                    </TableLabel>
                                </td>
                                <td>{item.asset_code || 'Não informado'}</td>
                                <td>{item.is_digital ? 'Não se aplica' : (item.quantity ?? 0)}</td>
                                <td>{item.is_digital ? 'Não se aplica' : (item.quantity_available ?? 0)}</td>
                                <td>{item.conservation_state_label || 'Não informado'}</td>
                                <td>{item.resource_status_label || 'Não informado'}</td>
                                <td>{item.is_loanable ? 'Sim' : 'Não'}</td>
                                <td>
                                    {item.deficiencies.map((entry) => entry.name).join(', ') || 'Não informado'}
                                </td>
                                <td>
                                    {item.accessibility_features.map((entry) => entry.name).join(', ') || 'Não informado'}
                                </td>
                                <td>{formatTableListDate(item.created_at, true)}</td>
                                <td>{formatTableListDate(item.updated_at, true)}</td>
                                <td>
                                    <TableActions>
                                        {permissions.show ? (
                                            <Button
                                                href={`${itemsBase}/${item.id}`}
                                                size="sm"
                                                tone="dark-blue"
                                                iconOnly
                                                leftIconClass="fa fa-eye"
                                                aria-label={`Visualizar ${item.name}`}
                                            />
                                        ) : null}
                                        {permissions.destroy ? (
                                            <Button
                                                size="sm"
                                                tone="dark-red"
                                                iconOnly
                                                leftIconClass="fa fa-trash-o"
                                                disabled={processing}
                                                aria-label={`Excluir ${item.name}`}
                                                onClick={() => onDelete(item)}
                                            />
                                        ) : null}
                                        {!permissions.show && !permissions.destroy ? (
                                            <span>Nenhuma ação</span>
                                        ) : null}
                                    </TableActions>
                                </td>
                            </tr>
                        );
                    })}
                    {!items.data.length ? (
                        <tr>
                            <TableListEmpty colSpan={itemColumns.length}>
                                Nenhum item cadastrado.
                            </TableListEmpty>
                        </tr>
                    ) : null}
                </TableListTable>
            </TableListScroll>
            <TableListFooter
                page={items}
                disabled={loading}
                paginationLabel="Páginas de itens"
                onSelect={onPageSelect}
            />
        </>
    );
}
