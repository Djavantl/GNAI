import type { ReactNode } from "react";
import { usePage } from "@inertiajs/react";
import BusinessPage from "@/components/Navigation/BusinessPage/BusinessPage";
import AppLayout from "@/components/Navigation/AppLayout/AppLayout";
import PageTabs from "@/components/Navigation/PageTabs/PageTabs";
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
    const current = categories.find((item) => item.component === component) ?? categories[0];
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
            <PageTabs
                items={categories}
                activeKey={category}
                ariaLabel="Categorias de configuração"
            />
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
