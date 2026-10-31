import type { ReactNode } from "react";
import { Link, usePage } from "@inertiajs/react";
import BusinessPage from "@/components/Navigation/BusinessPage/BusinessPage";
import AppLayout from "@/components/Navigation/AppLayout/AppLayout";
import "@/components/Navigation/Tabs/Tabs.css";
import "./Settings.css";

export const settingsBase = "/settings";

// Novas categorias de configuração entram nesta lista, cada uma com sua página.
const categories = [
    {
        key: "mail",
        component: "Settings/Mail",
        subtitle: "Defina o serviço usado pelo Gnai para enviar e-mails, como links de redefinição de senha e notificações.",
        label: "E-mail",
        href: `${settingsBase}/mail`,
        iconClass: "fa fa-envelope",
    },
    {
        key: "backup",
        component: "Settings/Backup",
        subtitle: "Defina por quanto tempo os backups são mantidos e em quais horários o backup automático é executado.",
        label: "Backups",
        href: `${settingsBase}/backups`,
        iconClass: "fa fa-database",
    },
] as const;

export default function SettingsPage({
    children,
}: {
    children: ReactNode;
}): React.JSX.Element {
    const { component } = usePage();
    const activeIndex = Math.max(0, categories.findIndex((item) => item.component === component));
    const current = categories[activeIndex];
    const category = current.key;

    return (
        <BusinessPage
            title="Configurações"
            subtitle={current.subtitle}
            breadcrumbItems={[
                { label: "Home", href: "/auth/dashboard" },
                { label: "Configurações", href: settingsBase },
                { label: current?.label ?? "" },
            ]}
            className="settingsPage"
        >
            <nav
                className="settingsPage__categories"
                aria-label="Categorias de configuração"
            >
                <ul className="tabs__nav--animated">
                    {categories.map((item) => (
                        <li key={item.key}>
                            <Link
                                href={item.href}
                                preserveScroll
                                className={
                                    item.key === category
                                        ? "settingsPage__category settingsPage__category--active"
                                        : "settingsPage__category"
                                }
                                aria-current={
                                    item.key === category ? "page" : undefined
                                }
                            >
                                <i className={item.iconClass} aria-hidden="true" />
                                <span>{item.label}</span>
                            </Link>
                        </li>
                    ))}
                    <li
                        className="tabs__indicator"
                        style={{
                            left: `${(activeIndex / categories.length) * 100}%`,
                            right: `${((categories.length - activeIndex - 1) / categories.length) * 100}%`,
                        }}
                        aria-hidden="true"
                    />
                </ul>
            </nav>
            {children}
        </BusinessPage>
    );
}

export function SettingsLayout({ children }: { children: ReactNode }): React.JSX.Element {
    return (
        <AppLayout>
            <SettingsPage>{children}</SettingsPage>
        </AppLayout>
    );
}
