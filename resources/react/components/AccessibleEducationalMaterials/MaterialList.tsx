import TableActions from '@/components/Tables/TableActions/TableActions';
import {
    TableListEmpty,
    TableListFooter,
    TableListScroll,
    TableListTable,
    type TableListColumn,
} from '@/components/Tables/TableList/TableList';
import Button from '@/components/UI/Button/Button';
import TableLabel from '@/components/UI/TableLabel/TableLabel';
import {
    materialsBase,
    type AccessibleEducationalMaterial,
    type MaterialPaginator,
    type MaterialPermissions,
} from './AccessibleEducationalMaterials.types';

const materialColumns: TableListColumn[] = [
    {
        id: 'name',
        label: 'Nome',
        width: '35%',
        minWidth: 120,
        fixed: 'start',
    },
    { id: 'nature', label: 'Natureza', width: '15%', minWidth: 72 },
    { id: 'stock', label: 'Estoque', width: '15%', minWidth: 72 },
    { id: 'status', label: 'Status', width: '15%', minWidth: 72 },
    {
        id: 'actions',
        label: 'Ações',
        width: '20%',
        minWidth: 92,
        fixed: 'end',
    },
];

interface MaterialListProps {
    materials: MaterialPaginator;
    permissions: MaterialPermissions;
    processing: boolean;
    onDelete: (material: AccessibleEducationalMaterial) => void;
    onPageSelect: (page: number) => void;
}

export default function MaterialList({
    materials,
    permissions,
    processing,
    onDelete,
    onPageSelect,
}: MaterialListProps): React.JSX.Element {
    return (
        <>
            <TableListScroll aria-label="Tabela de materiais pedagógicos acessíveis">
                <TableListTable
                    className="materialList"
                    columns={materialColumns}
                    summary={
                        processing
                            ? 'Processando…'
                            : `${materials.from ?? 0}–${materials.to ?? 0} de ${materials.total} registros`
                    }
                >
                    {materials.data.map((material) => {
                        const exhausted =
                            !material.is_digital &&
                            (material.quantity_available ?? 0) <= 0;

                        return (
                            <tr key={material.id}>
                                <td>{material.name}</td>
                                <td>
                                    {material.is_digital ? 'Digital' : 'Físico'}
                                </td>
                                <td>
                                    {material.is_digital
                                        ? 'Ilimitado'
                                        : `${material.quantity_available ?? 0} / ${material.quantity ?? 0}`}
                                </td>
                                <td>
                                    <TableLabel
                                        tone={
                                            exhausted
                                                ? 'danger'
                                                : material.is_active
                                                    ? 'success'
                                                    : 'inverse'
                                        }
                                    >
                                        {exhausted
                                            ? 'Esgotado'
                                            : material.is_active
                                                ? 'Ativo'
                                                : 'Inativo'}
                                    </TableLabel>
                                </td>
                                <td>
                                    <TableActions>
                                        {permissions.show ? (
                                            <Button
                                                href={`${materialsBase}/${material.id}`}
                                                size="sm"
                                                tone="dark-blue"
                                                iconOnly
                                                leftIconClass="fa fa-eye"
                                                aria-label={`Ver ${material.name}`}
                                            />
                                        ) : null}
                                        {permissions.destroy ? (
                                            <Button
                                                size="sm"
                                                tone="dark-red"
                                                iconOnly
                                                leftIconClass="fa fa-trash-o"
                                                disabled={processing}
                                                aria-label={`Excluir ${material.name}`}
                                                onClick={() => onDelete(material)}
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
                    {!materials.data.length ? (
                        <tr>
                            <TableListEmpty colSpan={5}>
                                Nenhum material pedagógico cadastrado.
                            </TableListEmpty>
                        </tr>
                    ) : null}
                </TableListTable>
            </TableListScroll>
            <TableListFooter
                summary={`${materials.from ?? 0}–${materials.to ?? 0} de ${materials.total}`}
                currentPage={materials.current_page}
                pageCount={materials.last_page}
                disabled={processing}
                paginationLabel="Páginas de materiais pedagógicos acessíveis"
                onSelect={onPageSelect}
            />
        </>
    );
}
