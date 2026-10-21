import PageScaffold from '@/components/UI/PageScaffold/PageScaffold';
import StudentRecordReturnButton from '@/components/Students/StudentRecordReturnButton';

export default function SpecializedEducationalSupportStudentDeficienciesCreatePage({
  student,
}: {
  student: { id: number };
}): React.JSX.Element {
  return (
    <PageScaffold title="Cadastro — Deficiências do estudante">
      <StudentRecordReturnButton
        studentId={student.id}
        tab="support-profiles"
        cancel
      />
    </PageScaffold>
  );
}
