import BusinessPage from "@/components/Navigation/BusinessPage/BusinessPage";
import StudentRecordReturnButton from "@/components/Students/StudentRecordReturnButton";
import {
    studentRecordUrl,
    studentsBase,
} from "@/components/Students/Students.utils";
import { TableListCreateButton } from "@/components/Tables/TableList/TableList";
import PeiList, { studentPeisUrl } from "./PeiList";
import type {
    PeiListProps,
    PeiStudentSummary,
} from "./PeiList.types";

export default function InclusionPeisIndexPage({
    student,
    ...props
}: PeiListProps & {
    student: PeiStudentSummary;
}): React.JSX.Element {
    return (
        <BusinessPage
            title="Histórico de PEIs"
            subtitle={`Aluno: ${student.name}`}
            breadcrumbItems={[
                { label: "Home", href: "/auth/dashboard" },
                { label: "Alunos", href: studentsBase },
                { label: student.name, href: studentRecordUrl(student.id, "peis") },
                { label: "PEIs" },
            ]}
            actions={
                <>
                    <StudentRecordReturnButton
                        studentId={student.id}
                        tab="peis"
                    />
                    {props.can.create ? (
                        <TableListCreateButton
                            href={`${studentPeisUrl(student.id)}/create`}
                            ariaLabel="Cadastrar PEI do aluno"
                        />
                    ) : null}
                </>
            }
            className="peisPage"
        >
            <PeiList
                {...props}
                variant="student"
                studentId={student.id}
            />
        </BusinessPage>
    );
}
