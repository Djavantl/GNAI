import {
  TableListCard,
  TableListCreateButton,
} from '@/components/Tables/TableList/TableList';
import StudentPage from '@/components/Students/StudentPage';
import StudentFilters from '@/components/Students/StudentFilters';
import StudentList from '@/components/Students/StudentList';
import type { StudentIndexProps } from '@/components/Students/Students.types';
import { studentsBase } from '@/components/Students/Students.utils';

export default function StudentsIndex(props: StudentIndexProps): React.JSX.Element {
  const createAction = props.permissions['student.create'] ? (
    <TableListCreateButton
      href={`${studentsBase}/create`}
      ariaLabel="Cadastrar aluno"
    />
  ) : undefined;

  return (
    <StudentPage
      title="Alunos"
      subtitle="Gerencie os estudantes e seus documentos de apoio especializado."
      actions={createAction}
    >
      <TableListCard aria-label="Listagem de alunos">
        <StudentFilters {...props} />
        <StudentList {...props} />
      </TableListCard>
    </StudentPage>
  );
}
