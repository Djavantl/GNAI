import StudentDocumentForm from "./StudentDocumentForm";
import StudentDocumentPage from "./StudentDocumentPage";
import type {
    StudentDocumentFormOptions,
    StudentDocumentRecord,
} from "./StudentDocument.types";

export default function InclusionStudentDocumentsEditPage({
    student,
    types,
    semester,
    studentDocument,
}: StudentDocumentFormOptions & {
    studentDocument: StudentDocumentRecord;
}): React.JSX.Element {
    return (
        <StudentDocumentPage
            title="Editar Documento"
            subtitle="Atualize as informações do documento ou substitua o arquivo anexado."
            current="Editar"
            student={student}
            documentTitle={studentDocument.title}
        >
            <StudentDocumentForm
                key={studentDocument.id}
                student={student}
                types={types}
                semester={semester}
                studentDocument={studentDocument}
            />
        </StudentDocumentPage>
    );
}
