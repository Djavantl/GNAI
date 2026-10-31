import type { ReactNode } from "react";
import BusinessPage from "@/components/Navigation/BusinessPage/BusinessPage";
import type { PageBreadcrumbItem } from "@/components/Navigation/PageBreadcrumb/PageBreadcrumb";
import { professionalsBase, professionalShowUrl } from "./Professionals.utils";
import "./Professionals.css";

export default function ProfessionalPage({
    title,
    subtitle,
    current,
    professional,
    children,
}: {
    title: string;
    subtitle?: string;
    current: string;
    professional?: { id: number; name: string };
    children: ReactNode;
}): React.JSX.Element {
    const breadcrumbItems: PageBreadcrumbItem[] = [
        { label: "Home", href: "/auth/dashboard" },
        { label: "Profissionais", href: professionalsBase },
    ];

    if (professional) {
        breadcrumbItems.push({
            label: professional.name,
            href: professionalShowUrl(professional.id),
        });
    }

    breadcrumbItems.push({ label: current });

    return (
        <BusinessPage
            title={title}
            subtitle={subtitle}
            breadcrumbItems={breadcrumbItems}
            className="professionalPage"
        >
            {children}
        </BusinessPage>
    );
}
