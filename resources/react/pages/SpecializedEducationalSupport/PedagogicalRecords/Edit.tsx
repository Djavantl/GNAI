import PageScaffold from '@/components/UI/PageScaffold/PageScaffold';
import StudentRecordReturnButton from '@/components/Students/StudentRecordReturnButton';

export default function SpecializedEducationalSupportPedagogicalRecordsEditPage({
  returnStudentId,
}: {
  returnStudentId?: number;
}): React.JSX.Element {
  return (
    <PageScaffold title="Edição — Atendimentos pedagógicos">
      {returnStudentId ? (
        <StudentRecordReturnButton
          studentId={returnStudentId}
          tab="pedagogical-attendance"
          cancel
        />
      ) : null}
    </PageScaffold>
  );
}
