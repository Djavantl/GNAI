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
    student,
    guardian,
    children,
}: {
    title: string;
    subtitle?: string;
    current: string;
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

    breadcrumbItems.push({ label: current });

    return (
        <BusinessPage
            title={title}
            subtitle={subtitle}
            breadcrumbItems={breadcrumbItems}
            className="guardianPage"
        >
            {children}
        </BusinessPage>
    );
}
