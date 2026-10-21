import PageScaffold from '@/components/UI/PageScaffold/PageScaffold';
import StudentRecordReturnButton from '@/components/Students/StudentRecordReturnButton';

export default function SpecializedEducationalSupportPeisShowPage({
  student,
}: {
  student: { id: number };
}): React.JSX.Element {
  return (
    <PageScaffold title="Detalhes — Planos educacionais individualizados">
      <StudentRecordReturnButton studentId={student.id} tab="peis" />
    </PageScaffold>
  );
}
