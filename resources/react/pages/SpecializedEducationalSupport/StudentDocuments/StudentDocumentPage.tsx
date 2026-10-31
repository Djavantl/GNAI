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
    actions,
    student,
    documentTitle,
    children,
}: {
    title: string;
    subtitle?: string;
    current?: string;
    actions?: ReactNode;
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
            href: current ? studentRecordUrl(student.id, "documents") : undefined,
        },
    ];

    if (documentTitle) breadcrumbItems.push({ label: documentTitle });

    if (current) {
        breadcrumbItems.push({ label: current });
    }

    return (
        <BusinessPage
            title={title}
            subtitle={subtitle}
            breadcrumbItems={breadcrumbItems}
            actions={actions}
            className="studentDocumentPage"
        >
            {children}
        </BusinessPage>
    );
}
