import PageScaffold from '@/components/UI/PageScaffold/PageScaffold';
import StudentRecordReturnButton from '@/components/Students/StudentRecordReturnButton';

export default function SpecializedEducationalSupportPedagogicalRecordsShowPage({
  returnStudentId,
}: {
  returnStudentId?: number;
}): React.JSX.Element {
  return (
    <PageScaffold title="Detalhes — Atendimentos pedagógicos">
      {returnStudentId ? (
        <StudentRecordReturnButton
          studentId={returnStudentId}
          tab="pedagogical-attendance"
        />
      ) : null}
    </PageScaffold>
  );
}
