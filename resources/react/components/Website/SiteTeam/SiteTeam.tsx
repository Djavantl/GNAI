import type { CSSProperties } from "react";
import SiteCollection from "../SiteCollection/SiteCollection";
import TeamMemberCard from "../TeamMemberCard/TeamMemberCard";
import { appearanceColor as color } from "../WebsiteAppearance";
import { websiteRoutes, type WebsiteSection, type WebsiteContent } from "../Website.types";
import "./SiteTeam.css";

export default function SiteTeam({ section, content }: { section: WebsiteSection; content: WebsiteContent }): React.JSX.Element | null {
    const appearance = section.team;
    if (!appearance) return null;

    const style = {
        "--team-photo-size": `${appearance.photo_size}px`,
        "--team-photo-radius": `${appearance.photo_radius}px`,
        "--team-photo-border-width": `${appearance.photo_border_width}px`,
        "--team-photo-border": color(appearance.photo_border_color, "var(--site-primary-soft)"),
        "--team-card-radius": `${appearance.card_radius}px`,
        "--team-card-padding": `${appearance.card_padding}px`,
        "--team-name-size": `${appearance.card_title_size}px`,
        "--team-role-size": `${appearance.card_text_size}px`,
        "--team-card-background": color(appearance.card_background, "#ffffff"),
        "--team-name-color": color(appearance.card_title_color, "var(--site-ink)"),
        "--team-role-color": color(appearance.card_text_color, "var(--site-primary)"),
        "--team-card-border": color(appearance.card_border_color, "var(--site-border)"),
        "--team-card-align": appearance.card_align,
        "--team-photo-align": appearance.card_align === "left" ? "flex-start" : appearance.card_align === "right" ? "flex-end" : "center",
    } as CSSProperties;

    return (
        <SiteCollection
            section={section}
            appearance={appearance}
            className="siteTeam"
            style={style}
            actionLabel="Ver equipe completa"
            actionUrl={websiteRoutes.team}
            emptyLabel="Em breve apresentaremos nossa equipe."
            fallbackTitle="Equipe do núcleo"
            itemLabel="profissional"
            cards={content.team.slice(0, section.limit).map((member) => ({
                id: member.id,
                label: member.name,
                content: <TeamMemberCard member={member} appearance={appearance} />,
            }))}
        />
    );
}
