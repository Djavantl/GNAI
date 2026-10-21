import PageScaffold from '@/components/UI/PageScaffold/PageScaffold';
import StudentRecordReturnButton from '@/components/Students/StudentRecordReturnButton';

export default function SpecializedEducationalSupportStudentContextVersionPage({
  student,
}: {
  student: { id: number };
}): React.JSX.Element {
  return (
    <PageScaffold title="Versão — Contextos do estudante">
      <StudentRecordReturnButton
        studentId={student.id}
        tab="current-context"
        cancel
      />
    </PageScaffold>
  );
}
