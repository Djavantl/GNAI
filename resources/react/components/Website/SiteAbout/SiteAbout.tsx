import type { CSSProperties } from "react";
import RichTextContent from "@/components/UI/RichTextContent/RichTextContent";
import SiteLink from "../SiteLayout/SiteLink";
import type { WebsiteSection } from "../Website.types";
import { appearanceColor as color } from "../WebsiteAppearance";
import "./SiteAbout.css";

export default function SiteAbout({ section }: { section: WebsiteSection }): React.JSX.Element | null {
    const appearance = section.about;
    if (!appearance) return null;

    const hasImage = appearance.image_visible && !!section.image_url;
    const background = color(appearance.background_color, "#ffffff");
    const style = {
        "--about-gap": `${appearance.gap}px`,
        "--about-padding": `${appearance.padding_vertical}px`,
        "--about-title-size": `${appearance.title_size}px`,
        "--about-subtitle-size": `${appearance.subtitle_size}px`,
        "--about-body-size": `${appearance.body_size}px`,
        "--about-body-color": color(appearance.body_color, "var(--site-ink-soft)"),
        "--about-body-align": appearance.body_align,
        "--about-body-line-height": appearance.body_line_height / 100,
        "--about-image-height": `${appearance.image_height}px`,
        "--about-image-column": `${appearance.image_width}fr`,
        "--about-text-column": `${100 - appearance.image_width}fr`,
        "--about-divider-color": color(appearance.divider_color, "var(--site-primary)"),
        background: appearance.gradient_enabled
            ? `linear-gradient(${appearance.gradient_direction}, ${background}, ${color(appearance.gradient_end_color, "var(--site-primary-soft)")})`
            : background,
    } as CSSProperties;

    return (
        <section
            className={`siteAbout siteAbout--${appearance.image_side}${hasImage ? " siteAbout--withImage" : ""}${appearance.title_divider ? "" : " siteAbout--noDivider"}`}
            style={style}
        >
            <div className="siteLayout__container siteSections__about siteAbout__grid">
                <div className="siteAbout__main">
                    {section.title ? (
                        <h2
                            className="siteSections__title siteAbout__title"
                            data-title-align={appearance.title_align}
                            style={{ color: color(appearance.title_color, "var(--site-primary)"), textAlign: appearance.title_align }}
                        >
                            {section.title}
                        </h2>
                    ) : null}
                    {section.subtitle ? (
                        <p
                            className="siteSections__subtitle siteAbout__subtitle"
                            style={{ color: color(appearance.subtitle_color, "var(--site-ink-muted)"), textAlign: appearance.subtitle_align }}
                        >
                            {section.subtitle}
                        </p>
                    ) : null}
                    {section.body ? (
                        <RichTextContent
                            html={section.body}
                            className="siteSections__richText siteAbout__body"
                        />
                    ) : null}
                    {section.button_label && section.button_url ? (
                        <div style={{ textAlign: appearance.button_align }}>
                            <SiteLink
                                href={section.button_url}
                                className="siteSections__button"
                                ariaLabel={section.button_label}
                            >
                                <span
                                    className="siteAbout__button"
                                    style={{
                                        backgroundColor: color(appearance.button_background, "var(--site-primary)"),
                                        color: color(appearance.button_color, "#ffffff"),
                                        borderRadius: appearance.button_radius,
                                        boxShadow: appearance.button_shadow
                                            ? `0 ${appearance.button_shadow / 3}px ${appearance.button_shadow}px rgba(0,0,0,0.3)` : "none",
                                    }}
                                >
                                    {section.button_label}
                                </span>
                            </SiteLink>
                        </div>
                    ) : null}
                </div>
                {hasImage ? (
                    <img
                        src={section.image_url ?? ""}
                        alt={section.image_alt}
                        className="siteSections__aboutImage siteAbout__image"
                        loading="lazy"
                        style={{
                            borderRadius: appearance.image_radius,
                            objectFit: appearance.image_fit,
                            objectPosition: appearance.image_position,
                            boxShadow: appearance.image_shadow
                                ? `0 ${appearance.image_shadow / 3}px ${appearance.image_shadow}px rgba(0,0,0,0.15)` : "none",
                        }}
                    />
                ) : null}
            </div>
        </section>
    );
}
