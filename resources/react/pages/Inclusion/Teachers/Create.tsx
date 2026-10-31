import TeacherForm from "./TeacherForm";
import TeacherPage from "./TeacherPage";

export default function InclusionTeachersCreatePage(): React.JSX.Element {
    return (
        <TeacherPage
            title="Novo Professor"
            subtitle="Cadastre os dados pessoais, matrícula e atribua as disciplinas do novo docente."
            current="Cadastrar"
        >
            <TeacherForm mode="create" />
        </TeacherPage>
    );
}
