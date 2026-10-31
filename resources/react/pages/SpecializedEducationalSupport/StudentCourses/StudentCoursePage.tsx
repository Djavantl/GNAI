import type { ReactNode } from "react";
import BusinessPage from "@/components/Navigation/BusinessPage/BusinessPage";
import type { PageBreadcrumbItem } from "@/components/Navigation/PageBreadcrumb/PageBreadcrumb";
import { aeeBase, studentsBase, studentUrl } from "@/components/Students/Students.utils";

export default function StudentCoursePage({
    title,
    subtitle,
    studentId,
    studentName,
    studentCourse,
    children,
}: {
    title: string;
    subtitle?: string;
    studentId: number;
    studentName: string;
    studentCourse?: { id: number; courseName: string };
    children: ReactNode;
}): React.JSX.Element {
    const breadcrumbItems: PageBreadcrumbItem[] = [
        { label: "Home", href: "/auth/dashboard" },
        { label: "Alunos", href: studentsBase },
        { label: studentName, href: studentUrl(studentId) },
        { label: "Matrículas", href: `${aeeBase}/students/${studentId}/history` },
    ];

    if (studentCourse) {
        breadcrumbItems.push(
            { label: studentCourse.courseName, href: `${aeeBase}/student-courses/${studentCourse.id}` },
            { label: "Editar" },
        );
    } else {
        breadcrumbItems.push({ label: "Cadastrar" });
    }

    return (
        <BusinessPage
            title={title}
            subtitle={subtitle}
            breadcrumbItems={breadcrumbItems}
            className="studentCoursePage"
        >
            {children}
        </BusinessPage>
    );
}
