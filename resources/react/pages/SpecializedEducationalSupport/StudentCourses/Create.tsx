import PageScaffold from '@/components/UI/PageScaffold/PageScaffold';
import StudentRecordReturnButton from '@/components/Students/StudentRecordReturnButton';

export default function SpecializedEducationalSupportStudentCoursesCreatePage({
  student,
}: {
  student: { id: number };
}): React.JSX.Element {
  return (
    <PageScaffold title="Cadastro — Cursos do estudante">
      <StudentRecordReturnButton
        studentId={student.id}
        tab="academic-data"
        cancel
      />
    </PageScaffold>
  );
}
