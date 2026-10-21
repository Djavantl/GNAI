import PageScaffold from '@/components/UI/PageScaffold/PageScaffold';
import StudentRecordReturnButton from '@/components/Students/StudentRecordReturnButton';

export default function SpecializedEducationalSupportStudentsAeeRecordsShowPage({
  student,
}: {
  student: { id: number };
}): React.JSX.Element {
  return (
    <PageScaffold title="Detalhes — Atendimentos educacionais especializados">
      <StudentRecordReturnButton
        studentId={student.id}
        tab="aee-attendance"
      />
    </PageScaffold>
  );
}
