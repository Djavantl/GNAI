import PageScaffold from '@/components/UI/PageScaffold/PageScaffold';
import StudentRecordReturnButton from '@/components/Students/StudentRecordReturnButton';

export default function SpecializedEducationalSupportGuardiansEditPage({
  student,
}: {
  student: { id: number };
}): React.JSX.Element {
  return (
    <PageScaffold title="Edição — Responsáveis">
      <StudentRecordReturnButton
        studentId={student.id}
        tab="guardians"
        cancel
      />
    </PageScaffold>
  );
}
