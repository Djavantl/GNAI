import Button from '@/components/UI/Button/Button';
import StudentPage from '@/components/Students/StudentPage';
import StudentPersonalRecord from '@/components/Students/StudentPersonalRecord';
import StudentSupportRecord from '@/components/Students/StudentSupportRecord';
import StudentDocumentsRecord from '@/components/Students/StudentDocumentsRecord';
import StudentAttendanceRecord from '@/components/Students/StudentAttendanceRecord';
import DeleteStudent from '@/components/Students/DeleteStudent';
import type { StudentShowProps } from '@/components/Students/Students.types';
import { studentUrl, studentsBase } from '@/components/Students/Students.utils';

export default function StudentsShow(props: StudentShowProps): React.JSX.Element {
  const { student, permissions } = props;
  return <StudentPage title="Prontuário do Aluno" current={student.person.name}
    subtitle="Visualize o ecossistema completo e histórico detalhado do aluno."
    actions={<>
      {permissions['student.view'] ? <Button href={studentUrl(student.id, 'pdf')} target="_blank" rel="noopener noreferrer">Gerar PDF</Button> : null}
      {permissions['student.update'] ? <Button href={studentUrl(student.id, 'edit')} tone="warning">Editar Cadastro</Button> : null}
      <Button href={studentsBase}>Voltar</Button>
    </>}>
    <StudentPersonalRecord {...props} />
    <StudentSupportRecord student={student} permissions={permissions} />
    <StudentDocumentsRecord student={student} permissions={permissions} />
    <StudentAttendanceRecord {...props} />
    {permissions['student.delete'] ? <div className="studentsPage__sectionActions"><DeleteStudent id={student.id} name={student.person.name} /></div> : null}
  </StudentPage>;
}
