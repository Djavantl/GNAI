import type { ReactNode } from "react";
import SiteLink from "../SiteLayout/SiteLink";
import { websiteRoutes } from "../Website.types";
import "./SitePageHeader.css";

/** Faixa de título das páginas internas do site, com trilha de navegação. */
export default function SitePageHeader({
    eyebrow,
    title,
    subtitle,
    trail = [],
    children,
}: {
    eyebrow?: string;
    title: string;
    subtitle?: string | null;
    trail?: { label: string; href?: string }[];
    children?: ReactNode;
}): React.JSX.Element {
    return (
        <section className="sitePageHeader">
            <div className="siteLayout__container">
                <nav aria-label="Você está em" className="sitePageHeader__trail">
                    <ol>
                        <li><SiteLink href={websiteRoutes.home}>Início</SiteLink></li>
                        {trail.map((item) => (
                            <li key={item.label}>
                                {item.href ? (
                                    <SiteLink href={item.href}>{item.label}</SiteLink>
                                ) : (
                                    <span aria-current="page">{item.label}</span>
                                )}
                            </li>
                        ))}
                    </ol>
                </nav>
                {eyebrow ? <p className="sitePageHeader__eyebrow">{eyebrow}</p> : null}
                <h1 className="sitePageHeader__title">{title}</h1>
                {subtitle ? <p className="sitePageHeader__subtitle">{subtitle}</p> : null}
                {children}
            </div>
        </section>
    );
}
