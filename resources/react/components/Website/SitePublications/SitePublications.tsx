import type { CSSProperties } from "react";
import PublicationCard from "../PublicationCard/PublicationCard";
import SiteCollection from "../SiteCollection/SiteCollection";
import { appearanceColor as color } from "../WebsiteAppearance";
import { websiteRoutes, type WebsiteSection, type WebsiteContent } from "../Website.types";
import "./SitePublications.css";

export default function SitePublications({ section, content }: { section: WebsiteSection; content: WebsiteContent }): React.JSX.Element | null {
    const events = section.type === "events";
    const appearance = events ? section.events : section.publications;
    if (!appearance) return null;
    const publications = (events ? content.events : content.publications).slice(0, section.limit);
    const style = {
        "--event-date-width": `${appearance.date_badge_width}px`,
        "--event-date-radius": `${appearance.date_badge_radius}px`,
        "--event-date-color": color(appearance.date_badge_color, "#ffffff"),
        "--event-date-background": color(appearance.date_badge_background, "var(--site-primary)"),
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
    } as CSSProperties;

    return (
        <SiteCollection
            section={section}
            appearance={appearance}
            className="sitePublications"
            style={style}
            defaultBackground={events ? "#ffffff" : "var(--site-surface)"}
            actionLabel={events ? "Ver todos os eventos" : "Ver todas as publicações"}
            actionUrl={events ? `${websiteRoutes.publications}?type=event` : websiteRoutes.publications}
            emptyLabel={events ? "Nenhum evento programado no momento." : "Nenhuma publicação disponível no momento."}
            fallbackTitle={events ? "Atividades e encontros" : "Últimas publicações"}
            itemLabel={events ? "evento" : "publicação"}
            cards={publications.map((publication) => ({
                id: publication.id,
                label: publication.title,
                content: (
                    <PublicationCard
                        publication={publication}
                        appearance={appearance}
                        variant={events ? "event" : "default"}
                    />
                ),
            }))}
        />
    );
}
