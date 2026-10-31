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
    children,
}: {
    title: string;
    subtitle?: string;
    current: string;
    position?: { id: number; name: string };
    children: ReactNode;
}): React.JSX.Element {
    const breadcrumbItems: PageBreadcrumbItem[] = [
        { label: "Home", href: "/auth/dashboard" },
        { label: "Cargos", href: positionsBase },
    ];

    if (position) {
        breadcrumbItems.push({
            label: position.name,
            href: `${positionsBase}/${position.id}/show`,
        });
    }

    breadcrumbItems.push({ label: current });

    return (
        <BusinessPage
            title={title}
            subtitle={subtitle}
            breadcrumbItems={breadcrumbItems}
            className="positionPage"
        >
            {children}
        </BusinessPage>
    );
}
