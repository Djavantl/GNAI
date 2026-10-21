import type { ReactNode } from "react";
import BusinessPage from "@/components/Navigation/BusinessPage/BusinessPage";
import type { PageBreadcrumbItem } from "@/components/Navigation/PageBreadcrumb/PageBreadcrumb";

const base = "/inclusive-radar/institutional-events";

export default function InstitutionalEventPage({
    title,
    subtitle,
    current,
    eventTitle,
    eventId,
    backRoute,
    actions,
    children,
}: {
    title: string;
    subtitle: string;
    current?: string;
    eventTitle?: string;
    eventId?: number;
    backRoute?: string;
    actions?: ReactNode;
    children: ReactNode;
}): React.JSX.Element {
    const breadcrumbs: PageBreadcrumbItem[] = [
        { label: "Home", href: "/auth/dashboard" },
        {
            label: "Agenda Institucional",
            href: current || eventId ? backRoute || base : undefined,
        },
    ];

    if (eventId) {
        breadcrumbs.push({
            label: eventTitle || `Evento #${eventId}`,
            href: current ? `${base}/${eventId}${backRoute ? `?back=${encodeURIComponent(backRoute)}` : ""}` : undefined,
        });
    }

    if (current) breadcrumbs.push({ label: current });

    return (
        <BusinessPage
            title={title}
            subtitle={subtitle}
            breadcrumbItems={breadcrumbs}
            actions={actions}
            className="institutionalEventPage"
        >
            {children}
        </BusinessPage>
    );
}
