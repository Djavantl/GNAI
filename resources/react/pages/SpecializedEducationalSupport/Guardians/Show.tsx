import PageScaffold from '@/components/UI/PageScaffold/PageScaffold';
import StudentRecordReturnButton from '@/components/Students/StudentRecordReturnButton';

export default function SpecializedEducationalSupportGuardiansShowPage({
  student,
}: {
  student: { id: number };
}): React.JSX.Element {
  return (
    <PageScaffold title="Detalhes — Responsáveis">
      <StudentRecordReturnButton studentId={student.id} tab="guardians" />
    </PageScaffold>
  );
}
