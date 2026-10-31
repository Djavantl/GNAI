import type { ReactNode } from "react";
import BusinessPage from "@/components/Navigation/BusinessPage/BusinessPage";
import type { PageBreadcrumbItem } from "@/components/Navigation/PageBreadcrumb/PageBreadcrumb";
import "./Positions.css";

export const positionsBase = "/specialized-educational-support/positions";

export default function PositionPage({
    title,
    subtitle,
    current,
    position,
    actions,
    children,
}: {
    title: string;
    subtitle?: string;
    current?: string;
    position?: { id: number; name: string };
    actions?: ReactNode;
    children: ReactNode;
}): React.JSX.Element {
    const breadcrumbItems: PageBreadcrumbItem[] = [
        { label: "Home", href: "/auth/dashboard" },
        { label: "Cargos", href: current || position ? positionsBase : undefined },
    ];

    if (position) {
        breadcrumbItems.push({
            label: position.name,
            href: current ? `${positionsBase}/${position.id}/show` : undefined,
        });
    }

    if (current) breadcrumbItems.push({ label: current });

    return (
        <BusinessPage
            title={title}
            subtitle={subtitle}
            breadcrumbItems={breadcrumbItems}
            actions={actions}
            className="positionPage"
        >
            {children}
        </BusinessPage>
    );
}
