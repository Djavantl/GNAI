import Button from '@/components/UI/Button/Button';
import FormActions from '@/components/Forms/FormActions/FormActions';
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

  return (
    <StudentPage
      title="Prontuário do Aluno"
      current={student.person.name}
      subtitle="Visualize o ecossistema completo e histórico detalhado do aluno."
    >
      <StudentPersonalRecord {...props} />
      <StudentSupportRecord
        student={student}
        permissions={permissions}
      />
      <StudentDocumentsRecord
        student={student}
        permissions={permissions}
      />
      <StudentAttendanceRecord {...props} />
      <FormActions
        secondary={permissions['student.view'] ? (
          <Button
            href={studentUrl(student.id, 'pdf')}
            target="_blank"
            rel="noopener noreferrer"
            tone="dark-red"
            leftIconClass="fa fa-file-pdf-o"
            iconOnly
            aria-label="Gerar PDF do aluno"
          />
        ) : undefined}
      >
        <Button
          href={studentsBase}
          tone="cancel"
          iconOnly
          aria-label="Voltar para alunos"
        />
        {permissions['student.update'] ? (
          <Button
            href={studentUrl(student.id, 'edit')}
            tone="warning"
            leftIconClass="fa fa-pencil"
            iconOnly
            aria-label="Editar cadastro do aluno"
          />
        ) : null}
        {permissions['student.delete'] ? (
          <DeleteStudent
            id={student.id}
            name={student.person.name}
            iconOnly
          />
        ) : null}
      </FormActions>
    </StudentPage>
  );
}
