import PageScaffold from '@/components/UI/PageScaffold/PageScaffold';
import StudentRecordReturnButton from '@/components/Students/StudentRecordReturnButton';

export default function SpecializedEducationalSupportStudentsAeeRecordsEditPage({
  student,
}: {
  student: { id: number };
}): React.JSX.Element {
  return (
    <PageScaffold title="Edição — Atendimentos educacionais especializados">
      <StudentRecordReturnButton
        studentId={student.id}
        tab="aee-attendance"
        cancel
      />
    </PageScaffold>
  );
}
