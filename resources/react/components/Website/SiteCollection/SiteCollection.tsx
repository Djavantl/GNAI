import type { CSSProperties, ReactNode } from "react";
import BootstrapCarousel from "@/components/UI/BootstrapCarousel/BootstrapCarousel";
import SiteLink from "../SiteLayout/SiteLink";
import { appearanceColor as color } from "../WebsiteAppearance";
import type { GuidanceAppearance, WebsiteSection } from "../Website.types";
import "../SiteGuidance/SiteGuidance.css";
import "./SiteCollection.css";

type Props = {
    section: WebsiteSection;
    appearance: GuidanceAppearance;
    cards: { id: number; label: string; content: ReactNode }[];
    className: string;
    style?: CSSProperties;
    defaultBackground?: string;
    actionLabel?: string;
    actionUrl?: string;
    emptyLabel: string;
    itemLabel: string;
    fallbackTitle: string;
    children?: ReactNode;
};

/** Estrutura compartilhada das seções com conteúdo real e aparência configurável. */
export default function SiteCollection({
    section, appearance, cards, className, style: cardStyle, defaultBackground = "var(--site-surface)",
    actionLabel, actionUrl, emptyLabel, itemLabel, fallbackTitle, children,
}: Props): React.JSX.Element {
    const resolvedActionLabel = section.button_label || actionLabel;
    const resolvedActionUrl = section.button_url || actionUrl;
    const background = color(appearance.background_color, defaultBackground);
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
        background: appearance.gradient_enabled
            ? `linear-gradient(${appearance.gradient_direction}, ${background}, ${color(appearance.gradient_end_color, "var(--site-primary-soft)")})`
            : background,
        ...cardStyle,
    } as CSSProperties;

    return (
        <section
            className={`siteCollection ${className} siteGuidance siteGuidance--${appearance.layout}${appearance.title_divider ? "" : " siteGuidance--noDivider"}`}
            style={style}
            data-columns={appearance.columns}
            data-feed-type={section.type}
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
                {!cards.length ? (
                    <p className="siteSections__empty">{emptyLabel}</p>
                ) : appearance.layout === "carousel" ? (
                    <div className="siteGuidance__carousel">
                        <BootstrapCarousel
                            ariaLabel={section.title || fallbackTitle}
                            itemLabel={itemLabel}
                            controls
                            showPause
                            autoplay={appearance.autoplay}
                            interval={appearance.interval * 1000}
                            slides={cards.map((card) => ({ src: String(card.id), alt: card.label }))}
                            renderSlide={(_, index) => cards[index].content}
                        />
                    </div>
                ) : (
                    <div className="siteCollection__grid">
                        {cards.map((card) => (
                            <div key={card.id} className="siteCollection__item">
                                {card.content}
                            </div>
                        ))}
                    </div>
                )}
                {children}
                {resolvedActionLabel && resolvedActionUrl ? (
                    <div className="siteGuidance__actionRow" style={{ textAlign: appearance.button_align }}>
                        <SiteLink
                            href={resolvedActionUrl}
                            className={`siteSections__guidanceLink siteGuidance__action siteGuidance__action--${appearance.action_style}${appearance.hover_enabled ? " siteGuidance__action--hover" : ""}`}
                        >
                            {resolvedActionLabel}
                            {appearance.button_icon ? <i className="fa fa-arrow-right" aria-hidden="true" /> : null}
                        </SiteLink>
                    </div>
                ) : null}
            </div>
        </section>
    );
}
