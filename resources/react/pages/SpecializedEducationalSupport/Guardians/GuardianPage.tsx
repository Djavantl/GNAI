import type { ReactNode } from "react";
import BusinessPage from "@/components/Navigation/BusinessPage/BusinessPage";
import type { PageBreadcrumbItem } from "@/components/Navigation/PageBreadcrumb/PageBreadcrumb";
import {
    aeeBase,
    studentRecordUrl,
    studentsBase,
    studentUrl,
} from "@/components/Students/Students.utils";
import type { GuardianStudent } from "./Guardian.types";
import "./Guardians.css";

export default function GuardianPage({
    title,
    subtitle,
    current,
    actions,
    student,
    guardian,
    children,
}: {
    title: string;
    subtitle?: string;
    current?: string;
    actions?: ReactNode;
    student: GuardianStudent;
    guardian?: { id: number; person: { name: string } };
    children: ReactNode;
}): React.JSX.Element {
    const breadcrumbItems: PageBreadcrumbItem[] = [
        { label: "Home", href: "/auth/dashboard" },
        { label: "Alunos", href: studentsBase },
        { label: student.person.name, href: studentUrl(student.id) },
        {
            label: "Responsáveis",
            href: studentRecordUrl(student.id, "guardians"),
        },
    ];

    if (guardian) {
        breadcrumbItems.push({
            label: guardian.person.name,
            href: `${aeeBase}/guardian/${guardian.id}/show`,
        });
    }

    if (current) {
        breadcrumbItems.push({ label: current });
    }

    return (
        <BusinessPage
            title={title}
            subtitle={subtitle}
            breadcrumbItems={breadcrumbItems}
            actions={actions}
            className="guardianPage"
        >
            {children}
        </BusinessPage>
    );
}
