import PageScaffold from '@/components/UI/PageScaffold/PageScaffold';
import StudentRecordReturnButton from '@/components/Students/StudentRecordReturnButton';

export default function SpecializedEducationalSupportGuardiansCreatePage({
  student,
}: {
  student: { id: number };
}): React.JSX.Element {
  return (
    <PageScaffold title="Cadastro — Responsáveis">
      <StudentRecordReturnButton
        studentId={student.id}
        tab="guardians"
        cancel
      />
    </PageScaffold>
  );
}
