import type { CSSProperties } from "react";
import BootstrapCarousel from "@/components/UI/BootstrapCarousel/BootstrapCarousel";
import SiteItemCard, { itemCardStyle } from "../SiteItemCard/SiteItemCard";
import SiteLink from "../SiteLayout/SiteLink";
import { appearanceColor as color } from "../WebsiteAppearance";
import type { WebsiteSection } from "../Website.types";
import "./SiteGuidance.css";

export default function SiteGuidance({ section }: { section: WebsiteSection }): React.JSX.Element | null {
    const appearance = section.guidance;
    if (!appearance) return null;

    const background = color(appearance.background_color, "var(--site-surface)");
    const buttonBackground = color(appearance.button_background, appearance.action_style === "button" ? "var(--site-primary)" : "transparent");
    const buttonColor = color(appearance.button_color, appearance.action_style === "button" ? "#ffffff" : "var(--site-primary)");
    const shadow = (value: number) => value ? `0 ${value / 3}px ${value}px rgba(0,0,0,.15)` : "none";
    const style = {
        "--guidance-gap": `${appearance.gap}px`,
        "--guidance-columns": appearance.columns,
        "--guidance-padding": `${appearance.padding_vertical}px`,
        "--guidance-title-size": `${appearance.title_size}px`,
        "--guidance-subtitle-size": `${appearance.subtitle_size}px`,
        "--guidance-divider": color(appearance.divider_color, "var(--site-primary)"),
        "--guidance-timeline": color(appearance.timeline_color, "var(--site-border)"),
        "--guidance-marker-column": `${Math.max(44, ...section.items.map((item) => item.appearance?.icon_width ?? 44))}px`,
        "--guidance-button-color": buttonColor,
        "--guidance-button-background": buttonBackground,
        "--guidance-button-radius": `${appearance.button_radius}px`,
        "--guidance-button-size": `${appearance.button_size}px`,
        "--guidance-button-shadow": shadow(appearance.button_shadow),
        "--guidance-hover-color": color(appearance.hover_color, buttonColor),
        "--guidance-hover-background": color(appearance.hover_background, buttonBackground),
        "--guidance-hover-shadow": shadow(appearance.hover_shadow),
        "--guidance-hover-lift": `-${appearance.hover_lift}px`,
        background: appearance.gradient_enabled
            ? `linear-gradient(${appearance.gradient_direction}, ${background}, ${color(appearance.gradient_end_color, "var(--site-primary-soft)")})`
            : background,
    } as CSSProperties;

    return (
        <section
            className={`siteGuidance siteGuidance--${appearance.layout}${appearance.title_divider ? "" : " siteGuidance--noDivider"}`}
            style={style}
            data-columns={appearance.columns}
        >
            <div className="siteLayout__container">
                {section.title ? (
                    <h2
                        className="siteSections__title siteGuidance__title"
                        data-title-align={appearance.title_align}
                        style={{ textAlign: appearance.title_align, color: color(appearance.title_color, "var(--site-primary)") }}
                    >
                        {section.title}
                    </h2>
                ) : null}
                {section.subtitle ? (
                    <p
                        className="siteSections__subtitle siteGuidance__subtitle"
                        style={{ textAlign: appearance.subtitle_align, color: color(appearance.subtitle_color, "var(--site-ink-muted)") }}
                    >
                        {section.subtitle}
                    </p>
                ) : null}
                {appearance.layout === "carousel" ? (
                    <div className="siteGuidance__carousel">
                        <BootstrapCarousel
                            ariaLabel={section.title || "Como buscar apoio"}
                            itemLabel="etapa"
                            controls
                            showPause
                            autoplay={appearance.autoplay}
                            interval={appearance.interval * 1000}
                            slides={section.items.map((item, index) => ({ src: `step-${index}`, alt: item.title }))}
                            renderSlide={(_, index) => (
                                <SiteItemCard item={section.items[index]} index={index} numbered as="div" />
                            )}
                        />
                    </div>
                ) : (
                    <ol className="siteGuidance__steps" role="list">
                        {section.items.map((item, index) => {
                            if (appearance.layout === "timeline" && item.appearance) {
                                return (
                                    <li
                                        key={index}
                                        className="siteGuidance__timelineRow"
                                        data-item-index={index}
                                        style={itemCardStyle(item.appearance)}
                                    >
                                        <span className="siteSections__featureIcon siteItemCard__icon" aria-hidden="true">
                                            {index + 1}
                                        </span>
                                        <SiteItemCard item={item} index={index} hideMarker as="div" />
                                    </li>
                                );
                            }

                            return <SiteItemCard key={index} item={item} index={index} numbered />;
                        })}
                    </ol>
                )}
                {section.button_label && section.button_url ? (
                    <div className="siteGuidance__actionRow" style={{ textAlign: appearance.button_align }}>
                        <SiteLink
                            href={section.button_url}
                            className={`siteSections__guidanceLink siteGuidance__action siteGuidance__action--${appearance.action_style}${appearance.hover_enabled ? " siteGuidance__action--hover" : ""}`}
                        >
                            {section.button_label}
                            {appearance.button_icon ? <i className="fa fa-arrow-right" aria-hidden="true" /> : null}
                        </SiteLink>
                    </div>
                ) : null}
            </div>
        </section>
    );
}
