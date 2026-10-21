import PageScaffold from '@/components/UI/PageScaffold/PageScaffold';
import StudentRecordReturnButton from '@/components/Students/StudentRecordReturnButton';

export default function SpecializedEducationalSupportPeisCreatePage({
  student,
}: {
  student: { id: number };
}): React.JSX.Element {
  return (
    <PageScaffold title="Cadastro — Planos educacionais individualizados">
      <StudentRecordReturnButton studentId={student.id} tab="peis" cancel />
    </PageScaffold>
  );
}
