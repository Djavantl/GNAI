import type { CSSProperties } from "react";
import { appearanceColor as color } from "../WebsiteAppearance";
import type { PillarCardAppearance, WebsiteSection, WebsiteSectionItem } from "../Website.types";
import "./SitePillars.css";

function cardStyle(appearance: PillarCardAppearance): CSSProperties {
    const background = color(appearance.card_background, "#ffffff");
    const end = color(appearance.gradient_end_color, "var(--site-primary-soft)");
    const hoverBackground = color(appearance.hover_background, background);
    const shadow = (value: number) => value ? `0 ${value / 3}px ${value}px rgba(0,0,0,0.15)` : "none";
    return {
        "--pillar-card-width": `${appearance.card_width}%`,
        "--pillar-card-height": `${appearance.card_min_height}px`,
        "--pillar-card-padding": `${appearance.card_padding}px`,
        "--pillar-card-radius": `${appearance.card_radius}px`,
        "--pillar-card-border-width": `${appearance.card_border_width}px`,
        "--pillar-card-border": color(appearance.card_border_color, "var(--site-border)"),
        "--pillar-card-background": appearance.gradient_enabled
            ? `linear-gradient(${appearance.gradient_direction}, ${background}, ${end})` : background,
        "--pillar-card-shadow": shadow(appearance.card_shadow),
        "--pillar-hover-background": appearance.gradient_enabled
            ? `linear-gradient(${appearance.gradient_direction}, ${hoverBackground}, ${end})` : hoverBackground,
        "--pillar-hover-border": color(appearance.hover_border_color, "var(--pillar-card-border)"),
        "--pillar-hover-lift": `-${appearance.hover_lift}px`,
        "--pillar-hover-shadow": shadow(appearance.hover_shadow),
        "--pillar-icon-color": color(appearance.icon_color, "var(--site-primary)"),
        "--pillar-icon-background": color(appearance.icon_background, "var(--site-primary-soft)"),
        "--pillar-icon-size": `${appearance.icon_size}px`,
        "--pillar-icon-width": `${appearance.icon_width}px`,
        "--pillar-icon-height": `${appearance.icon_height}px`,
        "--pillar-icon-radius": `${appearance.icon_radius}px`,
        "--pillar-icon-gap": `${appearance.icon_gap}px`,
        "--pillar-icon-align": appearance.icon_align === "left" ? "start" : appearance.icon_align === "right" ? "end" : "center",
        "--pillar-title-color": color(appearance.title_color, "var(--site-ink)"),
        "--pillar-title-size": `${appearance.title_size}px`,
        "--pillar-title-align": appearance.title_align,
        "--pillar-text-color": color(appearance.text_color, "var(--site-ink-soft)"),
        "--pillar-text-size": `${appearance.text_size}px`,
        "--pillar-text-align": appearance.text_align,
        "--pillar-text-height": appearance.text_line_height / 100,
    } as CSSProperties;
}

function PillarCard({ item, index }: { item: WebsiteSectionItem; index: number }): React.JSX.Element | null {
    const appearance = item.appearance;
    if (!appearance) return null;

    return (
        <li
            className={`siteSections__feature sitePillars__card sitePillars__card--${appearance.icon_position}${appearance.hover_enabled ? " sitePillars__card--hover" : ""}`}
            style={cardStyle(appearance)}
            data-pillar-index={index}
            data-has-icon={!!item.icon}
        >
            {item.icon ? (
                <span className="siteSections__featureIcon sitePillars__icon" aria-hidden="true">
                    <i className={item.icon} />
                </span>
            ) : null}
            <div className="sitePillars__cardText">
                {item.title ? <h3>{item.title}</h3> : null}
                {item.text ? <p>{item.text}</p> : null}
            </div>
        </li>
    );
}

export default function SitePillars({ section }: { section: WebsiteSection }): React.JSX.Element | null {
    const appearance = section.pillars;
    if (!appearance) return null;
    const background = color(
        appearance.background_color,
        section.type === "pillars" ? "var(--site-surface)" : "#ffffff",
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
            className={`sitePillars sitePillars--${appearance.layout}${appearance.title_divider ? "" : " sitePillars--noDivider"}`}
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
                    {section.items.map((item, index) => <PillarCard key={index} item={item} index={index} />)}
                </ul>
            </div>
        </section>
    );
}
