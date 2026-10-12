import type { ReactNode } from "react";
import { Head } from "@inertiajs/react";
import PageBreadcrumb, {
    type PageBreadcrumbItem,
} from "@/components/Navigation/PageBreadcrumb/PageBreadcrumb";
import PageTitle from "@/components/Navigation/PageTitle/PageTitle";
import FlashNotifications from "@/components/UI/FlashNotifications/FlashNotifications";

const base = "/inclusive-radar/assistive-technologies";

export default function AssistiveTechnologyPage({
    title,
    subtitle,
    current,
    technology,
    actions,
    children,
}: {
    title: string;
    subtitle: string;
    current: string;
    technology?: { id: number; name: string };
    actions?: ReactNode;
    children: ReactNode;
}): React.JSX.Element {
    const items: PageBreadcrumbItem[] = [
        { label: "Home", href: "/auth/dashboard" },
        { label: "Tecnologias Assistivas", href: base },
    ];
    if (technology)
        items.push({
            label: technology.name,
            href: `${base}/${technology.id}`,
        });
    items.push({ label: current, href: undefined });
    return (
        <main className="technologyFormPage">
            <Head title={`${title} - Gnai`} />
            <nav aria-label="Localização">
                <PageBreadcrumb items={items} />
            </nav>
            <PageTitle title={title} subtitle={subtitle} actions={actions} />
            <FlashNotifications />
            {children}
        </main>
    );
}
