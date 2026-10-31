import TeacherForm from "./TeacherForm";
import TeacherPage from "./TeacherPage";
import type { TeacherRecord } from "./Teacher.types";

export default function InclusionTeachersEditPage({
    teacher,
}: {
    teacher: TeacherRecord;
}): React.JSX.Element {
    return (
        <TeacherPage
            title="Editar Professor"
            subtitle="Atualize os dados e as disciplinas atribuídas ao docente."
            current="Editar"
            teacher={{ id: teacher.id, name: teacher.person.name }}
        >
            <TeacherForm
                key={teacher.id}
                mode="edit"
                teacher={teacher}
            />
        </TeacherPage>
    );
}
