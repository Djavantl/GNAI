import BusinessPage from "@/components/Navigation/BusinessPage/BusinessPage";
import StudentRecordReturnButton from "@/components/Students/StudentRecordReturnButton";
import { studentsBase, studentUrl } from "@/components/Students/Students.utils";
import PedagogicalRecordList from "@/pages/Inclusion/PedagogicalRecords/PedagogicalRecordList";
import type {
    PedagogicalRecordFilterName,
    PedagogicalRecordListProps,
} from "@/pages/Inclusion/PedagogicalRecords/PedagogicalRecordList.types";

type Props = PedagogicalRecordListProps & {
    student: { id: number; name: string | null };
};

export default function InclusionStudentsPedagogicalRecordsIndexPage({
    student,
    ...props
}: Props): React.JSX.Element {
    const studentName = student.name ?? "Aluno";
    const filters: PedagogicalRecordFilterName[] = props.can.view_all
        ? ["professional_id", "is_present", "with_guardians"]
        : ["is_present", "with_guardians"];

    return (
        <BusinessPage
            title="Histórico de Atendimentos Pedagógicos"
            subtitle={`Aluno: ${studentName}`}
            breadcrumbItems={[
                { label: "Home", href: "/auth/dashboard" },
                { label: "Alunos", href: studentsBase },
                { label: studentName, href: studentUrl(student.id) },
                { label: "Atendimentos pedagógicos" },
            ]}
            actions={(
                <StudentRecordReturnButton
                    studentId={student.id}
                    tab="pedagogical-attendance"
                />
            )}
        >
            <PedagogicalRecordList
                {...props}
                url={studentUrl(student.id, "pedagogical-records")}
                variant="student"
                primaryFilters={filters}
                pageParam="pedagogical_page"
                studentId={student.id}
            />
        </BusinessPage>
    );
}
