import type { ReactNode } from "react";
import { Link } from "@inertiajs/react";
import BusinessPage from "@/components/Navigation/BusinessPage/BusinessPage";
import "./Settings.css";

export const settingsBase = "/settings";

// Novas categorias de configuração entram nesta lista, cada uma com sua página.
const categories = [
    {
        key: "mail",
        label: "E-mail",
        href: `${settingsBase}/mail`,
        iconClass: "fa fa-envelope",
    },
    {
        key: "backup",
        label: "Backups",
        href: `${settingsBase}/backups`,
        iconClass: "fa fa-database",
    },
] as const;

export type SettingsCategory = (typeof categories)[number]["key"];

export default function SettingsPage({
    category,
    subtitle,
    children,
}: {
    category: SettingsCategory;
    subtitle: string;
    children: ReactNode;
}): React.JSX.Element {
    const current = categories.find((item) => item.key === category);

    return (
        <BusinessPage
            title="Configurações"
            subtitle={subtitle}
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
                <ul>
                    {categories.map((item) => (
                        <li key={item.key}>
                            <Link
                                href={item.href}
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
                </ul>
            </nav>
            {children}
        </BusinessPage>
    );
}
