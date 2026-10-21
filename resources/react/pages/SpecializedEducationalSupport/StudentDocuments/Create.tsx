import PageScaffold from '@/components/UI/PageScaffold/PageScaffold';
import StudentRecordReturnButton from '@/components/Students/StudentRecordReturnButton';

export default function SpecializedEducationalSupportStudentDocumentsCreatePage({
  student,
}: {
  student: { id: number };
}): React.JSX.Element {
  return (
    <PageScaffold title="Cadastro — Documentos do estudante">
      <StudentRecordReturnButton
        studentId={student.id}
        tab="documents"
        cancel
      />
    </PageScaffold>
  );
}
