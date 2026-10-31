import type { ReactNode } from "react";
import BusinessPage from "@/components/Navigation/BusinessPage/BusinessPage";
import type { PageBreadcrumbItem } from "@/components/Navigation/PageBreadcrumb/PageBreadcrumb";
import { aeeBase, studentsBase, studentUrl } from "@/components/Students/Students.utils";

export default function StudentContextPage({
    title,
    subtitle,
    studentId,
    studentName,
    contextId,
    children,
}: {
    title: string;
    subtitle?: string;
    studentId: number;
    studentName: string;
    contextId?: number;
    children: ReactNode;
}): React.JSX.Element {
    const contextsUrl = `${aeeBase}/student-context/${studentId}/index`;
    const breadcrumbItems: PageBreadcrumbItem[] = contextId
        ? [
            { label: "Home", href: "/auth/dashboard" },
            { label: "Alunos", href: studentsBase },
            { label: studentName, href: studentUrl(studentId) },
            { label: "Contextos", href: contextsUrl },
            { label: `Contexto #${contextId}`, href: `${aeeBase}/student-context/${contextId}/show` },
            { label: "Editar" },
        ]
        : [
            { label: "Home", href: "/auth/dashboard" },
            { label: "Prontuário do Aluno", href: studentUrl(studentId) },
            { label: "Contexto do Aluno", href: contextsUrl },
            { label: "Novo Contexto" },
        ];

    return (
        <BusinessPage
            title={title}
            subtitle={subtitle}
            breadcrumbItems={breadcrumbItems}
            className="studentContextPage"
        >
            {children}
        </BusinessPage>
    );
}
