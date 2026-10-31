import type { ReactNode } from "react";
import BusinessPage from "@/components/Navigation/BusinessPage/BusinessPage";
import type { PageBreadcrumbItem } from "@/components/Navigation/PageBreadcrumb/PageBreadcrumb";
import {
    studentRecordUrl,
    studentsBase,
    studentUrl,
} from "@/components/Students/Students.utils";
import type { StudentDocumentStudent } from "./StudentDocument.types";
import "./StudentDocuments.css";

export default function StudentDocumentPage({
    title,
    subtitle,
    current,
    student,
    documentTitle,
    children,
}: {
    title: string;
    subtitle?: string;
    current: string;
    student: StudentDocumentStudent;
    documentTitle?: string;
    children: ReactNode;
}): React.JSX.Element {
    const breadcrumbItems: PageBreadcrumbItem[] = [
        { label: "Home", href: "/auth/dashboard" },
        { label: "Alunos", href: studentsBase },
        { label: student.person.name, href: studentUrl(student.id) },
        {
            label: "Documentos",
            href: studentRecordUrl(student.id, "documents"),
        },
    ];

    if (documentTitle) breadcrumbItems.push({ label: documentTitle });

    breadcrumbItems.push({ label: current });

    return (
        <BusinessPage
            title={title}
            subtitle={subtitle}
            breadcrumbItems={breadcrumbItems}
            className="studentDocumentPage"
        >
            {children}
        </BusinessPage>
    );
}
