import SiteLink, { useSitePreview } from "../SiteLayout/SiteLink";
import type { CSSProperties } from "react";
import { appearanceColor as color } from "../WebsiteAppearance";
import type { PillarCardAppearance, WebsiteSectionItem } from "../Website.types";
import "./SiteItemCard.css";

export function itemCardStyle(appearance: PillarCardAppearance): CSSProperties {
    const background = color(appearance.card_background, "var(--item-card-default, #ffffff)");
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
        "--pillar-icon-background": color(appearance.icon_background, "var(--item-marker-default, var(--site-primary-soft))"),
        "--pillar-icon-size": `${appearance.icon_size}px`,
        "--pillar-icon-width": `${appearance.icon_width}px`,
        "--pillar-icon-height": `${appearance.icon_height}px`,
        "--pillar-icon-radius": `${appearance.icon_radius}px`,
        "--pillar-icon-gap": `${appearance.icon_gap}px`,
        "--pillar-icon-align": appearance.icon_align === "left" ? "start" : appearance.icon_align === "right" ? "end" : "center",
        "--pillar-title-color": color(appearance.title_color, "var(--item-title-default, var(--site-ink))"),
        "--pillar-title-size": `${appearance.title_size}px`,
        "--pillar-title-align": appearance.title_align,
        "--pillar-text-color": color(appearance.text_color, "var(--site-ink-soft)"),
        "--pillar-text-size": `${appearance.text_size}px`,
        "--pillar-text-align": appearance.text_align,
        "--pillar-text-height": appearance.text_line_height / 100,
    } as CSSProperties;
}

type Props = {
    item: WebsiteSectionItem;
    index: number;
    numbered?: boolean;
    faq?: boolean;
    resource?: boolean;
    hideMarker?: boolean;
    as?: "li" | "div";
};

export default function SiteItemCard({
    item, index, faq = false, resource = false, numbered = false, hideMarker = false, as: Tag = "li",
}: Props): React.JSX.Element | null {
    const preview = useSitePreview();
    const appearance = item.appearance;
    if (!appearance) return null;

    return (
        <Tag
            className={`siteSections__feature siteItemCard__card siteItemCard__card--${appearance.icon_position}${appearance.hover_enabled ? " siteItemCard__card--hover" : ""}`}
            style={itemCardStyle(appearance)}
            data-item-index={index}
            data-has-icon={!faq && !hideMarker && (numbered || !!item.icon)}
        >
            {!faq && !hideMarker && (numbered || item.icon) ? (
                <span className="siteSections__featureIcon siteItemCard__icon" aria-hidden="true">
                    {numbered ? index + 1 : <i className={item.icon} />}
                </span>
            ) : null}
            {faq ? (
                <details className="siteSections__question" open={preview || undefined}>
                    <summary>{item.title}</summary>
                    {item.text ? <p>{item.text}</p> : null}
                </details>
            ) : (
                <div className="siteItemCard__cardText">
                    {item.title ? <h3>{item.title}</h3> : null}
                    {item.text ? <p>{item.text}</p> : null}
                    {resource && item.url ? (
                        <SiteLink
                            href={item.url}
                            className="siteSections__more siteSections__resourceLink"
                            ariaLabel={`Consultar: ${item.title}`}
                        >
                            Consultar material <i className="fa fa-arrow-right" aria-hidden="true" />
                        </SiteLink>
                    ) : null}
                </div>
            )}
        </Tag>
    );
}
