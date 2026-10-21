import PageScaffold from '@/components/UI/PageScaffold/PageScaffold';
import StudentRecordReturnButton from '@/components/Students/StudentRecordReturnButton';

export default function SpecializedEducationalSupportStudentContextCreatePage({
  student,
}: {
  student: { id: number };
}): React.JSX.Element {
  return (
    <PageScaffold title="Cadastro — Contextos do estudante">
      <StudentRecordReturnButton
        studentId={student.id}
        tab="current-context"
        cancel
      />
    </PageScaffold>
  );
}
