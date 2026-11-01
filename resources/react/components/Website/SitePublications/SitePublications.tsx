import type { CSSProperties } from "react";
import BootstrapCarousel from "@/components/UI/BootstrapCarousel/BootstrapCarousel";
import PublicationCard from "../PublicationCard/PublicationCard";
import SiteLink from "../SiteLayout/SiteLink";
import { appearanceColor as color } from "../WebsiteAppearance";
import { websiteRoutes, type WebsiteSection, type WebsiteContent } from "../Website.types";
import "../SiteGuidance/SiteGuidance.css";
import "./SitePublications.css";

export default function SitePublications({ section, content }: { section: WebsiteSection; content: WebsiteContent }): React.JSX.Element | null {
    const appearance = section.publications;
    if (!appearance) return null;

    const publications = content.publications.slice(0, section.limit);
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
        "--guidance-button-color": buttonColor,
        "--guidance-button-background": buttonBackground,
        "--guidance-button-radius": `${appearance.button_radius}px`,
        "--guidance-button-size": `${appearance.button_size}px`,
        "--guidance-button-shadow": shadow(appearance.button_shadow),
        "--guidance-hover-color": color(appearance.hover_color, buttonColor),
        "--guidance-hover-background": color(appearance.hover_background, buttonBackground),
        "--guidance-hover-shadow": shadow(appearance.hover_shadow),
        "--guidance-hover-lift": `-${appearance.hover_lift}px`,
        "--publication-badge-radius": `${appearance.badge_radius}px`,
        "--publication-card-radius": `${appearance.card_radius}px`,
        "--publication-card-padding": `${appearance.card_padding}px`,
        "--publication-card-title-size": `${appearance.card_title_size}px`,
        "--publication-card-text-size": `${appearance.card_text_size}px`,
        "--publication-card-image-height": `${appearance.image_height}px`,
        "--publication-card-background": color(appearance.card_background, "#ffffff"),
        "--publication-card-title-color": color(appearance.card_title_color, "var(--site-ink)"),
        "--publication-card-text-color": color(appearance.card_text_color, "var(--site-ink-soft)"),
        "--publication-card-border": color(appearance.card_border_color, "var(--site-border)"),
        background: appearance.gradient_enabled
            ? `linear-gradient(${appearance.gradient_direction}, ${background}, ${color(appearance.gradient_end_color, "var(--site-primary-soft)")})`
            : background,
    } as CSSProperties;

    return (
        <section
            className={`sitePublications siteGuidance siteGuidance--${appearance.layout}${appearance.title_divider ? "" : " siteGuidance--noDivider"}`}
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
                {!publications.length ? (
                    <p className="siteSections__empty">Nenhuma publicação disponível no momento.</p>
                ) : appearance.layout === "carousel" ? (
                    <div className="siteGuidance__carousel">
                        <BootstrapCarousel
                            ariaLabel={section.title || "Últimas publicações"}
                            itemLabel="publicação"
                            controls
                            showPause
                            autoplay={appearance.autoplay}
                            interval={appearance.interval * 1000}
                            slides={publications.map((publication) => ({ src: `publication-${publication.id}`, alt: publication.title }))}
                            renderSlide={(_, index) => (
                                <PublicationCard publication={publications[index]} appearance={appearance} />
                            )}
                        />
                    </div>
                ) : (
                    <div className="sitePublications__grid">
                        {publications.map((publication) => (
                            <PublicationCard key={publication.id} publication={publication} appearance={appearance} />
                        ))}
                    </div>
                )}
                <div className="siteGuidance__actionRow" style={{ textAlign: appearance.button_align }}>
                    <SiteLink
                        href={section.button_url || websiteRoutes.publications}
                        className={`siteSections__guidanceLink siteGuidance__action siteGuidance__action--${appearance.action_style}${appearance.hover_enabled ? " siteGuidance__action--hover" : ""}`}
                    >
                        {section.button_label || "Ver todas as publicações"}
                        {appearance.button_icon ? <i className="fa fa-arrow-right" aria-hidden="true" /> : null}
                    </SiteLink>
                </div>
            </div>
        </section>
    );
}
