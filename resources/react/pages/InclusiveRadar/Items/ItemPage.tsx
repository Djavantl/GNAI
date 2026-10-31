import type { ReactNode } from "react";
import BusinessPage from "@/components/Navigation/BusinessPage/BusinessPage";
import type { PageBreadcrumbItem } from "@/components/Navigation/PageBreadcrumb/PageBreadcrumb";
import { itemsBase as base } from "./Items.types";

export default function ItemPage({
    title,
    subtitle,
    current,
    item,
    actions,
    children,
}: {
    title: string;
    subtitle: string;
    current?: string;
    item?: { id: number; name: string };
    actions?: ReactNode;
    children: ReactNode;
}): React.JSX.Element {
    const items: PageBreadcrumbItem[] = [
        { label: "Home", href: "/auth/dashboard" },
        {
            label: "Itens",
            href: current ? base : undefined,
        },
    ];

    if (item) {
        items.push({ label: item.name, href: `${base}/${item.id}` });
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
            className="itemFormPage"
        >
            {children}
        </BusinessPage>
    );
}
