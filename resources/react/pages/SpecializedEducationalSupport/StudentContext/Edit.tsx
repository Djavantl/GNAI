import PageScaffold from '@/components/UI/PageScaffold/PageScaffold';
import StudentRecordReturnButton from '@/components/Students/StudentRecordReturnButton';

export default function SpecializedEducationalSupportStudentContextEditPage({
  student,
}: {
  student: { id: number };
}): React.JSX.Element {
  return (
    <PageScaffold title="Edição — Contextos do estudante">
      <StudentRecordReturnButton
        studentId={student.id}
        tab="current-context"
        cancel
      />
    </PageScaffold>
  );
}
