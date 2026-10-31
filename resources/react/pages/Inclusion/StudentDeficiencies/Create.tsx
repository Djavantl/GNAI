import StudentDeficiencyForm from "./StudentDeficiencyForm";
import StudentDeficiencyPage from "./StudentDeficiencyPage";
import type {
    DeficiencyOption,
    StudentDeficiencyStudent,
} from "./StudentDeficiency.types";

export default function InclusionStudentDeficienciesCreatePage({
    student,
    deficienciesList,
}: {
    student: StudentDeficiencyStudent;
    deficienciesList: DeficiencyOption[];
}): React.JSX.Element {
    return (
        <StudentDeficiencyPage
            title="Vincular Perfil de Atendimento"
            subtitle={`Aluno: ${student.person.name}`}
            current="Cadastrar"
            student={student}
        >
            <StudentDeficiencyForm
                mode="create"
                student={student}
                deficiencies={deficienciesList}
            />
        </StudentDeficiencyPage>
    );
}
