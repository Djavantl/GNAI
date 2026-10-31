import type { ReactNode } from "react";
import BusinessPage from "@/components/Navigation/BusinessPage/BusinessPage";
import type { PageBreadcrumbItem } from "@/components/Navigation/PageBreadcrumb/PageBreadcrumb";

export const disciplineBase = "/specialized-educational-support/disciplines";

export default function DisciplinePage({
    title,
    subtitle,
    current,
    discipline,
    actions,
    children,
}: {
    title: string;
    subtitle?: string;
    current?: string;
    discipline?: { id: number; name: string };
    actions?: ReactNode;
    children: ReactNode;
}): React.JSX.Element {
    const breadcrumbItems: PageBreadcrumbItem[] = [
        { label: "Home", href: "/auth/dashboard" },
        { label: "Disciplinas", href: current || discipline ? disciplineBase : undefined },
    ];

    if (discipline) {
        breadcrumbItems.push({
            label: discipline.name,
            href: current ? `${disciplineBase}/${discipline.id}/show` : undefined,
        });
    }

    if (current) breadcrumbItems.push({ label: current });

    return (
        <BusinessPage
            title={title}
            subtitle={subtitle}
            breadcrumbItems={breadcrumbItems}
            actions={actions}
            className="disciplinePage"
        >
            {children}
        </BusinessPage>
    );
}
