import FormSection from '@/components/Forms/FormSection/FormSection';
import TableActions from '@/components/Tables/TableActions/TableActions';
import {
  TableListEmpty,
  TableListScroll,
  TableListTable,
  type TableListColumn,
} from '@/components/Tables/TableList/TableList';
import Button from '@/components/UI/Button/Button';
import TableLabel from '@/components/UI/TableLabel/TableLabel';
import type { InspectionHistoryItem } from './InspectionHistoryTable.types';
import './InspectionHistoryTable.css';

const inspectionColumns: TableListColumn[] = [
  {
    id: 'date',
    label: 'Data',
    width: '12%',
    minWidth: 82,
    fixed: 'start',
  },
  { id: 'type', label: 'Tipo', width: '18%', minWidth: 80 },
  {
    id: 'state',
    label: 'Estado de conservação',
    width: '18%',
    minWidth: 100,
  },
  { id: 'description', label: 'Descrição', width: '29%', minWidth: 120 },
  { id: 'evidence', label: 'Evidências', width: '15%', minWidth: 80 },
  {
    id: 'actions',
    label: 'Ações',
    width: '8%',
    minWidth: 72,
    fixed: 'end',
  },
];

interface InspectionHistoryTableProps {
  id: string;
  baseUrl: string;
  inspections: InspectionHistoryItem[];
}

export default function InspectionHistoryTable({
  id,
  baseUrl,
  inspections,
}: InspectionHistoryTableProps): React.JSX.Element {
  return (
    <FormSection
      id={id}
      title="Histórico de Vistorias"
      variant="inline"
      iconClass="fa fa-history"
    >
      <TableListScroll aria-label="Histórico de vistorias">
        <TableListTable
          className="inspectionHistoryTable__table"
          columns={inspectionColumns}
          summary={`${inspections.length} registro(s)`}
        >
          {inspections.length ? (
            inspections.map((inspection) => (
              <tr key={inspection.id}>
                <td>
                  <TableLabel tone="purple">{inspection.date}</TableLabel>
                </td>
                <td>{inspection.type}</td>
                <td>{inspection.state}</td>
                <td>
                  {inspection.descriptionHtml ? (
                    <div
                      className="inspectionHistoryTable__description"
                      dangerouslySetInnerHTML={{
                        __html: inspection.descriptionHtml,
                      }}
                    />
                  ) : (
                    'Não informada'
                  )}
                </td>
                <td>
                  {inspection.evidenceCount
                    ? `${inspection.evidenceCount} registrada(s)`
                    : 'Nenhuma'}
                </td>
                <td>
                  <TableActions>
                    <Button
                      href={`${baseUrl}/inspection/${inspection.id}`}
                      size="sm"
                      tone="dark-blue"
                      iconOnly
                      leftIconClass="fa fa-eye"
                      aria-label={`Visualizar vistoria de ${inspection.date}`}
                    />
                  </TableActions>
                </td>
              </tr>
            ))
          ) : (
            <tr>
              <TableListEmpty colSpan={6}>
                Nenhum histórico de vistoria encontrado.
              </TableListEmpty>
            </tr>
          )}
        </TableListTable>
      </TableListScroll>
    </FormSection>
  );
}
