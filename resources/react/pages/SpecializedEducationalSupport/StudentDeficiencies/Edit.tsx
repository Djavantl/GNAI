import PageScaffold from '@/components/UI/PageScaffold/PageScaffold';
import StudentRecordReturnButton from '@/components/Students/StudentRecordReturnButton';

export default function SpecializedEducationalSupportStudentDeficienciesEditPage({
  student,
}: {
  student: { id: number };
}): React.JSX.Element {
  return (
    <PageScaffold title="Edição — Deficiências do estudante">
      <StudentRecordReturnButton
        studentId={student.id}
        tab="support-profiles"
        cancel
      />
    </PageScaffold>
  );
}
