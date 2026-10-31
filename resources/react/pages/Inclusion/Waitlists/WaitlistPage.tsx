import type { ReactNode } from "react";
import BusinessPage from "@/components/Navigation/BusinessPage/BusinessPage";
import type { PageBreadcrumbItem } from "@/components/Navigation/PageBreadcrumb/PageBreadcrumb";

const base = "/inclusive-radar/waitlists";

export default function WaitlistPage({
    title,
    subtitle,
    current,
    waitlistId,
    actions,
    children,
}: {
    title: string;
    subtitle: string;
    current?: string;
    waitlistId?: number;
    actions?: ReactNode;
    children: ReactNode;
}): React.JSX.Element {
    const breadcrumbs: PageBreadcrumbItem[] = [
        { label: "Home", href: "/auth/dashboard" },
        {
            label: "Fila de Espera",
            href: current || waitlistId ? base : undefined,
        },
    ];

    if (waitlistId) {
        breadcrumbs.push({
            label: `Solicitação #${waitlistId}`,
            href: current ? `${base}/${waitlistId}` : undefined,
        });
    }

    if (current) breadcrumbs.push({ label: current });

    return (
        <BusinessPage
            title={title}
            subtitle={subtitle}
            breadcrumbItems={breadcrumbs}
            actions={actions}
            className="waitlistPage"
        >
            {children}
        </BusinessPage>
    );
}
