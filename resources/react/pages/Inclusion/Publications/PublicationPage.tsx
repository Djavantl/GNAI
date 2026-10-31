import type { ReactNode } from "react";
import BusinessPage from "@/components/Navigation/BusinessPage/BusinessPage";
import type { PageBreadcrumbItem } from "@/components/Navigation/PageBreadcrumb/PageBreadcrumb";

const base = "/inclusive-radar/publications";

export default function PublicationPage({
    title,
    subtitle,
    current,
    publicationTitle,
    publicationId,
    backRoute,
    actions,
    children,
}: {
    title: string;
    subtitle: string;
    current?: string;
    publicationTitle?: string;
    publicationId?: number;
    backRoute?: string;
    actions?: ReactNode;
    children: ReactNode;
}): React.JSX.Element {
    const breadcrumbs: PageBreadcrumbItem[] = [
        { label: "Home", href: "/auth/dashboard" },
        { label: "Site" },
        {
            label: "Publicações",
            href: current || publicationId ? backRoute || base : undefined,
        },
    ];

    if (publicationId) {
        breadcrumbs.push({
            label: publicationTitle || `Publicação #${publicationId}`,
            href: current ? `${base}/${publicationId}` : undefined,
        });
    }

    if (current) breadcrumbs.push({ label: current });

    return (
        <BusinessPage
            title={title}
            subtitle={subtitle}
            breadcrumbItems={breadcrumbs}
            actions={actions}
            className="publicationPage"
        >
            {children}
        </BusinessPage>
    );
}
