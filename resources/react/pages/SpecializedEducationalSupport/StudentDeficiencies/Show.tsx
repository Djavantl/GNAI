import PageScaffold from '@/components/UI/PageScaffold/PageScaffold';
import StudentRecordReturnButton from '@/components/Students/StudentRecordReturnButton';

export default function SpecializedEducationalSupportStudentDeficienciesShowPage({
  student,
}: {
  student: { id: number };
}): React.JSX.Element {
  return (
    <PageScaffold title="Detalhes — Deficiências do estudante">
      <StudentRecordReturnButton
        studentId={student.id}
        tab="support-profiles"
      />
    </PageScaffold>
  );
}
