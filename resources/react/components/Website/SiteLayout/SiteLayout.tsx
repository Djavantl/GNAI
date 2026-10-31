import { Head } from "@inertiajs/react";
import { useState, type CSSProperties, type ReactNode } from "react";
import { joinClassNames } from "@/utils/joinClassNames";
import { websiteRoutes, type WebsiteSeo, type WebsiteSite } from "../Website.types";
import SiteLink, { SitePreviewContext } from "./SiteLink";
import "./SiteLayout.css";

export type SitePage = "home" | "team" | "publications";

const navigation: { key: SitePage; label: string; href: string }[] = [
    { key: "home", label: "Início", href: websiteRoutes.home },
    { key: "team", label: "Equipe", href: websiteRoutes.team },
    { key: "publications", label: "Publicações", href: websiteRoutes.publications },
];

const socialNetworks = [
    { key: "instagram", label: "Instagram", icon: "fa fa-instagram" },
    { key: "facebook", label: "Facebook", icon: "fa fa-facebook" },
    { key: "youtube", label: "YouTube", icon: "fa fa-youtube-play" },
    { key: "website", label: "Site", icon: "fa fa-globe" },
] as const;

function SiteBrand({ site }: { site: WebsiteSite }): React.JSX.Element {
    return (
        <SiteLink href={websiteRoutes.home} className="siteLayout__brand" ariaLabel={`${site.name}, página inicial`}>
            {site.logo_url ? (
                <img src={site.logo_url} alt="" className="siteLayout__logo" />
            ) : (
                <span className="siteLayout__monogram" aria-hidden="true">
                    {site.name.slice(0, 1).toUpperCase()}
                </span>
            )}
            <span className="siteLayout__brandText">
                <span className="siteLayout__brandName">{site.name}</span>
                {site.tagline ? (
                    <span className="siteLayout__brandTagline">{site.tagline}</span>
                ) : null}
            </span>
        </SiteLink>
    );
}

export function SiteSocialLinks({
    site,
    className,
}: {
    site: WebsiteSite;
    className?: string;
}): React.JSX.Element | null {
    const links = socialNetworks.filter((network) => site.social[network.key]);

    if (!links.length) return null;

    return (
        <ul className={joinClassNames("siteLayout__social", className)}>
            {links.map((network) => (
                <li key={network.key}>
                    <SiteLink
                        href={site.social[network.key] ?? "#"}
                        ariaLabel={`${network.label} de ${site.name} (abre em nova aba)`}
                    >
                        <i className={network.icon} aria-hidden="true" />
                    </SiteLink>
                </li>
            ))}
        </ul>
    );
}

/**
 * Estrutura do site público white label: cabeçalho, navegação e rodapé.
 * A cor principal configurada na retaguarda alimenta --site-primary.
 */
export default function SiteLayout({
    site,
    current,
    seo,
    preview = false,
    children,
}: {
    site: WebsiteSite;
    current?: SitePage;
    seo?: WebsiteSeo;
    preview?: boolean;
    children: ReactNode;
}): React.JSX.Element {
    const [menuOpen, setMenuOpen] = useState(false);
    const style = { "--site-primary": site.primary_color } as CSSProperties;
    const year = new Date().getFullYear();

    return (
        <SitePreviewContext.Provider value={preview}>
            {!preview && seo ? (
                <Head title={seo.title}>
                    {seo.description ? <meta head-key="description" name="description" content={seo.description} /> : null}
                    {seo.description ? <meta head-key="og:description" property="og:description" content={seo.description} /> : null}
                    <meta head-key="og:title" property="og:title" content={seo.title} />
                    {seo.image ? <meta head-key="og:image" property="og:image" content={seo.image} /> : null}
                </Head>
            ) : null}
            <div className={joinClassNames("siteLayout", preview && "siteLayout--preview")} style={style}>
                {!preview ? (
                    <a href="#site-content" className="siteLayout__skip">Pular para o conteúdo</a>
                ) : null}
                <header className="siteLayout__header">
                    <div className="siteLayout__container siteLayout__headerInner">
                        <SiteBrand site={site} />
                        <button
                            type="button"
                            className="siteLayout__menuToggle"
                            aria-expanded={menuOpen}
                            aria-controls="site-navigation"
                            aria-label={menuOpen ? "Fechar menu" : "Abrir menu"}
                            title={menuOpen ? "Fechar menu" : "Abrir menu"}
                            onClick={() => setMenuOpen((open) => !open)}
                        >
                            <i className={menuOpen ? "fa fa-times" : "fa fa-bars"} aria-hidden="true" />
                        </button>
                        <nav
                            id="site-navigation"
                            className={joinClassNames("siteLayout__nav", menuOpen && "siteLayout__nav--open")}
                            aria-label="Navegação do site"
                        >
                            <ul>
                                {navigation.map((item) => (
                                    <li key={item.key}>
                                        <SiteLink
                                            href={item.href}
                                            className={joinClassNames(
                                                "siteLayout__navLink",
                                                current === item.key && "siteLayout__navLink--active",
                                            )}
                                            ariaCurrent={current === item.key ? "page" : undefined}
                                        >
                                            {item.label}
                                        </SiteLink>
                                    </li>
                                ))}
                            </ul>
                        </nav>
                    </div>
                </header>
                {preview ? (
                    <div className="siteLayout__main">{children}</div>
                ) : (
                    <main id="site-content" className="siteLayout__main" tabIndex={-1}>
                        {children}
                    </main>
                )}
                <footer className="siteLayout__footer">
                    <div className="siteLayout__container siteLayout__footerGrid">
                        <div className="siteLayout__footerBrand">
                            <SiteBrand site={site} />
                            {site.footer_text ? <p>{site.footer_text}</p> : null}
                            <SiteSocialLinks site={site} />
                        </div>
                        <nav aria-label="Links do rodapé">
                            <p className="siteLayout__footerTitle">Navegação</p>
                            <ul className="siteLayout__footerLinks">
                                {navigation.map((item) => (
                                    <li key={item.key}>
                                        <SiteLink href={item.href}>{item.label}</SiteLink>
                                    </li>
                                ))}
                            </ul>
                        </nav>
                        {site.contact_email || site.contact_phone || site.address ? (
                            <div>
                                <p className="siteLayout__footerTitle">Contato</p>
                                <address className="siteLayout__footerContact">
                                    {site.address ? (
                                        <span><i className="fa fa-map-marker" aria-hidden="true" /> {site.address}</span>
                                    ) : null}
                                    {site.contact_phone ? (
                                        <a href={`tel:${site.contact_phone.replace(/[^\d+]/g, "")}`}>
                                            <i className="fa fa-phone" aria-hidden="true" /> {site.contact_phone}
                                        </a>
                                    ) : null}
                                    {site.contact_email ? (
                                        <a href={`mailto:${site.contact_email}`}>
                                            <i className="fa fa-envelope-o" aria-hidden="true" /> {site.contact_email}
                                        </a>
                                    ) : null}
                                </address>
                            </div>
                        ) : null}
                    </div>
                    <div className="siteLayout__container siteLayout__copyright">
                        <span>© {year} {site.name}. Todos os direitos reservados.</span>
                        <span>Plataforma <SiteLink href="/about-us">Gnai</SiteLink></span>
                    </div>
                </footer>
            </div>
        </SitePreviewContext.Provider>
    );
}
