import type { ReactNode } from "react";
import BusinessPage from "@/components/Navigation/BusinessPage/BusinessPage";
import type {
    PageBreadcrumbItem,
} from "@/components/Navigation/PageBreadcrumb/PageBreadcrumb";

const base = "/inclusive-radar/assistive-technologies";

export default function AssistiveTechnologyPage({
    title,
    subtitle,
    current,
    technology,
    actions,
    className = "technologyFormPage",
    children,
}: {
    title: string;
    subtitle: string;
    current?: string;
    technology?: { id: number; name: string };
    actions?: ReactNode;
    className?: string;
    children: ReactNode;
}): React.JSX.Element {
    const items: PageBreadcrumbItem[] = [
        { label: "Home", href: "/auth/dashboard" },
        {
            label: "Tecnologias Assistivas",
            href: current ? base : undefined,
        },
    ];
    if (technology) {
        items.push({
            label: technology.name,
            href: `${base}/${technology.id}`,
        });
    }

    if (current) {
        items.push({ label: current, href: undefined });
    }

    return (
        <BusinessPage
            title={title}
            subtitle={subtitle}
            breadcrumbItems={items}
            actions={actions}
            className={className}
        >
            {children}
        </BusinessPage>
    );
}
