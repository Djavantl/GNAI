import StudentDeficiencyForm from "./StudentDeficiencyForm";
import StudentDeficiencyPage from "./StudentDeficiencyPage";
import type {
    StudentDeficiencyRecord,
    StudentDeficiencyStudent,
} from "./StudentDeficiency.types";

export default function InclusionStudentDeficienciesEditPage({
    student,
    student_deficiency: studentDeficiency,
}: {
    student: StudentDeficiencyStudent;
    student_deficiency: StudentDeficiencyRecord;
}): React.JSX.Element {
    return (
        <StudentDeficiencyPage
            title="Editar Perfil de Atendimento do Aluno"
            subtitle={`Aluno: ${student.person.name}`}
            current="Editar"
            student={student}
            studentDeficiency={studentDeficiency}
        >
            <StudentDeficiencyForm
                key={studentDeficiency.id}
                mode="edit"
                student={student}
                studentDeficiency={studentDeficiency}
            />
        </StudentDeficiencyPage>
    );
}
