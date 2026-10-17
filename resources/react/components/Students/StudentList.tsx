import { useState } from 'react';
import { router } from '@inertiajs/react';
import TableActions from '@/components/Tables/TableActions/TableActions';
import {
  TableListEmpty,
  TableListFooter,
  TableListProgress,
  TableListScroll,
  TableListTable,
} from '@/components/Tables/TableList/TableList';
import Button from '@/components/UI/Button/Button';
import TableLabel from '@/components/UI/TableLabel/TableLabel';
import InlineAlert from '@/components/UI/InlineAlert/InlineAlert';
import type { StudentIndexProps } from './Students.types';
import { dateLabel, studentsBase, studentUrl } from './Students.utils';
import { useStudentRequest } from './useStudentRequest';
import './StudentList.css';

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
      <TableListProgress>
        {processing
          ? 'Carregando página...'
          : `${students.total} aluno(s) encontrado(s)`}
      </TableListProgress>
      <TableListScroll aria-label="Tabela de alunos">
        <TableListTable className="studentsPage__indexTable">
          <thead>
            <tr>
              <th scope="col">Nome</th>
              <th scope="col">Telefone</th>
              <th scope="col">E-mail</th>
              <th scope="col">Status</th>
              <th scope="col">Ingresso</th>
              <th scope="col">Ações</th>
            </tr>
          </thead>
          <tbody>
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
          </tbody>
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
