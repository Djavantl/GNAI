import StudentPage from '@/components/Students/StudentPage';
import StudentForm from '@/components/Students/StudentForm';
import type { StudentFormOptions } from '@/components/Students/Students.types';

export default function StudentsCreate(props: StudentFormOptions): React.JSX.Element {
  return (
    <StudentPage
      title="Cadastrar Aluno"
      current="Cadastrar"
      subtitle="Insira as informações pessoais e acadêmicas para registrar o novo estudante no sistema."
    >
      <StudentForm mode="create" {...props} />
    </StudentPage>
  );
}
