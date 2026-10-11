import { useState } from 'react';
import Button from '@/components/UI/Button/Button';
import StudentPage from '@/components/Students/StudentPage';
import StudentForm from '@/components/Students/StudentForm';
import type { Student, StudentFormOptions } from '@/components/Students/Students.types';
import { studentUrl } from '@/components/Students/Students.utils';

export default function StudentsEdit(props: StudentFormOptions & { student: Student }): React.JSX.Element {
  const [processing, setProcessing] = useState(false);
  return <StudentPage title={`Editar ${props.student.person.name}`} current="Editar"
    subtitle="Atualize as informações cadastrais e acadêmicas do estudante."
    actions={<Button href={studentUrl(props.student.id)} tone="cancel" disabled={processing}>Cancelar</Button>}>
    <StudentForm key={props.student.id} mode="edit" {...props} onProcessingChange={setProcessing} />
  </StudentPage>;
}
