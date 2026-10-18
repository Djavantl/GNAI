import type { ReactNode } from "react";
import BusinessPage from "@/components/Navigation/BusinessPage/BusinessPage";
import type { PageBreadcrumbItem } from "@/components/Navigation/PageBreadcrumb/PageBreadcrumb";

const base = "/inclusive-radar/accessible-educational-materials";

export default function AccessibleEducationalMaterialPage({
    title,
    subtitle,
    current,
    material,
    actions,
    children,
}: {
    title: string;
    subtitle: string;
    current?: string;
    material?: { id: number; name: string };
    actions?: ReactNode;
    children: ReactNode;
}): React.JSX.Element {
    const items: PageBreadcrumbItem[] = [
        { label: "Home", href: "/auth/dashboard" },
        {
            label: "Materiais Pedagógicos Acessíveis",
            href: current ? base : undefined,
        },
    ];

    if (material) {
        items.push({ label: material.name, href: `${base}/${material.id}` });
    }

    if (current) {
        items.push({ label: current });
    }

    return (
        <BusinessPage
            title={title}
            subtitle={subtitle}
            breadcrumbItems={items}
            actions={actions}
            className="materialFormPage"
        >
            {children}
        </BusinessPage>
    );
}
