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
import DeleteStudent from './DeleteStudent';
import type { StudentIndexProps } from './Students.types';
import { dateLabel, studentUrl } from './Students.utils';

const studentColumns: TableListColumn[] = [
  {
    id: 'name',
    label: 'Nome',
    width: '25%',
    minWidth: 120,
    fixed: 'start',
  },
  { id: 'phone', label: 'Telefone', width: '15%', minWidth: 72 },
  { id: 'email', label: 'E-mail', width: '25%', minWidth: 100 },
  { id: 'status', label: 'Status', width: '10%', minWidth: 72 },
  { id: 'entry', label: 'Ingresso', width: '10%', minWidth: 72 },
  { id: 'registration', label: 'Matrícula', width: '16%', defaultVisible: false },
  { id: 'document', label: 'CPF', width: '16%', defaultVisible: false },
  { id: 'birth', label: 'Nascimento', width: '16%', defaultVisible: false },
  { id: 'age', label: 'Idade', width: '16%', defaultVisible: false },
  { id: 'gender', label: 'Gênero', width: '16%', defaultVisible: false },
  {
    id: 'actions',
    label: 'Ações',
    width: '15%',
    minWidth: 104,
    fixed: 'end',
  },
];

export default function StudentList({
  students,
  permissions,
  studentStatuses,
  statusColors,
  loading,
  onPageChange,
}: Pick<
  StudentIndexProps,
  'students' | 'permissions' | 'studentStatuses' | 'statusColors'
> & {
  loading: boolean;
  onPageChange: (page: number) => void;
}): React.JSX.Element {
  const canView = permissions['student.view'];
  const canDelete = permissions['student.delete'];

  return (
    <div aria-busy={loading}>
      <TableListScroll aria-label="Tabela de alunos">
        <TableListTable
          className="studentsPage__indexTable"
          columns={studentColumns}
          page={students}
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
                <td>{student.registration || 'Não informado'}</td>
                <td>{student.person.document || 'Não informado'}</td>
                <td>{formatTableListDate(student.person.birth_date)}</td>
                <td>{student.person.age ?? 'Não informado'}</td>
                <td>{student.person.gender_label || 'Não informado'}</td>
                <td>
                  {canView || canDelete ? (
                    <TableActions>
                      {canView ? (
                        <Button
                          href={studentUrl(student.id)}
                          size="sm"
                          tone="dark-blue"
                          iconOnly
                          leftIconClass="fa fa-eye"
                          aria-label={`Visualizar aluno ${student.person.name}`}
                        />
                      ) : null}
                      {canDelete ? (
                        <DeleteStudent
                          id={student.id}
                          name={student.person.name}
                          iconOnly
                        />
                      ) : null}
                    </TableActions>
                  ) : (
                    'Nenhuma ação'
                  )}
                </td>
              </tr>
            ))
          ) : (
            <tr>
              <TableListEmpty colSpan={studentColumns.length}>
                Nenhum aluno encontrado.
              </TableListEmpty>
            </tr>
          )}
        </TableListTable>
      </TableListScroll>
      <TableListFooter
        page={students}
        disabled={loading}
        paginationLabel="Páginas de alunos"
        onSelect={onPageChange}
      />
    </div>
  );
}
