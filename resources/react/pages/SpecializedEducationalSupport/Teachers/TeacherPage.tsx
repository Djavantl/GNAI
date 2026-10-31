import type { ReactNode } from "react";
import BusinessPage from "@/components/Navigation/BusinessPage/BusinessPage";
import type { PageBreadcrumbItem } from "@/components/Navigation/PageBreadcrumb/PageBreadcrumb";
import { teachersBase, teacherShowUrl } from "./Teachers.utils";
import "./Teachers.css";

export default function TeacherPage({
    title,
    subtitle,
    current,
    actions,
    teacher,
    children,
}: {
    title: string;
    subtitle?: string;
    current?: string;
    actions?: ReactNode;
    teacher?: { id: number; name: string };
    children: ReactNode;
}): React.JSX.Element {
    const breadcrumbItems: PageBreadcrumbItem[] = [
        { label: "Home", href: "/auth/dashboard" },
        { label: "Professores", href: teachersBase },
    ];

    if (teacher) {
        breadcrumbItems.push({
            label: teacher.name,
            href: teacherShowUrl(teacher.id),
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
            className="teacherPage"
        >
            {children}
        </BusinessPage>
    );
}
