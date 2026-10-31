import type { ReactNode } from "react";
import BusinessPage from "@/components/Navigation/BusinessPage/BusinessPage";
import type { PageBreadcrumbItem } from "@/components/Navigation/PageBreadcrumb/PageBreadcrumb";
import "./SessionList.css";

export default function SessionPage({
    title,
    subtitle,
    breadcrumbItems,
    actions,
    children,
}: {
    title: string;
    subtitle?: string;
    breadcrumbItems?: PageBreadcrumbItem[];
    actions?: ReactNode;
    children: ReactNode;
}): React.JSX.Element {
    const crumbs: PageBreadcrumbItem[] = breadcrumbItems ?? [
        { label: "Home", href: "/auth/dashboard" },
        { label: "Agendamentos" },
    ];

    return (
        <BusinessPage
            title={title}
            subtitle={subtitle}
            breadcrumbItems={crumbs}
            actions={actions}
            className="sessionsPage"
        >
            {children}
        </BusinessPage>
    );
}
