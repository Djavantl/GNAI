import type { CSSProperties } from "react";
import BootstrapCarousel from "@/components/UI/BootstrapCarousel/BootstrapCarousel";
import SiteLink from "../SiteLayout/SiteLink";
import type { HeroAlignment, HeroSlide, WebsiteSection, WebsiteSite } from "../Website.types";
import "./SiteHero.css";
import { appearanceColor as color } from "../WebsiteAppearance";

function align(value: HeroAlignment): CSSProperties {
    return {
        textAlign: value,
        marginLeft: value === "left" ? 0 : "auto",
        marginRight: value === "right" ? 0 : "auto",
    };
}

export default function SiteHero({
    section,
    site,
    isMainHeading,
}: {
    section: WebsiteSection;
    site: WebsiteSite;
    isMainHeading: boolean;
}): React.JSX.Element {
    const appearance = section.hero;
    const Heading = isMainHeading ? "h1" : "h2";
    // O presenter também preenche a aparência dos banners salvos antes desta configuração.
    if (!appearance) return <Heading>{section.title || site.name}</Heading>;

    const banners = appearance.mode === "banners";
    const images: HeroSlide[] = (section.slides ?? []).filter((slide) => slide.image_url);
    if (!images.length && section.image_url) {
        images.push({ id: "background", image_url: section.image_url, alt: section.image_alt, url: "" });
    }

    const overlayColor = color(appearance.overlay_color, "var(--site-primary-strong)");
    const start = `color-mix(in srgb, ${overlayColor} ${appearance.overlay_opacity}%, transparent)`;
    const end = `color-mix(in srgb, ${color(appearance.gradient_end_color, overlayColor)} ${appearance.gradient_end_opacity}%, transparent)`;
    const style = {
        "--hero-height": `${appearance.height}px`,
        "--hero-title-size": `${appearance.title_size}px`,
        "--hero-subtitle-size": `${appearance.subtitle_size}px`,
        "--hero-content-width": `${appearance.content_width}px`,
        "--hero-image-fit": appearance.image_fit,
        "--hero-image-position": appearance.image_position,
        backgroundColor: color(appearance.background_color, "var(--site-primary)"),
    } as CSSProperties;

    const overlayStyle: CSSProperties = {
        background: appearance.gradient_enabled
            ? `linear-gradient(${appearance.gradient_direction}, ${start}, ${end})`
            : start,
    };

    function image(slide: HeroSlide): React.JSX.Element {
        const picture = (
            <img
                src={slide.image_url ?? ""}
                alt={banners ? slide.alt : ""}
                className="siteHero__image"
                data-hero-slide-id={slide.id}
                draggable={false}
            />
        );

        if (!banners) {
            return (
                <>
                    {picture}
                    <div className="siteHero__overlay" aria-hidden="true" style={overlayStyle} />
                </>
            );
        }

        return slide.url ? (
            <SiteLink href={slide.url} className="siteHero__bannerLink" ariaLabel={slide.alt}>
                {picture}
            </SiteLink>
        ) : picture;
    }

    return (
        <section className={`siteHero ${banners ? "siteHero--banners" : "siteHero--text"}`} style={style} aria-label={site.name}>
            <div className="siteHero__media">
                {images.length > 1 ? (
                    <BootstrapCarousel
                        ariaLabel={banners ? "Banners do núcleo" : "Imagens de apresentação"}
                        slides={images.map((slide) => ({ src: slide.image_url ?? "", alt: slide.alt }))}
                        interval={appearance.interval * 1000}
                        autoplay={appearance.autoplay}
                        showPause
                        controls
                        renderSlide={(_, index) => image(images[index])}
                    />
                ) : images[0] ? image(images[0]) : null}
            </div>
            {!banners ? (
                <>
                    {!images.length ? (
                        <div className="siteHero__overlay" aria-hidden="true" style={overlayStyle} />
                    ) : null}
                    <div className="siteLayout__container siteHero__content">
                        {appearance.badge_visible ? (
                            <div className="siteHero__row" style={{ textAlign: appearance.badge_align }}>
                                <span
                                    className="siteHero__badge"
                                    style={{
                                        borderRadius: appearance.badge_radius,
                                        color: color(appearance.badge_color, "#ffffff"),
                                        backgroundColor: color(appearance.badge_background, "rgba(255, 255, 255, 0.1)"),
                                    }}
                                >
                                    {appearance.badge_text || site.name}
                                </span>
                            </div>
                        ) : null}
                        <Heading
                            className="siteHero__title"
                            style={{ ...align(appearance.title_align), color: color(appearance.title_color, "#ffffff") }}
                        >
                            {section.title || site.name}
                        </Heading>
                        {section.subtitle ? (
                            <p
                                className="siteHero__text"
                                style={{ ...align(appearance.subtitle_align), color: color(appearance.subtitle_color, "#ffffff") }}
                            >
                                {section.subtitle}
                            </p>
                        ) : null}
                        {section.button_label && section.button_url ? (
                            <div className="siteHero__row" style={{ textAlign: appearance.button_align }}>
                                <SiteLink href={section.button_url} className="siteHero__button">
                                    <span
                                        style={{
                                            color: color(appearance.button_color, "var(--site-primary)"),
                                            backgroundColor: color(appearance.button_background, "#ffffff"),
                                            borderRadius: appearance.button_radius,
                                            boxShadow: appearance.button_shadow
                                                ? `0 ${appearance.button_shadow / 3}px ${appearance.button_shadow}px rgba(0, 0, 0, 0.3)` : "none",
                                        }}
                                    >
                                        {section.button_label}
                                    </span>
                                </SiteLink>
                            </div>
                        ) : null}
                    </div>
                </>
            ) : (
                <Heading className="siteSections__srOnly">{section.title || site.name}</Heading>
            )}
        </section>
    );
}
