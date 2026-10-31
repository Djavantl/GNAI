import type { ReactNode } from "react";
import BusinessPage from "@/components/Navigation/BusinessPage/BusinessPage";
import type { PageBreadcrumbItem } from "@/components/Navigation/PageBreadcrumb/PageBreadcrumb";

const base = "/inclusive-radar/accessibility-features";

export default function AccessibilityFeaturePage({
    title,
    subtitle,
    current,
    feature,
    actions,
    children,
}: {
    title: string;
    subtitle?: string;
    current?: string;
    feature?: { id: number; name: string };
    actions?: ReactNode;
    children: ReactNode;
}): React.JSX.Element {
    const breadcrumbItems: PageBreadcrumbItem[] = [
        { label: "Home", href: "/auth/dashboard" },
        { label: "Recursos de Acessibilidade", href: current ? base : undefined },
    ];

    if (feature) {
        breadcrumbItems.push({
            label: feature.name,
            href: current ? `${base}/${feature.id}` : undefined,
        });
    }

    if (current) breadcrumbItems.push({ label: current });

    return (
        <BusinessPage
            title={title}
            subtitle={subtitle}
            breadcrumbItems={breadcrumbItems}
            actions={actions}
            className="accessibilityFeaturePage"
        >
            {children}
        </BusinessPage>
    );
}
