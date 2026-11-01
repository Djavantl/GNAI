import type { ReactNode } from "react";
import { usePage } from "@inertiajs/react";
import AppFooter from "@/components/Navigation/AppFooter/AppFooter";
import ImpersonationIndicator from "@/components/Navigation/ImpersonationIndicator/ImpersonationIndicator";
import InstitutionBrand from "@/components/Navigation/InstitutionBrand/InstitutionBrand";
import SidebarPrimary from "@/components/Navigation/SidebarPrimary/SidebarPrimary";
import TopBarPrimary from "@/components/Navigation/TopBarPrimary/TopBarPrimary";
import type { ProfileLink } from "@/components/Navigation/TopBarPrimary/TopBarPrimary.data";
import FlashNotifications from "@/components/UI/FlashNotifications/FlashNotifications";
import PageErrorBoundary from "@/components/UI/PageErrorBoundary/PageErrorBoundary";
import PageScrollbar from "@/components/UI/PageScrollbar/PageScrollbar";
import "@/styles/app-shell.css";
import type { AppLayoutProps } from "./AppLayout.types";
import { useAppSidebar } from "./useAppSidebar";
import "./AppLayout.css";

export default function AppLayout({ children }: { children: ReactNode }): React.JSX.Element {
    const { props, url } = usePage<AppLayoutProps>();
    const sidebar = useAppSidebar(url);
    const { auth, navigation, topbar, impersonating, impersonationReadOnly } = props;

    if (!auth.user) {
        return <>{children}</>;
    }

    const profileItems: ProfileLink[] = [
        { label: "Perfil", iconClass: "ti-user", href: "/auth/perfil" },
        ...(impersonating
            ? [
                  {
                      label: "Voltar para a minha conta",
                      iconClass: "ti-back-left",
                      href: "/auth/impersonate/leave",
                      method: "post" as const,
                  },
              ]
            : []),
        {
            label: "Sair",
            iconClass: "ti-power-off",
            href: "/auth/logout",
            method: "post",
        },
    ];

    return (
        <div
            className={`app-page gnaiApp${sidebar.collapsed ? " app-page--sidebarCollapsed" : ""}`}
        >
            <TopBarPrimary
                brand="Gnai"
                compactLogoSrc="/images/logo2.png"
                expandedLogoText="NAI"
                homeHref="/auth/dashboard"
                sidebarCollapsed={sidebar.collapsed}
                onToggleSidebar={sidebar.toggle}
                user={auth.user}
                profileItems={profileItems}
                notificationItems={topbar?.notifications ?? []}
                unreadCount={topbar?.unreadCount ?? 0}
                notificationsHref="/notifications"
                showSettings={false}
                actionsContent={
                    impersonating ? (
                        <ImpersonationIndicator
                            name={auth.user.name}
                            readOnly={impersonationReadOnly}
                        />
                    ) : undefined
                }
                leadingContent={
                    <InstitutionBrand
                        name={topbar?.institutionName ?? "Gnai"}
                        href={
                            navigation.entries.some(
                                (item) => item.key === "institutions",
                            )
                                ? "/inclusive-radar/institutions"
                                : undefined
                        }
                    />
                }
            />
            <SidebarPrimary
                collapsed={sidebar.collapsed}
                entries={navigation.entries}
                currentItemKey={navigation.currentItemKey ?? undefined}
            />
            {!sidebar.collapsed ? (
                <button
                    className="gnaiApp__overlay"
                    type="button"
                    onClick={sidebar.closeOnMobile}
                    aria-label="Fechar menu lateral"
                />
            ) : null}
            <div className="app-content-placeholder">
                <div className="app-content-placeholder__inner">
                    <PageErrorBoundary resetKey={url} homeHref="/auth/dashboard">
                        {children}
                    </PageErrorBoundary>
                </div>
                <AppFooter>
                    Gnai &copy; {new Date().getFullYear()} | Gestão NAIs
                </AppFooter>
            </div>
            <FlashNotifications />
            <PageScrollbar />
        </div>
    );
}
