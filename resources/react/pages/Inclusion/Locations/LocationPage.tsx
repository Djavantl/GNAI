import type { ReactNode } from "react";
import BusinessPage from "@/components/Navigation/BusinessPage/BusinessPage";
import type { PageBreadcrumbItem } from "@/components/Navigation/PageBreadcrumb/PageBreadcrumb";
import type { LocationRecord } from "./Location.types";

const base = "/inclusive-radar/locations";

export default function LocationPage({
    title,
    subtitle,
    current,
    location,
    actions,
    children,
}: {
    title: string;
    subtitle?: string;
    current?: string;
    location?: LocationRecord;
    actions?: ReactNode;
    children: ReactNode;
}): React.JSX.Element {
    const breadcrumbItems: PageBreadcrumbItem[] = [
        { label: "Home", href: "/auth/dashboard" },
        { label: "Pontos de Referência", href: current || location ? base : undefined },
    ];

    if (location) {
        breadcrumbItems.push({
            label: location.name,
            href: current ? `${base}/${location.id}` : undefined,
        });
    }
    if (current) breadcrumbItems.push({ label: current });

    return (
        <BusinessPage
            title={title}
            subtitle={subtitle}
            breadcrumbItems={breadcrumbItems}
            actions={actions}
            className="locationPage"
        >
            {children}
        </BusinessPage>
    );
}
