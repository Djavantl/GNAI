import StudentDocumentForm from "./StudentDocumentForm";
import StudentDocumentPage from "./StudentDocumentPage";
import type { StudentDocumentFormOptions } from "./StudentDocument.types";

export default function InclusionStudentDocumentsCreatePage({
    student,
    types,
    semester,
}: StudentDocumentFormOptions): React.JSX.Element {
    return (
        <StudentDocumentPage
            title={`Novo Documento para ${student.person.name}`}
            subtitle="Faça o upload de laudos, avaliações ou planos de AEE para este estudante."
            current="Cadastrar"
            student={student}
        >
            <StudentDocumentForm
                student={student}
                types={types}
                semester={semester}
            />
        </StudentDocumentPage>
    );
}
