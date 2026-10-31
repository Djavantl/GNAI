import {
  TableListCard,
  TableListCreateButton,
  useTableListVisit,
} from '@/components/Tables/TableList/TableList';
import StudentPage from '@/components/Students/StudentPage';
import StudentFilters, { filterValues } from '@/components/Students/StudentFilters';
import StudentList from '@/components/Students/StudentList';
import type { StudentIndexProps } from '@/components/Students/Students.types';
import { studentsBase } from '@/components/Students/Students.utils';

export default function StudentsIndex(props: StudentIndexProps): React.JSX.Element {
  const list = useTableListVisit({
    url: studentsBase,
    failureText: 'Não foi possível concluir a operação. Atualize a página e tente novamente.',
  });
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
      <TableListCard
        aria-label="Listagem de alunos"
        aria-busy={list.loading}
        loading={list.loading}
        loadingLabel="Atualizando alunos"
      >
        <StudentFilters {...props} onSearch={(filters) => list.visit(filters)} />
        <StudentList
          {...props}
          loading={list.loading}
          onPageChange={(page) => list.visit(filterValues(props.filters), page)}
        />
      </TableListCard>
    </StudentPage>
  );
}
