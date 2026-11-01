import type { CSSProperties } from "react";
import { appearanceColor as color } from "../WebsiteAppearance";
import type { WebsiteSection } from "../Website.types";
import SiteItemCard from "../SiteItemCard/SiteItemCard";
import "./SitePillars.css";

export default function SitePillars({ section }: { section: WebsiteSection }): React.JSX.Element | null {
    const appearance = section.pillars;
    if (!appearance) return null;
    const background = color(
        appearance.background_color,
        ["pillars", "resources"].includes(section.type) ? "var(--site-surface)" : "#ffffff",
    );
    const style = {
        "--pillars-padding": `${appearance.padding_vertical}px`,
        "--pillars-gap": `${appearance.gap}px`,
        "--pillars-columns": appearance.columns,
        "--pillars-title-size": `${appearance.title_size}px`,
        "--pillars-subtitle-size": `${appearance.subtitle_size}px`,
        "--pillars-divider-color": color(appearance.divider_color, "var(--site-primary)"),
        background: appearance.gradient_enabled
            ? `linear-gradient(${appearance.gradient_direction}, ${background}, ${color(appearance.gradient_end_color, "var(--site-primary-soft)")})`
            : background,
    } as CSSProperties;

    return (
        <section
            className={`sitePillars sitePillars--type-${section.type} sitePillars--${appearance.layout}${appearance.title_divider ? "" : " sitePillars--noDivider"}`}
            style={style}
            data-columns={appearance.columns}
        >
            <div className="siteLayout__container">
                {section.title ? (
                    <h2
                        className="siteSections__title sitePillars__title"
                        data-title-align={appearance.title_align}
                        style={{ color: color(appearance.title_color, "var(--site-primary)"), textAlign: appearance.title_align }}
                    >
                        {section.title}
                    </h2>
                ) : null}
                {section.subtitle ? (
                    <p
                        className="siteSections__subtitle sitePillars__subtitle"
                        style={{ color: color(appearance.subtitle_color, "var(--site-ink-muted)"), textAlign: appearance.subtitle_align }}
                    >
                        {section.subtitle}
                    </p>
                ) : null}
                <ul className="sitePillars__grid">
                    {section.items.map((item, index) => (
                        <SiteItemCard
                            key={index}
                            item={item}
                            index={index}
                            faq={section.type === "faq"}
                            resource={section.type === "resources"}
                        />
                    ))}
                </ul>
            </div>
        </section>
    );
}
