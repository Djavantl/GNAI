import PageScaffold from '@/components/UI/PageScaffold/PageScaffold';
import StudentRecordReturnButton from '@/components/Students/StudentRecordReturnButton';

export default function SpecializedEducationalSupportStudentDocumentsEditPage({
  student,
}: {
  student: { id: number };
}): React.JSX.Element {
  return (
    <PageScaffold title="Edição — Documentos do estudante">
      <StudentRecordReturnButton
        studentId={student.id}
        tab="documents"
        cancel
      />
    </PageScaffold>
  );
}
