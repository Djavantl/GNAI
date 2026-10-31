import type { ReactNode } from "react";
import BusinessPage from "@/components/Navigation/BusinessPage/BusinessPage";
import type { PageBreadcrumbItem } from "@/components/Navigation/PageBreadcrumb/PageBreadcrumb";
import {
    studentRecordUrl,
    studentsBase,
    studentUrl,
} from "@/components/Students/Students.utils";
import type {
    StudentDeficiencyRecord,
    StudentDeficiencyStudent,
} from "./StudentDeficiency.types";

export default function StudentDeficiencyPage({
    title,
    subtitle,
    current,
    student,
    studentDeficiency,
    children,
}: {
    title: string;
    subtitle?: string;
    current: string;
    student: StudentDeficiencyStudent;
    studentDeficiency?: StudentDeficiencyRecord;
    children: ReactNode;
}): React.JSX.Element {
    const breadcrumbItems: PageBreadcrumbItem[] = [
        { label: "Home", href: "/auth/dashboard" },
        { label: "Alunos", href: studentsBase },
        { label: student.person.name, href: studentUrl(student.id) },
        {
            label: "Perfis de Atendimento",
            href: studentRecordUrl(student.id, "support-profiles"),
        },
    ];

    if (studentDeficiency?.deficiency) {
        breadcrumbItems.push({
            label: studentDeficiency.deficiency.name,
            href: `${studentsBase}/${student.id}/deficiencies/${studentDeficiency.id}`,
        });
    }

    breadcrumbItems.push({ label: current });

    return (
        <BusinessPage
            title={title}
            subtitle={subtitle}
            breadcrumbItems={breadcrumbItems}
            className="studentDeficiencyPage"
        >
            {children}
        </BusinessPage>
    );
}
