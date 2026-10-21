import type { ReactNode } from "react";
import BusinessPage from "@/components/Navigation/BusinessPage/BusinessPage";
import type { PageBreadcrumbItem } from "@/components/Navigation/PageBreadcrumb/PageBreadcrumb";

const base = "/inclusive-radar/barrier-categories";

export default function BarrierCategoryPage({
    title,
    subtitle,
    current,
    category,
    actions,
    children,
}: {
    title: string;
    subtitle?: string;
    current?: string;
    category?: { id: number; name: string };
    actions?: ReactNode;
    children: ReactNode;
}): React.JSX.Element {
    const breadcrumbItems: PageBreadcrumbItem[] = [
        { label: "Home", href: "/auth/dashboard" },
        { label: "Categorias de Barreiras", href: current ? base : undefined },
    ];

    if (category) {
        breadcrumbItems.push({
            label: category.name,
            href: current ? `${base}/${category.id}` : undefined,
        });
    }

    if (current) breadcrumbItems.push({ label: current });

    return (
        <BusinessPage
            title={title}
            subtitle={subtitle}
            breadcrumbItems={breadcrumbItems}
            actions={actions}
            className="barrierCategoryPage"
        >
            {children}
        </BusinessPage>
    );
}
