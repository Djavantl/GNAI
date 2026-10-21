import type { ReactNode } from "react";
import BusinessPage from "@/components/Navigation/BusinessPage/BusinessPage";
import type { PageBreadcrumbItem } from "@/components/Navigation/PageBreadcrumb/PageBreadcrumb";

export default function BackupPage({
    title,
    subtitle,
    currentFile,
    actions,
    children,
}: {
    title: string;
    subtitle?: string;
    currentFile?: string;
    actions?: ReactNode;
    children: ReactNode;
}): React.JSX.Element {
    const breadcrumbItems: PageBreadcrumbItem[] = [
        { label: "Home", href: "/auth/dashboard" },
        { label: "Backups", href: currentFile ? "/backup/backups" : undefined },
    ];
    if (currentFile) breadcrumbItems.push({ label: currentFile });

    return (
        <BusinessPage
            title={title}
            subtitle={subtitle}
            breadcrumbItems={breadcrumbItems}
            actions={actions}
            className="backupPage"
        >
            {children}
        </BusinessPage>
    );
}
