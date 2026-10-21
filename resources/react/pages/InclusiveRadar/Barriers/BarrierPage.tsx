import type { ReactNode } from "react";
import BusinessPage from "@/components/Navigation/BusinessPage/BusinessPage";
import type { PageBreadcrumbItem } from "@/components/Navigation/PageBreadcrumb/PageBreadcrumb";
import type { BarrierRecord } from "./Barrier.types";

const base = "/inclusive-radar/barriers";

export default function BarrierPage({
    title,
    subtitle,
    current,
    barrier,
    actions,
    children,
}: {
    title: string;
    subtitle?: string;
    current?: string;
    barrier?: BarrierRecord;
    actions?: ReactNode;
    children: ReactNode;
}): React.JSX.Element {
    const breadcrumbItems: PageBreadcrumbItem[] = [
        { label: "Home", href: "/auth/dashboard" },
        { label: "Barreiras", href: current || barrier ? base : undefined },
    ];
    if (barrier) {
        breadcrumbItems.push({
            label: barrier.name,
            href: current ? `${base}/${barrier.id}` : undefined,
        });
    }
    if (current) breadcrumbItems.push({ label: current });
    return (
        <BusinessPage
            title={title}
            subtitle={subtitle}
            breadcrumbItems={breadcrumbItems}
            actions={actions}
            className="barrierPage"
        >
            {children}
        </BusinessPage>
    );
}
