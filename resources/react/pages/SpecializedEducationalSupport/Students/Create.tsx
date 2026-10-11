import { useState } from 'react';
import Button from '@/components/UI/Button/Button';
import StudentPage from '@/components/Students/StudentPage';
import StudentForm from '@/components/Students/StudentForm';
import type { StudentFormOptions } from '@/components/Students/Students.types';
import { studentsBase } from '@/components/Students/Students.utils';

export default function StudentsCreate(props: StudentFormOptions): React.JSX.Element {
  const [processing, setProcessing] = useState(false);
  return <StudentPage title="Cadastrar Aluno" current="Cadastrar"
    subtitle="Insira as informações pessoais e acadêmicas para registrar o novo estudante no sistema."
    actions={<Button href={studentsBase} tone="cancel" disabled={processing}>Cancelar</Button>}>
    <StudentForm mode="create" {...props} onProcessingChange={setProcessing} />
  </StudentPage>;
}
