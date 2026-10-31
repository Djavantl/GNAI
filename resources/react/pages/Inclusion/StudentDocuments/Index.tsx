import { usePage } from "@inertiajs/react";
import { useMemo } from "react";
import FormField from "@/components/Forms/FormField/FormField";
import FormLayout from "@/components/Forms/FormLayout/FormLayout";
import SelectField from "@/components/Forms/SelectField/SelectField";
import TextInput from "@/components/Forms/TextInput/TextInput";
import StudentRecordReturnButton from "@/components/Students/StudentRecordReturnButton";
import {
    aeeBase,
    dateTimeLabel,
    fileSizeLabel,
    studentsBase,
} from "@/components/Students/Students.utils";
import TableActions from "@/components/Tables/TableActions/TableActions";
import {
    TableListCard,
    TableListClearButton,
    TableListCreateButton,
    TableListEmpty,
    TableListFilterActions,
    TableListFooter,
    TableListScroll,
    TableListTable,
    useTableListAction,
    useTableListVisit,
    type TableListColumn,
} from "@/components/Tables/TableList/TableList";
import Button from "@/components/UI/Button/Button";
import ConfirmActionModal from "@/components/UI/ConfirmActionModal/ConfirmActionModal";
import useAutoListFilters from "@/hooks/useAutoListFilters";
import StudentDocumentPage from "./StudentDocumentPage";
import type {
    StudentDocumentFilters,
    StudentDocumentListRecord,
    StudentDocumentPaginator,
    StudentDocumentStudent,
} from "./StudentDocument.types";

type Props = {
    documents: StudentDocumentPaginator;
    student: StudentDocumentStudent;
    types: { value: string; label: string }[];
    semesters: { value: string; label: string }[];
    filters: Partial<StudentDocumentFilters>;
    can: { create: boolean; view: boolean; update: boolean; delete: boolean };
};

const documentsBase = `${aeeBase}/student-documents`;
const emptyFilters: StudentDocumentFilters = {
    title: "",
    type: "",
    semester_id: "",
};
const columns: TableListColumn[] = [
    { id: "title", label: "Título", width: "25%", minWidth: 130, fixed: "start" },
    { id: "type", label: "Tipo", width: "13%", minWidth: 82 },
    { id: "semester", label: "Semestre", width: "13%", minWidth: 82 },
    { id: "size", label: "Tamanho", width: "11%", minWidth: 76 },
    { id: "upload", label: "Data de upload", width: "16%", minWidth: 110 },
    { id: "originalName", label: "Nome original do arquivo", width: "16%", defaultVisible: false },
    { id: "actions", label: "Ações", width: "22%", minWidth: 150, fixed: "end" },
];

export default function InclusionStudentDocumentsIndexPage({
    documents,
    student,
    types,
    semesters,
    filters: initialFilters,
    can,
}: Props): React.JSX.Element {
    const pageErrors = usePage().props.errors as Record<string, string | undefined>;
    const listUrl = `${studentsBase}/${student.id}/documents`;
    const list = useTableListVisit({
        url: listUrl,
        failureText: "Não foi possível atualizar os documentos. Tente novamente.",
    });
    const removal = useTableListAction<StudentDocumentListRecord>({
        failureText: "Não foi possível excluir o documento. Tente novamente.",
    });
    const initialValues = useMemo(
        () => ({ ...emptyFilters, ...initialFilters }),
        [initialFilters],
    );
    const { values, update, clear, submit, cancelScheduled } = useAutoListFilters({
        initialValues,
        emptyValues: emptyFilters,
        onSearch: (filters) => list.visit(filters),
    });

    return (
        <StudentDocumentPage
            title={`Documentos, ${student.person.name}`}
            subtitle="Gestão de laudos, relatórios e planos de AEE."
            student={student}
            actions={(
                <>
                    <StudentRecordReturnButton
                        studentId={student.id}
                        tab="documents"
                    />
                    {can.create ? (
                        <TableListCreateButton
                            href={`${listUrl}/create`}
                            ariaLabel="Cadastrar documento do aluno"
                        />
                    ) : null}
                </>
            )}
        >
            <TableListCard
                aria-label="Documentos do aluno"
                aria-busy={list.loading}
                loading={list.loading}
                loadingLabel="Atualizando documentos"
            >
                <FormLayout
                    onSubmit={(event) => {
                        event.preventDefault();
                        submit();
                    }}
                >
                    <div className="studentDocumentIndex__filters">
                        <FormField
                            id="student-document-filter-title"
                            label="Título"
                            error={pageErrors.title}
                        >
                            <TextInput
                                id="student-document-filter-title"
                                name="title"
                                value={values.title}
                                maxLength={255}
                                placeholder="Buscar por título..."
                                invalid={!!pageErrors.title}
                                aria-describedby={pageErrors.title ? "student-document-filter-title-error" : undefined}
                                onChange={(event) => update("title", event.target.value)}
                            />
                        </FormField>
                        <FormField
                            id="student-document-filter-type"
                            label="Tipo"
                            error={pageErrors.type}
                        >
                            <SelectField
                                id="student-document-filter-type"
                                name="type"
                                label="Tipo"
                                placeholder="Todos"
                                value={values.type}
                                options={types}
                                aria-invalid={!!pageErrors.type}
                                aria-describedby={pageErrors.type ? "student-document-filter-type-error" : undefined}
                                onChange={(event) => update("type", event.target.value)}
                            />
                        </FormField>
                        <FormField
                            id="student-document-filter-semester"
                            label="Semestre"
                            error={pageErrors.semester_id}
                        >
                            <SelectField
                                id="student-document-filter-semester"
                                name="semester_id"
                                label="Semestre"
                                placeholder="Todos"
                                value={values.semester_id}
                                options={semesters}
                                aria-invalid={!!pageErrors.semester_id}
                                aria-describedby={pageErrors.semester_id ? "student-document-filter-semester-error" : undefined}
                                onChange={(event) => update("semester_id", event.target.value)}
                            />
                        </FormField>
                        <TableListFilterActions>
                            <TableListClearButton onClick={clear} />
                        </TableListFilterActions>
                    </div>
                </FormLayout>
                <TableListScroll aria-label="Tabela de documentos do aluno">
                    <TableListTable
                        columns={columns}
                        page={documents}
                    >
                        {documents.data.length ? documents.data.map((document) => (
                            <tr key={document.id}>
                                <td>
                                    <div className="studentDocumentIndex__title">
                                        <strong>{document.title}</strong>
                                        <small>{document.original_name}</small>
                                    </div>
                                </td>
                                <td>{document.type_label}</td>
                                <td>{document.semester_label ?? "Não informado"}</td>
                                <td>{fileSizeLabel(document.file_size)}</td>
                                <td>{dateTimeLabel(document.created_at)}</td>
                                <td>{document.original_name || "Não informado"}</td>
                                <td>
                                    {can.view || can.update || can.delete ? (
                                        <TableActions>
                                            {can.view ? (
                                                <>
                                                    <Button
                                                        href={`${documentsBase}/${document.id}/view`}
                                                        target="_blank"
                                                        rel="noopener noreferrer"
                                                        size="sm"
                                                        tone="dark-blue"
                                                        iconOnly
                                                        leftIconClass="fa fa-eye"
                                                        aria-label={`Visualizar documento ${document.title}`}
                                                    />
                                                    <Button
                                                        href={`${documentsBase}/${document.id}/download`}
                                                        download
                                                        size="sm"
                                                        tone="cancel"
                                                        iconOnly
                                                        leftIconClass="fa fa-download"
                                                        aria-label={`Baixar documento ${document.title}`}
                                                    />
                                                </>
                                            ) : null}
                                            {can.update ? (
                                                <Button
                                                    href={`${documentsBase}/${document.id}/edit`}
                                                    size="sm"
                                                    tone="warning"
                                                    iconOnly
                                                    leftIconClass="fa fa-pencil"
                                                    aria-label={`Editar documento ${document.title}`}
                                                />
                                            ) : null}
                                            {can.delete ? (
                                                <Button
                                                    tone="dark-red"
                                                    size="sm"
                                                    iconOnly
                                                    leftIconClass="fa fa-trash-o"
                                                    aria-label={`Excluir documento ${document.title}`}
                                                    disabled={removal.processing}
                                                    onClick={() => {
                                                        cancelScheduled();
                                                        removal.open(document);
                                                    }}
                                                />
                                            ) : null}
                                        </TableActions>
                                    ) : "Nenhuma ação"}
                                </td>
                            </tr>
                        )) : (
                            <tr>
                                <TableListEmpty colSpan={columns.length}>
                                    Nenhum documento do aluno encontrado.
                                </TableListEmpty>
                            </tr>
                        )}
                    </TableListTable>
                </TableListScroll>
                <TableListFooter
                    page={documents}
                    disabled={list.loading}
                    paginationLabel="Paginação de documentos"
                    onSelect={(page) => list.visit(values, page)}
                />
            </TableListCard>
            <ConfirmActionModal
                open={removal.target !== null}
                title="Excluir Documento"
                message={(
                    <>
                        Deseja excluir permanentemente o documento{" "}
                        <strong>{removal.target?.title}</strong>?
                    </>
                )}
                processing={removal.processing}
                confirmLabel="Confirmar exclusão"
                processingLabel="Excluindo documento..."
                confirmTone="dark-red"
                onClose={removal.close}
                onConfirm={() => {
                    if (!removal.target) return;
                    cancelScheduled();
                    list.cancel();
                    removal.submit("delete", `${documentsBase}/${removal.target.id}`);
                }}
            />
        </StudentDocumentPage>
    );
}
