import { useState } from 'react';
import { router } from '@inertiajs/react';
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
import InlineAlert from '@/components/UI/InlineAlert/InlineAlert';
import type { StudentIndexProps } from './Students.types';
import { dateLabel, studentsBase, studentUrl } from './Students.utils';
import { useStudentRequest } from './useStudentRequest';

const studentColumns: TableListColumn[] = [
  {
    id: 'name',
    label: 'Nome',
    width: '30%',
    minWidth: 120,
    fixed: 'start',
  },
  { id: 'phone', label: 'Telefone', width: '15%', minWidth: 72 },
  { id: 'email', label: 'E-mail', width: '25%', minWidth: 100 },
  { id: 'status', label: 'Status', width: '10%', minWidth: 72 },
  { id: 'entry', label: 'Ingresso', width: '10%', minWidth: 72 },
  {
    id: 'actions',
    label: 'Ações',
    width: '10%',
    minWidth: 76,
    fixed: 'end',
  },
];

export default function StudentList({
  students,
  filters,
  permissions,
  studentStatuses,
  statusColors,
}: StudentIndexProps): React.JSX.Element {
  const [processing, setProcessing] = useState(false);
  const request = useStudentRequest();

  function pageHref(page: number) {
    const query = new URLSearchParams(
      Object.entries(filters)
        .filter(([, value]) => value !== '')
        .map(([key, value]) => [key, String(value)]),
    );
    query.set('page', String(page));
    return `${studentsBase}?${query}`;
  }

  return (
    <div aria-busy={processing}>
      {request.error ? (
        <div role="alert">
          <InlineAlert variant="danger">{request.error}</InlineAlert>
        </div>
      ) : null}
      <TableListScroll aria-label="Tabela de alunos">
        <TableListTable
          className="studentsPage__indexTable"
          columns={studentColumns}
          summary={
            processing
              ? 'Carregando página...'
              : `${students.from ?? 0}–${students.to ?? 0} de ${students.total} registros`
          }
        >
          {students.data.length ? (
            students.data.map((student) => (
              <tr key={student.id}>
                <td>
                  <div className="studentsPage__identity">
                    <img
                      src={student.person.photo_url}
                      alt={`Foto de ${student.person.name}`}
                      className="studentsPage__avatar"
                    />
                    <strong>{student.person.name}</strong>
                  </div>
                </td>
                <td>{student.person.phone || 'Não informado'}</td>
                <td>{student.person.email || 'Não informado'}</td>
                <td>
                  <TableLabel
                    tone={statusColors[student.status] ?? 'inverse'}
                  >
                    {studentStatuses[student.status] ?? student.status}
                  </TableLabel>
                </td>
                <td>{dateLabel(student.entry_date)}</td>
                <td>
                  {permissions['student.view'] ? (
                    <TableActions>
                      <Button
                        href={studentUrl(student.id)}
                        size="sm"
                        tone="dark-blue"
                        iconOnly
                        leftIconClass="fa fa-eye"
                        aria-label={`Ver ficha de ${student.person.name}`}
                      />
                    </TableActions>
                  ) : (
                    'Nenhuma ação'
                  )}
                </td>
              </tr>
            ))
          ) : (
            <tr>
              <TableListEmpty colSpan={6}>
                Nenhum aluno encontrado.
              </TableListEmpty>
            </tr>
          )}
        </TableListTable>
      </TableListScroll>
      <TableListFooter
        summary={`${students.from ?? 0}–${students.to ?? 0} de ${students.total}`}
        currentPage={students.current_page}
        pageCount={students.last_page}
        disabled={processing}
        paginationLabel="Páginas de alunos"
        onSelect={(page) => {
          if (processing) {
            return;
          }

          router.get(
            pageHref(page),
            {},
            {
              ...request.options,
              preserveState: true,
              preserveScroll: true,
              onStart: () => setProcessing(true),
              onFinish: () => setProcessing(false),
            },
          );
        }}
      />
    </div>
  );
}
