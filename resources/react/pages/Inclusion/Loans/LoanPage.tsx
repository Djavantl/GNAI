import type { ReactNode } from "react";
import BusinessPage from "@/components/Navigation/BusinessPage/BusinessPage";
import type { PageBreadcrumbItem } from "@/components/Navigation/PageBreadcrumb/PageBreadcrumb";

const base = "/inclusive-radar/loans";

export default function LoanPage({
    title,
    subtitle,
    current,
    loan,
    actions,
    className = "loanPage",
    children,
}: {
    title: string;
    subtitle: string;
    current?: string;
    loan?: { id: number };
    actions?: ReactNode;
    className?: string;
    children: ReactNode;
}): React.JSX.Element {
    const items: PageBreadcrumbItem[] = [
        { label: "Home", href: "/auth/dashboard" },
        { label: "Empréstimos", href: current || loan ? base : undefined },
    ];

    if (loan) {
        items.push({
            label: `Empréstimo #${loan.id}`,
            href: current ? `${base}/${loan.id}` : undefined,
        });
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
            className={className}
        >
            {children}
        </BusinessPage>
    );
}
