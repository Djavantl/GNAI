import PageScaffold from '@/components/UI/PageScaffold/PageScaffold';
import StudentRecordReturnButton from '@/components/Students/StudentRecordReturnButton';

export default function SpecializedEducationalSupportStudentCoursesEditPage({
  studentCourse,
}: {
  studentCourse: { student_id: number; student?: { id: number } };
}): React.JSX.Element {
  return (
    <PageScaffold title="Edição — Cursos do estudante">
      <StudentRecordReturnButton
        studentId={studentCourse.student?.id ?? studentCourse.student_id}
        tab="academic-data"
        cancel
      />
    </PageScaffold>
  );
}
