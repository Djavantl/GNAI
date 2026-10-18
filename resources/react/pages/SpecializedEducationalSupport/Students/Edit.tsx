import StudentPage from '@/components/Students/StudentPage';
import StudentForm from '@/components/Students/StudentForm';
import type { Student, StudentFormOptions } from '@/components/Students/Students.types';

export default function StudentsEdit(props: StudentFormOptions & { student: Student }): React.JSX.Element {
  return (
    <StudentPage
      title={`Editar ${props.student.person.name}`}
      current="Editar"
      subtitle="Atualize as informações cadastrais e acadêmicas do estudante."
    >
      <StudentForm
        key={props.student.id}
        mode="edit"
        {...props}
      />
    </StudentPage>
  );
}
