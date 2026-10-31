import type { ReactNode } from "react";
import BusinessPage from "@/components/Navigation/BusinessPage/BusinessPage";
import type { PageBreadcrumbItem } from "@/components/Navigation/PageBreadcrumb/PageBreadcrumb";
import type { SemesterRecord } from "./Semester.types";

export const semesterBase = "/specialized-educational-support/semesters";

export function semesterLabel(semester: Pick<SemesterRecord, "label" | "year" | "term">): string {
    return semester.label || `${semester.year}.${semester.term}`;
}

export default function SemesterPage({
    title,
    subtitle,
    current,
    semester,
    actions,
    children,
}: {
    title: string;
    subtitle?: string;
    current?: string;
    semester?: Pick<SemesterRecord, "id" | "label" | "year" | "term">;
    actions?: ReactNode;
    children: ReactNode;
}): React.JSX.Element {
    const breadcrumbItems: PageBreadcrumbItem[] = [
        { label: "Home", href: "/auth/dashboard" },
        { label: "Semestres", href: current || semester ? semesterBase : undefined },
    ];

    if (semester) {
        breadcrumbItems.push({
            label: semesterLabel(semester),
            href: current ? `${semesterBase}/${semester.id}/show` : undefined,
        });
    }

    if (current) breadcrumbItems.push({ label: current });

    return (
        <BusinessPage
            title={title}
            subtitle={subtitle}
            breadcrumbItems={breadcrumbItems}
            actions={actions}
            className="semesterPage"
        >
            {children}
        </BusinessPage>
    );
}
