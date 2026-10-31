import type { ReactNode } from "react";
import BusinessPage from "@/components/Navigation/BusinessPage/BusinessPage";
import type { PageBreadcrumbItem } from "@/components/Navigation/PageBreadcrumb/PageBreadcrumb";
import type { InstitutionRecord } from "./Institution.types";

const base = "/inclusive-radar/institutions";

export default function InstitutionPage({ title, subtitle, current, institution, actions, children }: {
    title: string;
    subtitle?: string;
    current?: string;
    institution?: InstitutionRecord;
    actions?: ReactNode;
    children: ReactNode;
}): React.JSX.Element {
    const breadcrumbItems: PageBreadcrumbItem[] = [
        { label: "Home", href: "/auth/dashboard" },
        { label: "Instituições", href: current || institution ? base : undefined },
    ];
    if (institution) {
        breadcrumbItems.push({
            label: institution.name,
            href: current ? `${base}/${institution.id}` : undefined,
        });
    }
    if (current) breadcrumbItems.push({ label: current });

    return (
        <BusinessPage title={title} subtitle={subtitle} breadcrumbItems={breadcrumbItems} actions={actions} className="institutionPage">
            {children}
        </BusinessPage>
    );
}
