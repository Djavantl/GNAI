import CardBox from '@/components/UI/CardBox/CardBox';
import Button from '@/components/UI/Button/Button';
import StudentPage from '@/components/Students/StudentPage';
import StudentFilters from '@/components/Students/StudentFilters';
import StudentList from '@/components/Students/StudentList';
import type { StudentIndexProps } from '@/components/Students/Students.types';
import { studentsBase } from '@/components/Students/Students.utils';

export default function StudentsIndex(props: StudentIndexProps): React.JSX.Element {
  return <StudentPage title="Alunos" subtitle="Gerencie os estudantes e seus documentos de apoio especializado." banner
    actions={props.permissions['student.create'] ? <Button href={`${studentsBase}/create`} tone="primary">Cadastrar aluno</Button> : undefined}>
    <CardBox><StudentFilters {...props} /><StudentList {...props} /></CardBox>
  </StudentPage>;
}
